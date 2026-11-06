<?php

namespace App\Http\Controllers;

use App\Models\Credito;
use App\Models\Sucursal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Reportes > Operaciones por asesor.
 *
 * Cuenta las operaciones DESEMBOLSADAS (estado activo, mora o terminado) por la
 * fecha de desembolso del crédito, agrupadas por el asesor que registró el
 * crédito (prestamos.user_id), con el total de la agencia.
 */
class OperacionesAsesorController extends Controller
{
    private const ESTADOS_DESEMBOLSADOS = ['pagado', 'mora', 'terminado'];

    public function index(Request $request)
    {
        $user    = Auth::user();
        $esAdmin = $user->hasRole('Administrador');

        // Periodo: rango desde/hasta si viene completo; si no, el mes (por defecto el actual).
        $esFecha = function ($v) {
            if (!is_string($v) || !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $v, $m)) {
                return false;
            }
            return checkdate((int) $m[2], (int) $m[3], (int) $m[1]);
        };
        if ($esFecha($request->query('desde')) && $esFecha($request->query('hasta'))) {
            $desde = Carbon::parse($request->query('desde'))->startOfDay();
            $hasta = Carbon::parse($request->query('hasta'))->startOfDay();
            if ($desde->greaterThan($hasta)) {
                [$desde, $hasta] = [$hasta, $desde];
            }
            $mes = null;
        } else {
            $mes = preg_match('/^\d{4}-\d{2}$/', (string) $request->query('mes'))
                ? $request->query('mes')
                : now()->format('Y-m');
            $desde = Carbon::createFromFormat('Y-m-d', $mes . '-01')->startOfDay();
            $hasta = $desde->copy()->endOfMonth()->startOfDay();
        }

        $incluirCJ = $request->query('credijoya', '1') !== '0';

        $creditos = Credito::with(['user', 'clientes'])
            ->whereIn('estado', self::ESTADOS_DESEMBOLSADOS)
            ->whereDate('fecha_desembolso', '>=', $desde->toDateString())
            ->whereDate('fecha_desembolso', '<=', $hasta->toDateString())
            // El asesor solo ve sus operaciones; el administrador, toda la agencia.
            ->when(!$esAdmin, fn ($q) => $q->where('user_id', $user->id))
            ->when(!$incluirCJ, function ($q) {
                $q->where(fn ($w) => $w->whereNull('categoria')->orWhere('categoria', '!=', 'credijoya'))
                  ->where(fn ($w) => $w->whereNull('subproducto')->orWhere('subproducto', '!=', 'credijoya'));
            })
            ->orderByDesc('fecha_desembolso')
            ->orderByDesc('id')
            ->get();

        $vacio = ['operaciones' => 0, 'grupal' => 0, 'individual' => 0, 'credijoya' => 0, 'monto' => 0.0];

        $asesores = [];
        $total    = $vacio;

        foreach ($creditos as $credito) {
            $tipo = $this->tipo($credito);
            $credito->tipo_operacion = $tipo;

            $id = $credito->user_id ?? 0;
            if (!isset($asesores[$id])) {
                $asesores[$id] = $vacio + ['nombre' => optional($credito->user)->name ?? 'Sin asesor', 'user_id' => $id];
            }

            $asesores[$id] = $this->sumar($asesores[$id], $tipo, (float) $credito->monto_total);
            $total         = $this->sumar($total, $tipo, (float) $credito->monto_total);
        }

        // Asesores con más monto primero.
        uasort($asesores, fn ($a, $b) => $b['monto'] <=> $a['monto']);

        $sucursales = Sucursal::orderBy('id')->pluck('nombre');

        return view('admin.reportes.operacionesasesor', [
            'asesores'      => array_values($asesores),
            'total'         => $total,
            'creditos'      => $creditos,
            'desde'         => $desde,
            'hasta'         => $hasta,
            'mes'           => $mes,
            'incluirCJ'     => $incluirCJ,
            'esAdmin'       => $esAdmin,
            'agencia'       => $sucursales->count() === 1 ? $sucursales->first() : null,
            'hayCrediJoya'  => $total['credijoya'] > 0,
        ]);
    }

    private function sumar(array $fila, string $tipo, float $monto): array
    {
        $fila['operaciones']++;
        $fila[$tipo]++;
        $fila['monto'] = round($fila['monto'] + $monto, 2);

        return $fila;
    }

    private function tipo(Credito $credito): string
    {
        if ($credito->categoria === 'credijoya' || $credito->subproducto === 'credijoya') {
            return 'credijoya';
        }

        return $credito->categoria === 'grupal' ? 'grupal' : 'individual';
    }
}
