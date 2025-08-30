<?php
namespace App\Http\Controllers;

use App\Models\credito;
use App\Models\Cronograma;
use App\Models\CredijoyaJoya;
use App\Models\IngresoExtra;
use App\Models\Ingreso;
use App\Models\CreditoCliente;
use App\Models\CajaTransaccion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DevolucionController extends Controller
{
    public function index() {
        return view('admin.credijoya.devoluciones.index');
    }

    public function list(Request $r) {
        $q = credito::with(['joyas' => function($qq){
                    $qq->where('devuelta', 1);
                }, 'clientes'])
            ->where('estado','terminado')
            ->whereHas('joyas', fn($w)=>$w->where('devuelta',1))
            ->latest('fecha_fin')
            ->get()
            ->map(function($c){
                return [
                    'id' => $c->id,
                    'fecha_fin' => $c->fecha_fin,
                    'tasacion_total' => (float)$c->tasacion_total,
                    'clientes' => $c->clientes->pluck('nombre')->join(', '),
                    'joyas' => $c->joyas->map(fn($j)=>[
                        'id'=>$j->id,'descripcion'=>$j->descripcion,'kilate'=>$j->kilate,
                        'peso_neto'=>$j->peso_neto,'valor_tasacion'=>$j->valor_tasacion,'codigo'=>$j->codigo
                    ]),
                ];
            });

        return response()->json(['ok'=>true,'data'=>$q]);
    }

    // === Helper: cálculo de custodia acumulada, pagada y pendiente ===
    private function calcularEstadoCustodia(Credito $credito): array
    {
        // Fecha base: cancelación total del crédito
        $fin =Ingreso::where('prestamo_id',$credito->id)->latest()->first()->fecha_pago;

        $diasGracia = (int)config('credijoya.custodia_dias_gracia', 15);
        $desde      = $fin ? \Carbon\Carbon::parse($fin)->startOfDay() : null;
        $hoy        = now()->startOfDay();

        $diasTrans = ($desde ? $desde->diffInDays($hoy) : 0);
        $desdeDia  = $diasGracia + 1;            // cobra desde el día 16
        $diasCobra = max($diasTrans - $diasGracia, 0);

        $porcMensual = (float)config('credijoya.custodia_porcentaje_mensual', 26.82); // % mensual
        $tasaDiaria  = $porcMensual / 100 / 30;  // prorrateo diario
        $base        = (float)$credito->tasacion_total; // si tu política cambia, ajusta aquí

        // Acumulado bruto al día de hoy
        $acumulado = $fin ? round($base * $tasaDiaria * $diasCobra, 2) : 0.00;

        // Pagos ya realizados (pueden ser múltiples)
        $pagado = (float) IngresoExtra::where('motivo','custodia')
            ->where('serie_documento',$credito->id)
            ->sum('monto');

        $pendiente = round(max($acumulado - $pagado, 0), 2);

        return [
            'tiene_fecha_fin'  => (bool)$fin,
            'fecha_fin'        => $fin,
            'dias_transcurridos'=> $diasTrans,
            'desde_dia'        => $desdeDia,
            'dias_cobra'       => $diasCobra,
            'porcentaje_mensual'=> $porcMensual,
            'tasa_diaria'      => $tasaDiaria,
            'base'             => $base,
            'acumulado'        => $acumulado,
            'pagado'           => round($pagado,2),
            'pendiente'        => $pendiente,
        ];
    }

    // === API: calcular detalle de custodia (para UI) ===
    public function calcularCustodia(credito $credito) {
        $est = $this->calcularEstadoCustodia($credito);
        return response()->json(['ok'=>true] + $est);
    }

    // === API: pago de custodia (parcial o total) ===
    public function pagarCustodia(Request $r, credito $credito) {
        $r->validate([
            'monto' => ['required','numeric','min:0.01']
        ]);

        $est   = $this->calcularEstadoCustodia($credito);
        $deuda = $est['pendiente'];

        // Permite pagar varias veces. Si quieren impedir sobrepago, descomenta:
        if ($r->monto > $deuda + 0.001) {
            return response()->json(['ok'=>false,'error'=>'El monto excede la deuda de custodia (S/ '.number_format($deuda,2).').'],422);
        }

        $user = auth()->user();
        $caja = CajaTransaccion::where('user_id',$user->id)->whereNull('hora_cierre')->latest()->first();
        if(!$caja){
            return response()->json(['ok'=>false,'error'=>'No hay caja abierta.'],400);
        }

        $ing = IngresoExtra::create([
            'user_id'            => $user->id,
            'monto'              => round((float)$r->monto,2),
            'motivo'             => 'custodia',
            'numero_documento'   => 'CUSTODIA',
            'serie_documento'    => $credito->id, // vínculo con el crédito
            'observaciones'      => 'Servicio de custodia de joyas',
            'caja_transaccion_id'=> $caja->id,
            'archivo'            => null,
        ]);

        $caja->cantidad_ingresos = (float)($caja->cantidad_ingresos ?? 0) + (float)$ing->monto;
        $caja->save();

        return response()->json([
            'ok'=>true,
            'ticket_url'=>route('devoluciones.ticket.custodia',$ing->id),
            'ingreso_extra_id'=>$ing->id,
        ]);
    }

    // === API: devolver joyas (solo si NO hay deuda de custodia pendiente) ===
    public function devolver(Request $r, credito $credito) {
        $r->validate([
            'joya_ids' => ['required','array','min:1'],
            'joya_ids.*' => ['integer']
        ]);

        // Bloquea devolución si existe custodia pendiente
        $est = $this->calcularEstadoCustodia($credito);
        if ($est['pendiente'] > 0.001) {
            return response()->json([
                'ok'=>false,
                'error'=>'No se puede devolver: hay custodia pendiente de S/ '.number_format($est['pendiente'],2)
            ], 422);
        }

        DB::transaction(function() use ($r,$credito){
            $joyas = CredijoyaJoya::where('prestamo_id',$credito->id)
                ->whereIn('id',$r->joya_ids)
                ->where('devuelta',1) // solo “listas para entregar”
                ->get();

            foreach($joyas as $j){
                $j->devuelta = 2; // entregada
                $j->fecha_devolucion = now();
                $j->save();
            }
        });

        return response()->json([
            'ok'=>true,
            'hoja_url'=>route('devoluciones.hoja',$credito->id),
        ]);
    }

    // DevolucionController@ticketCustodia
    public function ticketCustodia(IngresoExtra $ingresoExtra) {
        $wMm = (int) request()->get('w', 58);
        if (!in_array($wMm,[58,80],true)) $wMm=58;
        $wPts = ($wMm === 80) ? 280 : 200; // un poco más ancho si es 80mm
        $hPts = 450; // alto generoso (Dompdf recorta si sobra)

        // Cliente
        $cc = \App\Models\CreditoCliente::with('clientes')
            ->where('prestamo_id', $ingresoExtra->serie_documento)->first();
        $clienteNombre = $cc->clientes?->nombre ?? '---';

        // Crédito y estado de custodia (para mostrar saldo)
        $credito = \App\Models\credito::find($ingresoExtra->serie_documento);
        $estado  = $credito ? $this->calcularEstadoCustodia($credito) : [
            'base'=>0,'porcentaje_mensual'=>0,'dias_cobra'=>0,'acumulado'=>0,'pagado'=>0,'pendiente'=>0,'desde_dia'=>16
        ];

        $pdf = Pdf::loadView('admin.credijoya.tickets.ticket-custodia', [
            'ing'        => $ingresoExtra,
            'clienteNombre' => $clienteNombre,
            'fecha'      => now()->format('Y-m-d'),
            'hora'       => now()->format('H:i:s'),
            'widthMm'    => $wMm,
            'estado'     => $estado,   // ← acumulado/pagado/pendiente
           
           
        ])->setPaper([0,0,$wPts,$hPts],'portrait'); // por si el logo es remoto

        return $pdf->stream('ticket_custodia_'.$ingresoExtra->id.'.pdf');
    }


    // === Hoja A4 ===
    public function hojaDevolucion(credito $credito) {
        $cc=CreditoCliente::with('clientes')->where('prestamo_id',$credito->id)->first();
        $clienteNombre = $cc->clientes?->nombre ?? '---';
         $clienteDni = $cc->clientes?->documento_identidad ?? '---';
        $joyasDevueltas = CredijoyaJoya::where('prestamo_id',$credito->id)
            ->where('devuelta',2)
            ->orderBy('id')
            ->get();

        $custodias = IngresoExtra::where('serie_documento',$credito->id)
            ->where('motivo','custodia')
            ->orderBy('id')->get();

        $pdf = Pdf::loadView('admin.credijoya.devoluciones.hoja-devolucion', [
            'credito'=>$credito,
            'clienteNombre'=>$clienteNombre,
            'clienteDni'=> $clienteDni,
            'joyas'=>$joyasDevueltas,
            'custodias'=>$custodias,
            'emitido'=>now(),
        ])->setPaper('a4','portrait');

        return $pdf->stream('hoja_devolucion_'.$credito->id.'.pdf');
    }
}
