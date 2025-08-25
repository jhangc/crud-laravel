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
use App\Models\Ingreso;
use App\Models\CajaTransaccion;
use App\Models\Egreso;
use Barryvdh\DomPDF\Facade\Pdf;

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
            case 'catorcenal':
                $n = 26;
                break;
            case 'quincenal':
                $n = 24;
                break;
            case 'veinteochenal':
                $n = 12;
                break;
            case 'semestral':
                $n = 2;
                break;
            case 'anual':
                $n = 1;
                break;
            case 'mensual':
            default:
                $n = 12;
                break;
        }

        $i = pow(1 + ($tea / 100), 1 / $n) - 1;  // tasa por período
        $cuota = ($monto * $i * pow(1 + $i, $periodos)) / (pow(1 + $i, $periodos) - 1);

        $saldo = $monto;
        $cuotas = [];
        $totalAmort = 0;
        $totalCuotas = 0;

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
            return response()->json(['ok' => false, 'message' => 'Debe registrar al menos una joya.'], 422);
        }

        DB::transaction(function () use (
            $r,
            $credito,
            $joyasFront,
            $tasacionTotal,
            $max80_calc,
            $montoAprobado,
            $fechaDesembolso,
            $proxVenc
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
    public function aprobarCredijoya(Request $r, $id)
    {
        $r->validate(['comentario' => 'nullable|string|max:1000']);
        $c = \App\Models\Credito::findOrFail($id);
        $c->estado = 'aprobado';
        $c->comentario_administrador = $r->input('comentario', '');
        $c->save();
        return back()->with('ok', 'Crédito CrediJoya aprobado.');
    }
    public function rechazarCredijoya(Request $r, $id)
    {
        $r->validate(['comentario' => 'required|string|max:1000']);
        $c = \App\Models\Credito::findOrFail($id);
        $c->estado = 'rechazado';
        $c->comentario_administrador = $r->input('comentario', '');
        $c->save();
        return back()->with('ok', 'Crédito CrediJoya rechazado.');
    }
    public function pagar(Request $request, $id)
    {
        $prestamo     = credito::with(['clientes', 'user', 'joyas'])->findOrFail($id);
        $cliente      = $prestamo->clientes->first();
        $responsable  = $prestamo->user;
        $estado       = $prestamo->estado;

        // Cronograma del crédito actual (solo para mostrar arriba en la vista)
        $cronograma = Cronograma::where('id_prestamo', $id)
            ->orderBy('numero')
            ->get();

        // === Deudas previas del mismo cliente (individual y credijoya), que sigan vigentes ===
        // Importante: usamos categoria (no "tipo") y excluimos estados cerrados.
        $deudasPrevias = credito::with(['joyas'])
            ->where('id', '!=', $prestamo->id)
            ->whereHas('clientes', function ($q) use ($cliente) {
                $q->where('clientes.id', $cliente->id);
            })
            ->whereIn('categoria', ['individual', 'credijoya'])  // <- ajusta si tus categorías difieren
            ->whereIn('estado', ['pagado', 'mora'])    // <- ajusta si tienes más estados de cierre
            ->get()
            ->map(function ($c) {

                // Cuotas del crédito $c
                $cuotas = Cronograma::where('id_prestamo', $c->id)
                    ->orderBy('numero')
                    ->get(['id', 'fecha', 'monto', 'numero']);

                $totalPendiente = 0.0;

                foreach ($cuotas as $cuota) {
                    // ¿Ya hay ingreso para esta cuota?
                    $ingreso = Ingreso::where('prestamo_id', $c->id)
                        ->where('cronograma_id', $cuota->id)
                        ->first();

                    if ($ingreso) {
                        // Pagada: no suma a deuda
                        continue;
                    }

                    // Igual que en verpagocuota: 1.5 por mil por día de mora
                    $moraData = $this->calcularMoraPorDia((float)$cuota->monto, (string)$cuota->fecha);
                    $totalCuota = round(((float)$cuota->monto) + $moraData['mora'], 2);

                    $totalPendiente += $totalCuota;
                }

                // Saldo real (cuotas impagas + mora acumulada a hoy)
                $c->saldo_pendiente = round($totalPendiente, 2);

                return $c;
            })
            // Solo mostrar créditos que de verdad tienen saldo por cancelar
            ->filter(function ($c) {
                return $c->saldo_pendiente > 0.009;
            })
            ->values();

        return view('admin.caja.credijoyadesembolso', compact(
            'prestamo',
            'cliente',
            'responsable',
            'estado',
            'cronograma',
            'deudasPrevias'
        ));
    }
    public function cuotasPendientes(Request $r)
    {
        $creditoId = (int) $r->query('credito_id');
        $modo      = $r->query('modo', 'parcial'); // 'parcial' | 'total'
        $credito   = Credito::findOrFail($creditoId);

        // Cuotas ordenadas
        $cuotas = Cronograma::where('id_prestamo', $creditoId)
            ->orderBy('numero')
            ->get();

        // Excluir pagadas
        $idsPagadas = Ingreso::where('prestamo_id', $creditoId)
            ->pluck('cronograma_id')
            ->filter()
            ->flip();

        $detalle = [];
        $primeraHabilitable = true; // para PARCIAL: sólo la 1ª sin pagar habilitada inicialmente

        foreach ($cuotas as $c) {
            if (isset($idsPagadas[$c->id])) continue;

            $hoy = now();
            $vto = \Carbon\Carbon::parse($c->fecha);

            // Inicio de período = fecha cuota anterior o desembolso
            $prev = Cronograma::where('id_prestamo', $creditoId)
                ->where('numero', (int)$c->numero - 1)
                ->first();
            $inicioPeriodo = \Carbon\Carbon::parse($prev ? $prev->fecha : $credito->fecha_desembolso);

            $esVencida       = $hoy->greaterThan($vto);
            $insidePeriodo   = !$esVencida && $hoy->greaterThanOrEqualTo($inicioPeriodo) && $hoy->lessThanOrEqualTo($vto);
            $futurePeriodo   = $hoy->lessThan($inicioPeriodo);
            $diasHastaVencer = $hoy->lessThan($vto) ? $hoy->diffInDays($vto) : 0;

            // Datos base
            $montoCuota        = (float)($c->monto ?? 0);           // amort + interés programado
            $amortizacion      = (float)($c->amortizacion ?? 0);
            $interesProgramado = (float)($c->interes ?? 0);
            $interesHoyCalc    = $this->interesDevengadoHastaHoy($c, $credito); // prorrateado & acotado
            $moraData          = $this->calcularMoraPorDia((float)$c->monto, (string)$c->fecha);

            $total = 0.0;
            $interesAplicado = 0.0;
            $calculo = '';

            if ($esVencida) {
                // Vencida: cuota completa + mora
                $total = round($montoCuota + $moraData['mora'], 2);
                $interesAplicado = 0.0; // (el interés ya está en el monto de la cuota)
                $calculo = 'VENCIDA: cuota + mora';
            } elseif ($insidePeriodo) {
                if ($modo === 'total') {
                    // TOTAL: #1 y #2 completas; si no, regla de 7 días
                    if (in_array((int)$c->numero, [1, 2], true)) {
                        $total = round($montoCuota, 2);
                        $interesAplicado = $interesProgramado;
                        $calculo = '#1/#2 completas';
                    } else {
                        $usaHoy = ($diasHastaVencer > 7);
                        $interesSegunRegla = $usaHoy ? $interesHoyCalc : $interesProgramado;
                        $total = round($amortizacion + $interesSegunRegla, 2);
                        $interesAplicado = round($interesSegunRegla, 2);
                        $calculo = $usaHoy ? 'TOTAL: amort + interés a hoy' : 'TOTAL: amort + interés programado';
                    }
                } else {
                    // PARCIAL: SIEMPRE cuota normal (orden forzado lo maneja el front)
                    $total = round($montoCuota, 2);
                    $interesAplicado = $interesProgramado;
                    $calculo = 'PARCIAL: cuota normal';
                }
            } elseif ($futurePeriodo) {
                if ($modo === 'total' && in_array((int)$c->numero, [1, 2], true)) {
                    // TOTAL: #1 y #2 completas aunque aún no inicie
                    $total = round($montoCuota, 2);
                    $interesAplicado = $interesProgramado;
                    $calculo = '#1/#2 completas';
                } else {
                    if ($modo === 'parcial') {
                        // PARCIAL: desde ahora cuota normal (según lo que pediste)
                        $total = round($montoCuota, 2);
                        $interesAplicado = $interesProgramado;
                        $calculo = 'PARCIAL: cuota normal';
                    } else {
                        // TOTAL resto: solo amortización
                        $total = round($amortizacion, 2);
                        $interesAplicado = 0.0;
                        $calculo = 'TOTAL: solo amortización';
                    }
                }
            } else {
                // Fallback
                $total = round($amortizacion, 2);
                $interesAplicado = 0.0;
                $calculo = $modo === 'parcial' ? 'PARCIAL: cuota normal' : 'TOTAL: solo amortización';
            }

            // Habilitación inicial (orden forzado en PARCIAL)
            $habilitada = true;
            $motivoBloqueo = null;
            if ($modo === 'parcial') {
                $habilitada = $primeraHabilitable;
                if ($habilitada) {
                    $primeraHabilitable = false; // sólo la primera queda habilitada al inicio
                } else {
                    $motivoBloqueo = 'Paga primero las cuotas anteriores';
                }
            }

            $detalle[] = [
                'cronograma_id'      => $c->id,
                'numero'             => $c->numero,
                'fecha'              => $c->fecha,
                'monto'              => round($montoCuota, 2),
                'amortizacion'       => round($amortizacion, 2),
                'interes'            => round($interesProgramado, 2),
                'interes_hoy'        => $interesHoyCalc,          // referencia
                'interes_aplicado'   => $interesAplicado,         // lo que se cobra según reglas
                'dias_mora'          => $moraData['dias'],
                'porcentaje'         => $moraData['porcentaje'],
                'mora'               => $moraData['mora'],
                'dias_hasta_vencer'  => $diasHastaVencer,
                'total'              => $total,
                'vencida'            => $esVencida ? 1 : 0,
                'inside_periodo'     => $insidePeriodo ? 1 : 0,
                'future_periodo'     => $futurePeriodo ? 1 : 0,
                'calculo'            => $calculo,
                'habilitada'         => $habilitada ? 1 : 0,
                'motivo_bloqueo'     => $motivoBloqueo,
            ];
        }

        return response()->json(['ok' => true, 'modo' => $modo, 'cuotas' => $detalle]);
    }

    private function calcularMoraPorDia(float $montoCuota, string $fechaVencimiento): array
    {
        $vto = Carbon::parse($fechaVencimiento);
        if (now()->greaterThan($vto)) {
            $dias = now()->diffInDays($vto);
            $porMil = 1.5; // 1.5 por mil por día
            $mora = round(($montoCuota * $porMil / 1000) * $dias, 2);
            return ['dias' => $dias, 'porcentaje' => $porMil, 'mora' => $mora];
        }
        return ['dias' => 0, 'porcentaje' => 0.0, 'mora' => 0.0];
    }

    private function interesDevengadoHastaHoy(Cronograma $c, Credito $credito)
    {
        $vto   = \Carbon\Carbon::parse($c->fecha);
        $hoy   = now();
        $to    = $hoy->lessThan($vto) ? $hoy : $vto; // nunca más allá del vencimiento
        // inicio del período: fecha de cuota anterior o fecha de desembolso
        $inicioPeriodo = $c->numero > 1
            ? optional(\App\Models\Cronograma::where('id_prestamo', $credito->id)
                ->where('numero', $c->numero - 1)
                ->first())->fecha
            : $credito->fecha_desembolso;
        $inicioPeriodo = \Carbon\Carbon::parse($inicioPeriodo);
        $diasPeriodo = max(1, $inicioPeriodo->diffInDays($vto));
        $diasDev     = max(0, $inicioPeriodo->diffInDays($to));
        $interesProgramado = (float) ($c->interes ?? 0);
        $interesHoy        = $interesProgramado * ($diasDev / $diasPeriodo);
        if ($interesHoy > $interesProgramado) $interesHoy = $interesProgramado;
        return round($interesHoy, 2);
    }

    public function desembolsar(Request $r, $id)
    {
        $r->validate([
            'gastos' => ['nullable', 'numeric', 'min:0'],
            'deudas' => ['nullable', 'string'], // JSON: [{credito_id, cuotas:[{cronograma_id,total,mora,dias_mora}]}]
            'modo'   => ['nullable', 'in:parcial,total'],
        ]);

        $creditoActual = credito::with(['clientes', 'joyas'])->findOrFail($id);
        $montoAprobado = (float) $creditoActual->monto_total;
        $gastos        = (float) ($r->gastos ?? 0);
        $modo          = $r->input('modo', 'parcial');
        $deudasReq     = json_decode($r->deudas ?? '[]', true) ?: [];

        // caja abierta
        $user = auth()->user();
        $caja = CajaTransaccion::where('user_id', $user->id)
            ->whereNull('hora_cierre')
            ->orderBy('created_at', 'desc')
            ->first();
        if (!$caja) return response()->json(['error' => 'No hay una caja abierta para el usuario actual'], 400);

        // Validación & suma previa (con mismas reglas que cuotasPendientes)
        $idsDeudas = collect($deudasReq)->pluck('credito_id')->map('intval')->all();
        $deudasBD  = credito::whereIn('id', $idsDeudas)->with('joyas')->get()->keyBy('id');

        $totalCancelar = 0.0;

        foreach ($deudasReq as $d) {
            $cid = (int)($d['credito_id'] ?? 0);
            if (!$cid || !$deudasBD->has($cid)) {
                return response()->json(['message' => "Crédito #{$cid} inválido."], 422);
            }

            $cuotasSel = collect($d['cuotas'] ?? []);
            if ($cuotasSel->isEmpty()) continue;

            $cronIds = $cuotasSel->pluck('cronograma_id')->filter()->map('intval')->all();
            $cronBD  = Cronograma::whereIn('id', $cronIds)->get()->keyBy('id');

            foreach ($cuotasSel as $q) {
                $crid = (int)($q['cronograma_id'] ?? 0);
                $cr   = $cronBD->get($crid);
                if (!$cr) {
                    return response()->json(['message' => "Cronograma #{$crid} inválido."], 422);
                }

                // mismo cálculo que el endpoint cuotasPendientes
                $calc = $this->totalSegunReglas($cr, $deudasBD->get($cid), $modo);

                $totalEnviado = round((float)($q['total'] ?? 0), 2);
                $totalOk      = round((float)$calc['total'], 2);
                if ($totalEnviado < $totalOk) {
                    // endurecemos mínimo
                    $totalEnviado = $totalOk;
                }

                $totalCancelar += $totalEnviado;
            }
        }

        if ($totalCancelar + $gastos > $montoAprobado) {
            return response()->json(['message' => '(Cancelar deudas + Gastos) supera el monto aprobado.'], 422);
        }

        $netoEntregar = round($montoAprobado - $totalCancelar , 2);

        DB::beginTransaction();
        try {
            $totalIngresadoCaja = 0.0;
            $ingresoIds = [];

            foreach ($deudasReq as $d) {
                $cid = (int)($d['credito_id'] ?? 0);
                $cuotasSel = collect($d['cuotas'] ?? []);
                if ($cuotasSel->isEmpty()) continue;

                $cronIds = $cuotasSel->pluck('cronograma_id')->filter()->map('intval')->all();
                $cronBD  = Cronograma::whereIn('id', $cronIds)->get()->keyBy('id');

                foreach ($cuotasSel as $q) {
                    $crid = (int)($q['cronograma_id'] ?? 0);
                    $cr   = $cronBD->get($crid);
                    if (!$cr) continue;

                    $calc = $this->totalSegunReglas($cr, $deudasBD->get($cid), $modo);
                    // reforzamos mínimo
                    $montoTotal = round(max((float)($q['total'] ?? 0), (float)$calc['total']), 2);

                    // evitar duplicado
                    $ingresoExistente = Ingreso::where('prestamo_id', $cid)
                        ->where('cronograma_id', $crid)
                        ->first();
                    if ($ingresoExistente) {
                        $ingresoIds[] = $ingresoExistente->id;
                        $totalIngresadoCaja += (float)$ingresoExistente->monto;
                        continue;
                    }

                    $ing = Ingreso::create([
                        'transaccion_id'         => $caja->id,
                        'prestamo_id'            => $cid,
                        'cliente_id'             => $cr->cliente_id,
                        'cronograma_id'          => $cr->id,
                        'numero_cuota'           => $cr->numero,
                        'monto'                  => $montoTotal,
                        'monto_mora'             => $calc['mora'],
                        'dias_mora'              => $calc['dias_mora'],
                        'porcentaje_mora'        => $calc['porcentaje_mora'],
                        'fecha_pago'             => now()->toDateString(),
                        'hora_pago'              => now()->toTimeString(),
                        'sucursal_id'            => $user->sucursal_id,
                        'monto_total_pago_final' => $montoTotal,
                    ]);

                    $ingresoIds[] = $ing->id;
                    $totalIngresadoCaja += $montoTotal;
                }

                // ¿quedó cancelada?
                $saldoPendiente = $this->recalcularSaldoCredito($cid);
                if ($saldoPendiente <= 0.009) {
                    $cred = $deudasBD->get($cid);
                    $cred->estado = 'terminado';
                    $cred->save();

                    foreach ($cred->joyas as $j) {
                        $j->estado = 1;
                       // $j->fecha_devolucion = now();
                        $j->save();
                    }
                }
            }

            // actualizar caja - ingresos
            if ($totalIngresadoCaja > 0) {
                $caja->cantidad_ingresos = ($caja->cantidad_ingresos ?? 0) + $totalIngresadoCaja;
                $caja->save();
            }

            // EGRESOS (gastos + neto)
            $totalEgresos = 0.0;
            if ($montoAprobado > 0) {
                Egreso::create([
                    'transaccion_id' => $caja->id,
                    'prestamo_id'    => $creditoActual->id,
                    'fecha_egreso'   => now()->toDateString(),
                    'hora_egreso'    => now()->toTimeString(),
                    'monto'          => $montoAprobado,
                    'sucursal_id'    => $user->sucursal_id,
                    'concepto'       => 'Neto entregado CrediJoya #' . $creditoActual->id,
                ]);
                $totalEgresos += $montoAprobado;
            }     
            $caja->cantidad_egresos = ($caja->cantidad_egresos ?? 0) + $totalEgresos;
            $caja->save();
        
            // estado crédito actual
            if (in_array($creditoActual->estado, ['pendiente', 'revisado', 'aprobado'])) {
                $creditoActual->estado = 'pagado';
                $creditoActual->fecha_desembolso = $creditoActual->fecha_desembolso ?? now()->toDateString();
                $creditoActual->neto_recibir=$netoEntregar??0;
                $creditoActual->save();
            }

            DB::commit();

            return response()->json([
                'ok'             => true,
                'neto_entregar'  => number_format($netoEntregar, 2, '.', ''),
                'total_cancelar' => number_format($totalCancelar, 2, '.', ''),
                'caja_id'        => $caja->id,
                'ingreso_ids'    => $ingresoIds, // para el ticket
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error en el desembolso: ' . $e->getMessage()], 500);
        }
    }

   
    private function totalSegunReglas(\App\Models\Cronograma $c, \App\Models\Credito $credito, string $modo): array
    {
        $hoy = now();
        $vto = \Carbon\Carbon::parse($c->fecha);

        // periodo anterior (cuota previa) o desembolso
        $prev = \App\Models\Cronograma::where('id_prestamo', $credito->id)
            ->where('numero', (int)$c->numero - 1)
            ->first();
        $inicioPeriodo = \Carbon\Carbon::parse($prev ? $prev->fecha : $credito->fecha_desembolso);

        $esVencida       = $hoy->greaterThan($vto);
        $insidePeriodo   = !$esVencida && $hoy->greaterThanOrEqualTo($inicioPeriodo) && $hoy->lessThanOrEqualTo($vto);
        $futurePeriodo   = $hoy->lessThan($inicioPeriodo);
        $diasHastaVencer = $hoy->lessThan($vto) ? $hoy->diffInDays($vto) : 0;

        $montoCuota        = (float)($c->monto ?? 0);
        $amortizacion      = (float)($c->amortizacion ?? 0);
        $interesProgramado = (float)($c->interes ?? 0);
        $interesHoyCalc    = $this->interesDevengadoHastaHoy($c, $credito);

        $moraData = $this->calcularMoraPorDia($montoCuota, (string)$c->fecha);

        $total = 0.0;

        if ($esVencida) {
            $total = round($montoCuota + $moraData['mora'], 2);
            return [
                'total'           => $total,
                'mora'            => $moraData['mora'],
                'dias_mora'       => $moraData['dias'],
                'porcentaje_mora' => $moraData['porcentaje'],
            ];
        }

        if ($modo === 'total') {
            if (in_array((int)$c->numero, [1, 2], true)) {
                $total = round($montoCuota, 2);
            } else if ($insidePeriodo) {
                // regla 7 días: si falta >7, interés a hoy; si ≤7, interés programado
                $interesSegunRegla = ($diasHastaVencer > 7) ? $interesHoyCalc : $interesProgramado;
                $total = round($amortizacion + $interesSegunRegla, 2);
            } else if ($futurePeriodo) {
                $total = round($amortizacion, 2);
            } else {
                // fallback
                $total = round($amortizacion, 2);
            }
            return [
                'total'           => $total,
                'mora'            => 0.0,
                'dias_mora'       => 0,
                'porcentaje_mora' => 0.0,
            ];
        }

        // PARCIAL: siempre cuota normal (amort + interés programado) en cualquier estado no vencido
        $total = round($montoCuota, 2);
        return [
            'total'           => $total,
            'mora'            => 0.0,
            'dias_mora'       => 0,
            'porcentaje_mora' => 0.0,
        ];
    }

    private function recalcularSaldoCredito(int $creditoId): float
    {
        $totalCronograma = (float) Cronograma::where('id_prestamo', $creditoId)->sum('monto');
        $totalIngresos   = (float) Ingreso::where('prestamo_id', $creditoId)->sum('monto');
        return round(max($totalCronograma - $totalIngresos, 0), 2);
    }
    public function ticketDesembolsoCJ($prestamoId)
    {
        $prestamo = credito::with('clientes')->findOrFail($prestamoId);
        $user     = auth()->user();

        $montoPrestamo = (float) ($prestamo->monto_total   ?? 0);
        $netoEntregado = (float) ($prestamo->neto_recibir  ?? 0);
        $Deuda         = max($montoPrestamo - $netoEntregado, 0);

        // Tasa ITF (0.05% = 0.0005 por defecto; ajústala en config/env)
        $itfRate   = (float) (config('finanzas.itf_rate', env('ITF_RATE', 0.0005)));

        // Aplica ITF solo si el neto a recibir supera S/ 1,000
        $umbralITF = 1000.00; // cámbialo si lo necesitas
        $aplicaITF = $netoEntregado > $umbralITF;

        // Base del ITF = neto a entregar (no el monto del préstamo)
        $itf = $aplicaITF ? round($netoEntregado * $itfRate, 2) : 0.00;

        // Neto final a pagar en ventanilla
        $netoAPagar = max(round($netoEntregado - $itf, 2), 0);

        $pdf = Pdf::loadView('pdf.ticket_desembolso_credijoya', [
            'prestamo'       => $prestamo,
            'asesor'         => $user,
            'montoPrestamo'  => round($montoPrestamo, 2),
            'Deuda'          => round($Deuda, 2),
            'itfRate'        => $itfRate,
            'itf'            => $itf,
            'aplicaITF'      => $aplicaITF,       // <- para la vista
            'netoEntregado'  => round($netoEntregado, 2),
            'netoAPagar'     => $netoAPagar,
        ])->setPaper([0, 0, 205, 500]);

        return $pdf->stream('ticket_desembolso_credijoya.pdf');
    }




    // 2) NUEVO: ticket de PAGOS de créditos anteriores (uno por cada Ingreso)
    public function ticketPagosAnterioresCJ($prestamoId, $cajaId, $idsDash)
    {
        $user = auth()->user();

        $ids = collect(explode('-', $idsDash))
            ->filter(fn($v) => ctype_digit($v))
            ->map('intval')
            ->values()
            ->all();

        // Trae todos los ingresos de esta transacción (los pagos realizados)
        $ingresos = Ingreso::with(['cronograma','cliente'])
            ->whereIn('id', $ids)
            ->where('transaccion_id', $cajaId)
            ->orderBy('prestamo_id')->orderBy('numero_cuota')
            ->get();

        // Armamos “tickets” individuales (uno por cuota pagada)
        $tickets = $ingresos->map(function (Ingreso $ing) {
            $prestamo    = credito::find($ing->prestamo_id);
            $cronograma  = Cronograma::find($ing->cronograma_id);

            // Siguiente cuota
            $siguiente   = Cronograma::where('id_prestamo', $ing->prestamo_id)
                            ->where('numero', '>', $ing->numero_cuota)
                            ->orderBy('numero','asc')
                            ->first();

            return [
                'prestamo'      => $prestamo,
                'cliente'       => $ing->cliente,   // relación
                'ingreso'       => $ing,
                'cronograma'    => $cronograma,
                'sig_cuota'     => $siguiente,
                'fecha_sig'     => $siguiente? $siguiente->fecha : 'N/A',
            ];
        });

        // Alto dinámico para rollo térmico: 420px por ticket aprox.
        $alto = max(420, 420 * max(1, $tickets->count())) + 60;

        $pdf = Pdf::loadView('pdf.tickets_pagos_anteriores', [
            'tickets' => $tickets,
            'asesor'  => $user,
            'fecha'   => now()->format('d/m/Y H:i'),
        ])->setPaper([0, 0, 205, $alto]); // 58mm aprox.

        return $pdf->stream('tickets_pagos_anteriores.pdf');
    }
}
