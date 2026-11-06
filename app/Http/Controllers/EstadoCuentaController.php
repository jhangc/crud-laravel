<?php

namespace App\Http\Controllers;

use App\Models\Credito;
use App\Services\EstadoCuentaCrediJoyaService;
use App\Services\EstadoCuentaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EstadoCuentaController extends Controller
{
    /** Estados de crédito que ya tienen cronograma y pagos (desembolsados). */
    private const ESTADOS_CON_CUENTA = ['pagado', 'mora', 'terminado'];

    /**
     * Reportes > Estado de Cuenta: buscador de créditos para imprimir su estado.
     * Administrador ve todos; el resto, solo los créditos que registró.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $filtros = $request->only(['estado', 'categoria', 'mes', 'desde', 'hasta']);
        // Aquí solo tienen sentido los estados con cronograma y pagos.
        if (!empty($filtros['estado']) && !in_array($filtros['estado'], self::ESTADOS_CON_CUENTA, true)) {
            unset($filtros['estado']);
        }

        $base = Credito::query()
            ->whereIn('estado', self::ESTADOS_CON_CUENTA)
            // CrediJoya: solo el crédito más reciente de cada cadena de renovaciones
            // (su estado de cuenta ya incluye los anteriores).
            ->whereNotExists(function ($s) {
                $s->selectRaw('1')
                    ->from('prestamos as siguiente')
                    ->whereColumn('siguiente.deuda_prev_modo', 'prestamos.id')
                    ->where('siguiente.categoria', 'credijoya');
            })
            ->when(!$user->hasRole('Administrador'), fn ($q) => $q->where('user_id', $user->id));

        $creditos = (clone $base)
            ->with(['creditoClientes.clientes', 'user'])
            ->filtrar($filtros)
            ->recientesPorDesembolso()
            ->get();

        return view('admin.reportes.estadocuenta', [
            'creditos'   => $creditos,
            'filtros'    => $filtros,
            'estados'    => array_intersect_key(Credito::ESTADOS, array_flip(self::ESTADOS_CON_CUENTA)),
            'categorias' => (clone $base)->whereNotNull('categoria')->distinct()->orderBy('categoria')->pluck('categoria'),
        ]);
    }

    /**
     * Estado de cuenta en pantalla (mismos datos que el PDF).
     *   ?cliente={id} → hoja de ese integrante (crédito grupal).
     * En grupal, sin ?cliente se muestra el consolidado del grupo.
     */
    public function ver(Request $request, $id, EstadoCuentaService $servicio)
    {
        $credito = Credito::with('creditoClientes.clientes')->findOrFail($id);

        if (EstadoCuentaCrediJoyaService::esCrediJoya($credito)) {
            return view('admin.creditos.estadocuenta_credijoya', [
                'credito' => $credito,
                'e'       => app(EstadoCuentaCrediJoyaService::class)->generar($credito),
                'hoy'     => now(),
            ]);
        }

        $clienteId = $request->filled('cliente') ? (int) $request->query('cliente') : null;

        // En pantalla se ve una hoja a la vez: grupo o un integrante.
        $estado = $servicio->generar($credito, $clienteId, false)[0];

        $integrantes = $credito->creditoClientes->filter(fn ($cc) => $cc->clientes)->unique('cliente_id')->values();

        return view('admin.creditos.estadocuenta', [
            'credito'     => $credito,
            'e'           => $estado,
            'clienteId'   => $credito->categoria === 'grupal' ? $clienteId : null,
            'integrantes' => $integrantes,
            'hoy'         => now(),
        ]);
    }

    /**
     * PDF del estado de cuenta.
     *   ?cliente={id}     → solo ese integrante (crédito grupal).
     *   ?integrantes=0    → en grupal, solo la hoja consolidada del grupo.
     */
    public function pdf(Request $request, $id, EstadoCuentaService $servicio)
    {
        $credito = Credito::findOrFail($id);

        $logo = null;
        $logoPath = public_path('logo.png');
        if (is_file($logoPath)) {
            $logo = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        if (EstadoCuentaCrediJoyaService::esCrediJoya($credito)) {
            return Pdf::loadView('pdf.estadocuenta_credijoya', [
                'credito' => $credito,
                'e'       => app(EstadoCuentaCrediJoyaService::class)->generar($credito),
                'logo'    => $logo,
                'emitido' => now(),
                'usuario' => optional(Auth::user())->name,
            ])->setPaper('a4', 'landscape')->stream('estado-cuenta-credijoya-' . $credito->id . '.pdf');
        }

        $estados = $servicio->generar(
            $credito,
            $request->filled('cliente') ? (int) $request->query('cliente') : null,
            $request->query('integrantes', '1') !== '0'
        );

        $pdf = Pdf::loadView('pdf.estadocuenta', [
            'credito'  => $credito,
            'estados'  => $estados,
            'logo'     => $logo,
            'emitido'  => now(),
            'usuario'  => optional(Auth::user())->name,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('estado-cuenta-' . $credito->id . '.pdf');
    }
}
