<?php

namespace App\Http\Controllers;

use App\Models\credito;
use App\Models\Cronograma;
use App\Models\Reprogramacion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReprogramacionController extends Controller
{
    public function viewreprogramacion()
    {
        // traer solo reprogramaciones pendientes con su crédito y cliente
        $reprogramaciones = Reprogramacion::with('credito.clientes', 'solicitante', 'administrador')
            ->where('estado', 'pendiente')
            ->get();

        // pasar exactamente ese nombre al template
        return view('admin.creditos.aprobarreprogramados', compact('reprogramaciones'));
    }


    public function reprogramacionStore(Request $request)
    {
        // 1) Validación
        $request->validate([
            'credito_id'           => 'required|integer',
            'cuotas_solicitadas'   => 'required|integer|min:1',
            'tasa_interes'         => 'required|numeric|min:0',
            'capital_restante'     => 'required|numeric|min:0',
            'periodo_pago'         => 'required|string',
            'observaciones'        => 'nullable|string',
            'cuotas_pendientes'    => 'required|integer|min:1',
            'nuevo_numero_cuotas'  => 'required|integer|min:1',
        ]);

        $creditoId  = $request->credito_id;
        $nPeriodos  = $request->cuotas_solicitadas;
        $credito    = credito::findOrFail($creditoId);

        // 2) Base de cuotas sin pagar
        $baseQuery = Cronograma::where('id_prestamo', $creditoId)
            ->whereNotIn('id', function ($q) {
                $q->select('cronograma_id')->from('ingresos');
            })
            ->orderBy('numero', 'asc');

        // 3) Filtrar según grupal o individual
        if ($credito->categoria === 'grupal') {
            $baseQuery->whereNull('cliente_id');
        } else {
            $baseQuery->whereNotNull('cliente_id');
        }

        // 4) Primera cuota y pendientes
        $primeraPendiente = (clone $baseQuery)->first();
        if (! $primeraPendiente) {
            return response()->json([
                'success' => false,
                'message' => 'No hay cuotas pendientes para reprogramar.'
            ], 422);
        }

        $pendientes = (clone $baseQuery)
            ->take($nPeriodos)
            ->get();

        // 5) Calcular nueva fecha a partir de la fecha de la primera cuota
        $fechaCuota = Carbon::parse($primeraPendiente->fecha);
        for ($i = 0; $i < $nPeriodos; $i++) {
            switch ($credito->recurrencia) {
                case 'catorcenal':
                    $fechaCuota->addDays(14);
                    break;
                case 'quincenal':
                    $fechaCuota->addDays(15);
                    break;
                case 'veinteochenal':
                    $fechaCuota->addDays(28);
                    break;
                case 'semestral':
                    $fechaCuota->addMonths(6);
                    break;
                case 'anual':
                    $fechaCuota->addYears(1);
                    break;
                case 'mensual':
                default:
                    $fechaCuota->addMonth();
                    break;
            }
        }
        $nuevaFecha = $fechaCuota->toDateString();

        // 6) Guardar la reprogramación
        $reprogramacion = Reprogramacion::create([
            'usuario_id'           => Auth::id(),
            'admin_id'             => null,
            'credito_id'           => $creditoId,
            'cuotas_pendientes'    => $request->cuotas_pendientes,
            'tasa_interes'         => $request->tasa_interes,
            'fecha_reprogramar'    => $nuevaFecha,
            'capital_restante'     => $request->capital_restante,
            'interes_restante'     => round($pendientes->sum('interes'), 2),
            'nuevo_numero_cuotas'  => $request->nuevo_numero_cuotas,
            'observaciones'        => $request->observaciones,
            'estado'               => 'pendiente',
            'comentario_admin'     => null,
        ]);

        return response()->json([
            'success'               => true,
            'id'                    => $reprogramacion->id,
            'nuevaFechaVencimiento' => $nuevaFecha,
            'interes_restante'      => $reprogramacion->interes_restante,
        ], 201);
    }


    public function process(Request $request)
    {
        //dd($request->all() );
        $request->validate([
            'id'             => 'required|exists:reprogramaciones,id',
            'estado'         => 'required|in:aprobada,rechazada',
            'comentario_admin' => 'nullable|string',
        ]);

        $r = Reprogramacion::findOrFail($request->id);

        // Sólo procesar si sigue pendiente
        if ($r->estado !== 'pendiente') {
            return response()->json([
                'message' => 'Esta solicitud ya ha sido procesada.'
            ], 422);
        }



        $r->update([
            'estado'          => $request->estado,
            'comentario_admin' => $request->comentario_admin,
            'admin_id'        => Auth::id(),
        ]);

        return response()->json([
            'message' => $request->estado === 'aprobada'
                ? 'Reprogramación aprobada correctamente.'
                : 'Reprogramación rechazada.'
        ]);
    }
}
