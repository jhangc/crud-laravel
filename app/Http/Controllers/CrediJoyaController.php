<?php

namespace App\Http\Controllers;

use App\Models\cliente;
use App\Models\CredijoyaJoya;
use App\Models\credito;
use App\Models\CreditoCliente;
use App\Models\Cronograma;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrediJoyaController extends Controller
{

    public function store(Request $r)
    {
        // ==== Validación (pre‑registro) ====
        $r->validate([
            // cliente
            'documento_identidad' => ['required', 'digits:8'],

            // parámetros del crédito
            'tasa_tea'            => ['required', 'numeric', 'min:0'],
            'fecha_desembolso'    => ['required', 'date'],
            'proximo_vencimiento' => ['nullable', 'date'],

            // tasación y monto
            'tasacion_total'      => ['required', 'numeric', 'min:0'],
            'monto_max_80'        => ['required', 'numeric', 'min:0'],
            'monto_aprobado'      => ['required', 'numeric', 'min:0.01'],

            // joyas (JSON del front)
            'joyas'               => ['required', 'string'],
        ]);

        // Joyas del front
        $joyas = json_decode($r->input('joyas'), true) ?: [];
        if (empty($joyas)) {
            return response()->json(['message' => 'Debe registrar al menos una joya.'], 422);
        }

        // Buscar cliente por DNI
        $cliente = cliente::where('documento_identidad', $r->input('documento_identidad'))->first();
        if (!$cliente) {
            return response()->json(['ok' => false, 'message' => 'Cliente no registrado.'], 422);
        }
        $clienteId     = (int) $cliente->id;
        $tasacionTotal = (float) $r->input('tasacion_total');
        $max80         = (float) $r->input('monto_max_80');      // viene del front, pero lo recalculamos por seguridad
        $max80_calc    = round($tasacionTotal * 0.80, 2);
        $montoAprobado = (float) $r->input('monto_aprobado');

        // Blindaje backend: monto aprobado no puede exceder el 80%
        if ($montoAprobado > $max80_calc + 0.001) {
            return response()->json([
                'ok' => false,
                'message' => 'El monto aprobado no puede superar el 80% de la tasación.',
                'max_80'  => $max80_calc
            ], 422);
        }

        // Proximo vencimiento (default +30 días)
        $fechaDesembolso = Carbon::parse($r->input('fecha_desembolso'));
        $proxVenc        = $r->input('proximo_vencimiento')
                            ? Carbon::parse($r->input('proximo_vencimiento'))->toDateString()
                            : $fechaDesembolso->copy()->addDays(30)->toDateString();

        return DB::transaction(function () use ($r, $joyas, $clienteId, $montoAprobado, $tasacionTotal, $max80_calc, $fechaDesembolso, $proxVenc) {

            // 1) Crear crédito (pre‑registro)
            $credito = Credito::create([
                'user_id'              => auth()->id(),
                'id_cliente'           => $clienteId,

                // taxonomía
                'tipo'                 => 'servicio',
                'producto'             => 'individual',
                'subproducto'          => 'credijoya',
                'destino'              => 'personal',
                'recurrencia'          => 'mensual',

                // montos / parámetros
                'tasa'                 => (float) request('tasa_tea'),
                'monto_total'          => $montoAprobado,
                'tasacion_total'       => $tasacionTotal,
                'monto_max_80'         => $max80_calc,

                // campos que ahora NO se usan en pre‑registro (los dejamos en 0 / null)
                'itf_desembolso'       => 0,
                'neto_recibir'         => 0,
                'deuda_prev_modo'      => null,
                'deuda_prev_monto'     => 0,

                'fecha_desembolso'     => $fechaDesembolso->toDateString(),
                'proximo_vencimiento'  => $proxVenc,

                'periodo_gracia_dias'  => 0,
                'fecha_registro'       => now(),
                'fecha_fin'            => $proxVenc,

                'descripcion_negocio'  => 'CrediJoya',
                'nombre_prestamo'      => 'CrediJoya',
                'cantidad_integrantes' => 1,
                'estado'               => 'revisado',   // pre‑registro
                'categoria'            => 'credijoya',
                'tiempo'               => 1,
                'activo'               => 1,
                'porcentaje_credito'   => 0,
            ]);

            // 2) Enlazar cliente
            CreditoCliente::create([
                'prestamo_id'    => $credito->id,
                'cliente_id'     => $clienteId,
                'monto_indivual' => $montoAprobado,
            ]);

            // 3) Cronograma por defecto (1 período)
            $this->guardarCronograma(
                $credito->id,
                $clienteId,
                $montoAprobado,
                (float) request('tasa_tea'),
                $fechaDesembolso->toDateString()
            );

            // 4) Guardar JOYAS (devuelta=0 y codigo generado único)
            foreach ($joyas as $j) {
                CredijoyaJoya::create([
                    'prestamo_id'     => $credito->id,
                    'kilate'          => (int)   ($j['kilataje'] ?? 0),
                    'precio_gramo'    => (float) ($j['precio_gramo'] ?? 0),
                    'peso_bruto'      => isset($j['peso_bruto']) ? (float) $j['peso_bruto'] : null,
                    'peso_neto'       => (float) ($j['peso_neto'] ?? 0),
                    'piezas'          => (int)   ($j['piezas'] ?? 1),
                    'descripcion'     => $j['descripcion'] ?? null,
                    'valor_tasacion'  => (float) ($j['valor_tasacion'] ?? 0),
                    'devuelta'        => 0,
                    'codigo'          => $this->generarCodigoJoyaUnico(),
                ]);
            }

            return response()->json([
                'ok'           => true,
                'prestamo_id'  => $credito->id,
                'message'      => 'CrediJoya pre‑registrado.',
            ]);
        });
    }
    private function generarCodigoJoyaUnico(): string
    {
        // Formato: yymmdd-xyz-1234
        do {
            $code = strtolower(now()->format('ymd'))
                . '-' . strtolower(Str::random(3))
                . '-' . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (CredijoyaJoya::where('codigo', $code)->exists());

        return $code;
    }

    private function guardarCronograma(int $creditoid, int $id_cliente, float $montoAprobado, float $tasa_tea, string $fecha_desembolso): void
    {
        $fecha_desembolso = Carbon::parse($fecha_desembolso);
        $periodos   = 1;
        $frecuencia = 'mensual';
        $cuotas     = $this->calcularCuota($montoAprobado, $tasa_tea, $periodos, $frecuencia);

        $fechaCuota = $fecha_desembolso->copy();
        foreach ($cuotas as $c) {
            // mensual: +1 mes
            $fechaCuota->addMonth();

            $cron = new Cronograma();
            $cron->fecha        = $fechaCuota->toDateString();
            $cron->monto        = $c['cuota'];
            $cron->numero       = $c['numero_cuota'];
            $cron->capital      = $c['capital'];
            $cron->interes      = $c['interes'];
            $cron->amortizacion = $c['amortizacion'];
            $cron->saldo_deuda  = $c['saldo_deuda'];
            $cron->id_prestamo  = $creditoid;
            $cron->cliente_id   = $id_cliente;
            $cron->save();
        }
    }

    private function calcularCuota(float $monto, float $tea, int $periodos, string $frecuencia): array
    {
        // períodos/año
        switch ($frecuencia) {
            case 'catorcenal':  $n = 26; break;
            case 'quincenal':   $n = 24; break;
            case 'veinteochenal': $n = 12; break;
            case 'semestral':   $n = 2;  break;
            case 'anual':       $n = 1;  break;
            case 'mensual':
            default:            $n = 12; break;
        }

        $i = pow(1 + ($tea / 100), 1 / $n) - 1;  // tasa por período
        $cuota = ($monto * $i * pow(1 + $i, $periodos)) / (pow(1 + $i, $periodos) - 1);

        $saldo = $monto;
        $cuotas = [];
        $totalAmort = 0; $totalCuotas = 0;

        for ($k = 0; $k < $periodos; $k++) {
            $interes      = $saldo * $i;
            $amortizacion = $cuota - $interes;
            $saldo       -= $amortizacion;

            $cuotas[] = [
                'numero_cuota' => $k + 1,
                'capital'      => round(max($saldo, 0), 2),
                'interes'      => round($interes, 2),
                'amortizacion' => round($amortizacion, 2),
                'cuota'        => round($cuota, 2),
                'saldo_deuda'  => round(max($saldo, 0), 2),
            ];

            $totalAmort  += round($amortizacion, 2);
            $totalCuotas += round($cuota, 2);
        }

        // Ajuste redondeo última cuota
        $difAmort = round($monto - $totalAmort, 2);
        $difCuota = round(($cuota * $periodos) - $totalCuotas, 2);
        if (abs($difAmort) > 0.001 || abs($difCuota) > 0.001) {
            $idx = count($cuotas) - 1;
            $cuotas[$idx]['amortizacion'] += $difAmort;
            $cuotas[$idx]['cuota']        += $difCuota;
            $cuotas[$idx]['capital']       = 0.00;
            $cuotas[$idx]['saldo_deuda']   = 0.00;
        }

        return $cuotas;
    }
    private function calcularDeudaPreviaSimple(int $clienteId): float
    {
        $prestamos = \App\Models\Credito::query()
            ->crediJoya()->activos()
            ->where('id_cliente', $clienteId)
            ->get(['id']);

        $total = 0.0;
        foreach ($prestamos as $p) {
            $c = \App\Models\Cronograma::where('id_prestamo', $p->id)
                ->orderByDesc('numero')
                ->first(['saldo_deuda', 'nuevo_saldo_deuda']);
            if ($c) {
                $saldo = is_null($c->nuevo_saldo_deuda) ? (float) $c->saldo_deuda : (float) $c->nuevo_saldo_deuda;
                if ($saldo > 0) $total += $saldo;
            }
        }
        return round($total, 2);
    }

    public function deudaPrevia(Request $r)
    {
        $dni = trim((string) $r->query('dni', ''));
        if ($dni === '' || !preg_match('/^\d{8}$/', $dni)) {
            return response()->json(['ok' => false, 'message' => 'DNI inválido'], 422);
        }

        $cliente = cliente::where('documento_identidad', $dni)->first(['id']);
        if (!$cliente) {
            // si no hay cliente, deuda previa = 0
            return response()->json(['ok' => true, 'total' => 0.00, 'message' => 'Cliente no registrado.'], 200);
        }

        $total = $this->calcularDeudaPreviaSimple((int)$cliente->id);
        return response()->json(['ok' => true, 'total' => $total], 200);
    }
    public function update(Request $r, int $id)
    {
        // En edición NO cambiamos el cliente; solo parámetros y joyas
        $r->validate([
            'tasa_tea'            => ['required', 'numeric', 'min:0'],
            'fecha_desembolso'    => ['required', 'date'],
            'proximo_vencimiento' => ['nullable', 'date'],

            'tasacion_total'      => ['required', 'numeric', 'min:0'],
            'monto_max_80'        => ['required', 'numeric', 'min:0'], // referencial del front
            'monto_aprobado'      => ['required', 'numeric', 'min:0.01'],

            'joyas'               => ['required', 'string'], // JSON del front (con id si existen)
        ]);

        $credito = Credito::findOrFail($id);

        // ---- seguridad backend: 80% de tasación ----
        $tasacionTotal = (float) $r->input('tasacion_total');
        $max80_calc    = round($tasacionTotal * 0.80, 2);
        $montoAprobado = (float) $r->input('monto_aprobado');

        if ($montoAprobado > $max80_calc + 0.001) {
            return response()->json([
                'ok' => false,
                'message' => 'El monto aprobado no puede superar el 80% de la tasación.',
                'max_80'  => $max80_calc
            ], 422);
        }

        $fechaDesembolso = Carbon::parse($r->input('fecha_desembolso'));
        $proxVenc        = $r->input('proximo_vencimiento')
                            ? Carbon::parse($r->input('proximo_vencimiento'))->toDateString()
                            : $fechaDesembolso->copy()->addDays(30)->toDateString();

        $joyasFront = collect(json_decode($r->input('joyas'), true) ?: []);

        if ($joyasFront->isEmpty()) {
            return response()->json(['ok'=>false,'message'=>'Debe registrar al menos una joya.'],422);
        }

        DB::transaction(function () use (
            $r, $credito, $joyasFront, $tasacionTotal, $max80_calc, $montoAprobado, $fechaDesembolso, $proxVenc
        ) {
            // 1) Actualizar cabecera del crédito
            $credito->update([
                'tasa'                => (float) $r->input('tasa_tea'),
                'monto_total'         => $montoAprobado,
                'tasacion_total'      => $tasacionTotal,
                'monto_max_80'        => $max80_calc,

                // en pre‑registro estos quedan en 0 / null (se manejarán en desembolso)
                'itf_desembolso'      => 0,
                'neto_recibir'        => 0,
                'deuda_prev_modo'     => null,
                'deuda_prev_monto'    => 0,

                'fecha_desembolso'    => $fechaDesembolso->toDateString(),
                'proximo_vencimiento' => $proxVenc,
                'fecha_fin'           => $proxVenc,
                // mantenemos estado/categoria/etc. tal cual
            ]);

            // 2) Regenerar cronograma (1 período) — simple y consistente con pre‑registro
            Cronograma::where('id_prestamo', $credito->id)->delete();
            $this->guardarCronograma(
                $credito->id,
                (int) $credito->id_cliente,
                $montoAprobado,
                (float) $r->input('tasa_tea'),
                $fechaDesembolso->toDateString()
            );

            // 3) Upsert de JOYAS (actualiza/crea y borra las quitadas)
            $idsKept = [];

            foreach ($joyasFront as $j) {
                $payload = [
                    'kilate'         => (int)   ($j['kilataje'] ?? 0),
                    'precio_gramo'   => (float) ($j['precio_gramo'] ?? 0),
                    'peso_bruto'     => isset($j['peso_bruto']) ? (float) $j['peso_bruto'] : null,
                    'peso_neto'      => (float) ($j['peso_neto'] ?? 0),
                    'piezas'         => (int)   ($j['piezas'] ?? 1),
                    'descripcion'    => $j['descripcion'] ?? null,
                    'valor_tasacion' => (float) ($j['valor_tasacion'] ?? 0),
                ];

                if (!empty($j['id'])) {
                    // actualizar existente (si pertenece al préstamo)
                    $joya = CredijoyaJoya::where('prestamo_id', $credito->id)
                            ->where('id', (int)$j['id'])
                            ->first();

                    if ($joya) {
                        $joya->update($payload);
                        $idsKept[] = $joya->id;
                    }
                } else {
                    // crear nueva
                    $joya = CredijoyaJoya::create(array_merge($payload, [
                        'prestamo_id' => $credito->id,
                        'devuelta'    => 0,
                        'codigo'      => $this->generarCodigoJoyaUnico(),
                    ]));
                    $idsKept[] = $joya->id;
                }
            }

            // eliminar joyas que fueron quitadas en el front
            CredijoyaJoya::where('prestamo_id', $credito->id)
                ->whereNotIn('id', $idsKept)
                ->delete();
        });

        return response()->json([
            'ok'      => true,
            'message' => 'CrediJoya actualizado correctamente.',
        ]);
    }
    //acciones
    public function aprobarCredijoya(Request $r, $id) {
        $r->validate(['comentario'=>'nullable|string|max:1000']);
        $c = \App\Models\Credito::findOrFail($id);
        $c->estado = 'aprobado';
        $c->comentario_administrador = $r->input('comentario', '');
        $c->save();
        return back()->with('ok','Crédito CrediJoya aprobado.');
    }
    public function rechazarCredijoya(Request $r, $id) {
        $r->validate(['comentario'=>'required|string|max:1000']);
        $c = \App\Models\Credito::findOrFail($id);
        $c->estado = 'rechazado';
        $c->comentario_administrador = $r->input('comentario', '');
        $c->save();
        return back()->with('ok','Crédito CrediJoya rechazado.');
    }

}
