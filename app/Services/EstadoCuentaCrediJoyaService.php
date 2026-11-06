<?php

namespace App\Services;

use App\Models\CredijoyaJoya;
use App\Models\Credito;
use App\Models\CreditoCliente;
use App\Models\Cronograma;
use App\Models\Ingreso;
use App\Models\Sucursal;
use Carbon\Carbon;

/**
 * Estado de cuenta de CrediJoya (préstamo con joyas en garantía).
 *
 * SOLO LECTURA. CrediJoya no se comporta como los demás créditos:
 *  - Al pagar solo el interés (o interés + adelanto) el crédito se RENUEVA: se
 *    cierra y se crea uno nuevo que guarda el anterior en `deuda_prev_modo`.
 *    Por eso el estado de cuenta recorre la CADENA completa de créditos.
 *  - Las joyas se mueven al crédito más reciente de la cadena.
 *  - "Para cancelar hoy" y "para renovar hoy" replican la pantalla de cobro de
 *    CrediJoya (resources/views/admin/credijoya/pago.blade.php + createPago):
 *      cancelar vencida  = cuota + mora
 *      cancelar en curso = capital + (interés a hoy si faltan > 7 días; si no, interés programado)
 *      con abonos previos = saldo de la cuota + mora (modo abono de caja)
 *      renovar           = (interés a hoy, o programado si está vencida) + mora
 *    Si cambia esa regla en caja, cámbiala también aquí.
 */
class EstadoCuentaCrediJoyaService
{
    public static function esCrediJoya(Credito $credito): bool
    {
        return $credito->categoria === 'credijoya' || $credito->subproducto === 'credijoya';
    }

    public function generar(Credito $credito): array
    {
        $cadena = $this->cadena($credito);
        $original = $cadena[0];
        $actual   = end($cadena);

        $cc = CreditoCliente::with('clientes')->where('prestamo_id', $actual->id)->first()
            ?? CreditoCliente::with('clientes')->where('prestamo_id', $original->id)->first();
        $cliente = optional($cc)->clientes;

        // ---- Historial: una fila por crédito de la cadena + lista de pagos ----
        $creditosFilas = [];
        $pagos = [];
        foreach ($cadena as $i => $cr) {
            $cuotas = Cronograma::where('id_prestamo', $cr->id)->orderBy('numero')->get();
            $ingresos = Ingreso::where('prestamo_id', $cr->id)->orderBy('fecha_pago')->orderBy('id')->get();

            $renovadoA = optional($ingresos->first(fn ($p) => !empty($p->nuevo_id)))->nuevo_id
                ?? optional($cadena[$i + 1] ?? null)->id;

            if ($renovadoA) {
                $resultado = 'Renovado';
            } elseif ($cr->estado === 'terminado') {
                $resultado = 'Cancelado';
            } else {
                $resultado = 'Vigente';
            }

            $creditosFilas[] = [
                'secuencia'     => $i + 1,
                'credito_id'    => $cr->id,
                'desembolso'    => $cr->fecha_desembolso ? Carbon::parse($cr->fecha_desembolso) : null,
                'vencimiento'   => $cuotas->last() ? Carbon::parse($cuotas->last()->fecha) : ($cr->proximo_vencimiento ? Carbon::parse($cr->proximo_vencimiento) : null),
                'capital'       => round((float) $cr->monto_total, 2),
                'tea'           => (float) $cr->tasa,
                'interes_prog'  => round((float) $cuotas->sum('interes'), 2),
                'fecha_pago'    => optional($ingresos->last())->fecha_pago,
                'interes_pag'   => round((float) $ingresos->sum('interes_pagado'), 2),
                'mora_pag'      => round((float) $ingresos->sum('monto_mora'), 2),
                'capital_pag'   => round((float) $ingresos->sum('capital_pagado'), 2),
                'total_pag'     => round((float) $ingresos->sum('monto'), 2),
                'resultado'     => $resultado,
                'renovado_a'    => $renovadoA,
            ];

            foreach ($ingresos as $p) {
                $pagos[] = [
                    'fecha'       => Carbon::parse($p->fecha_pago),
                    'credito_id'  => $cr->id,
                    'concepto'    => $this->concepto($p),
                    'interes'     => round((float) $p->interes_pagado, 2),
                    'mora'        => round((float) $p->monto_mora, 2),
                    'capital'     => round((float) $p->capital_pagado, 2),
                    'total'       => round((float) $p->monto, 2),
                    'renueva_a'   => $p->nuevo_id,
                ];
            }
        }

        // ---- Situación de hoy (crédito más reciente) ----
        $hoy = $this->situacionHoy($actual);

        // ---- Joyas (están en el crédito más reciente) ----
        $joyas = CredijoyaJoya::where('prestamo_id', $actual->id)
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get()
            ->map(function ($j) {
                if (!(int) $j->devuelta) {
                    $estado = 'En custodia';
                } elseif ($j->fecha_devolucion) {
                    $estado = 'Entregada';
                } else {
                    $estado = 'Liberada';
                }
                return [
                    'codigo'      => $j->codigo,
                    'descripcion' => $j->descripcion,
                    'kilate'      => $j->kilate,
                    'piezas'      => (int) $j->piezas,
                    'peso_bruto'  => (float) $j->peso_bruto,
                    'peso_neto'   => (float) $j->peso_neto,
                    'tasacion'    => round((float) $j->valor_tasacion, 2),
                    'resguardo'   => round((float) $j->monto_resguardo, 2),
                    'estado'      => $estado,
                    'entregada'   => $j->fecha_devolucion ? Carbon::parse($j->fecha_devolucion) : null,
                ];
            })
            ->all();

        $sum = fn (array $rows, string $k) => round(array_sum(array_column($rows, $k)), 2);

        $sucursal = optional(optional($original->user)->sucursal)->nombre ?? optional(Sucursal::first())->nombre;

        return [
            'cabecera' => [
                'cliente'           => optional($cliente)->nombre ?? '—',
                'cliente_id'        => optional($cliente)->id,
                'documento'         => optional($cliente)->documento_identidad,
                'original_id'       => $original->id,
                'actual_id'         => $actual->id,
                'fecha_original'    => $original->fecha_desembolso ? Carbon::parse($original->fecha_desembolso) : null,
                'monto_original'    => round((float) $original->monto_total, 2),
                'tasacion'          => round((float) ($actual->tasacion_total ?: $original->tasacion_total), 2),
                'tea'               => (float) $actual->tasa,
                'renovaciones'      => count($cadena) - 1,
                'asesor'            => optional($original->user)->name,
                'oficina'           => $sucursal,
                'condicion'         => $hoy['condicion'],
                'dias_atraso'       => $hoy['dias_atraso'],
                'vence'             => $hoy['vence'],
            ],
            'hoy'      => $hoy,
            'creditos' => $creditosFilas,
            'pagos'    => $pagos,
            'joyas'    => $joyas,
            'totales'  => [
                'interes' => $sum($pagos, 'interes'),
                'mora'    => $sum($pagos, 'mora'),
                'capital' => $sum($pagos, 'capital'),
                'pagado'  => $sum($pagos, 'total'),
                'tasacion'=> $sum($joyas, 'tasacion'),
            ],
        ];
    }

    /** Créditos de la cadena, del original al más reciente. */
    public function cadena(Credito $credito): array
    {
        $visto = [$credito->id => true];

        // Hacia atrás.
        $atras = [];
        $cur = $credito;
        while ($cur && is_numeric($cur->deuda_prev_modo) && (int) $cur->deuda_prev_modo > 0) {
            $prev = Credito::find((int) $cur->deuda_prev_modo);
            if (!$prev || isset($visto[$prev->id]) || !self::esCrediJoya($prev)) {
                break;
            }
            $visto[$prev->id] = true;
            array_unshift($atras, $prev);
            $cur = $prev;
        }

        // Hacia adelante.
        $adelante = [];
        $cur = $credito;
        while (true) {
            $next = Credito::where('deuda_prev_modo', (string) $cur->id)
                ->where(fn ($q) => $q->where('categoria', 'credijoya')->orWhere('subproducto', 'credijoya'))
                ->orderBy('id')
                ->first();
            if (!$next || isset($visto[$next->id])) {
                break;
            }
            $visto[$next->id] = true;
            $adelante[] = $next;
            $cur = $next;
        }

        return array_merge($atras, [$credito], $adelante);
    }

    /** Montos de hoy del crédito vigente (misma regla que la pantalla de cobro). */
    private function situacionHoy(Credito $actual): array
    {
        $base = [
            'vigente'        => false,
            'condicion'      => 'Cancelado',
            'dias_atraso'    => 0,
            'vence'          => null,
            'cuota'          => 0.0,
            'capital'        => 0.0,
            'interes_prog'   => 0.0,
            'interes_hoy'    => 0.0,
            'mora'           => 0.0,
            'saldo'          => 0.0,
            'con_abonos'     => false,
            'cancelar'       => ['capital' => 0.0, 'interes' => 0.0, 'mora' => 0.0, 'total' => 0.0],
            'renovar'        => ['interes' => 0.0, 'mora' => 0.0, 'total' => 0.0],
        ];

        if ($actual->estado === 'terminado') {
            return $base;
        }

        $cuota = Cronograma::where('id_prestamo', $actual->id)
            ->orderBy('numero')
            ->get()
            ->first(fn ($c) => $c->saldoYMora()['saldo'] > 0.009);

        if (!$cuota) {
            return $base;
        }

        $ahora    = Carbon::now();
        $vto      = Carbon::parse($cuota->fecha);
        $vencida  = $ahora->greaterThan($vto);
        $diasRest = $vencida ? 0 : $ahora->diffInDays($vto);

        $est      = $cuota->saldoYMora();
        $mora     = round((float) $est['mora'], 2);
        $saldo    = round((float) $est['saldo'], 2);
        $cuotaP   = round((float) $cuota->monto, 2);
        $amort    = round((float) $cuota->amortizacion, 2);
        $intProg  = round((float) $cuota->interes, 2);
        $intHoy   = $this->interesHoy($cuota, $actual);
        $conAbono = $cuota->ingresos()->exists();

        if ($conAbono) {
            // Con abonos, caja solo deja seguir abonando: tope = saldo + mora.
            $interesPag = (float) Ingreso::where('cronograma_id', $cuota->id)->sum('interes_pagado');
            $intPend    = round(min(max($intProg - $interesPag, 0), $saldo), 2);
            $cancelar   = ['capital' => round($saldo - $intPend, 2), 'interes' => $intPend, 'mora' => $mora];
        } elseif ($vencida) {
            $cancelar = ['capital' => $amort, 'interes' => round($cuotaP - $amort, 2), 'mora' => $mora];
        } else {
            $interesRegla = $diasRest > 7 ? $intHoy : $intProg;
            $cancelar = ['capital' => $amort, 'interes' => $interesRegla, 'mora' => 0.0];
        }
        $cancelar['total'] = round($cancelar['capital'] + $cancelar['interes'] + $cancelar['mora'], 2);

        $renovarInt = $vencida ? $intProg : $intHoy;
        $renovar = ['interes' => $renovarInt, 'mora' => $mora, 'total' => round($renovarInt + $mora, 2)];

        return array_merge($base, [
            'vigente'      => true,
            'condicion'    => $vencida ? 'Vencido' : 'Vigente',
            'dias_atraso'  => $vencida ? (int) $vto->copy()->startOfDay()->diffInDays($ahora->copy()->startOfDay()) : 0,
            'vence'        => $vto,
            'cuota'        => $cuotaP,
            'capital'      => $amort,
            'interes_prog' => $intProg,
            'interes_hoy'  => $intHoy,
            'mora'         => $mora,
            'saldo'        => $saldo,
            'con_abonos'   => $conAbono,
            'cancelar'     => $cancelar,
            'renovar'      => $renovar,
        ]);
    }

    /** Copia de CrediJoyaController::interesDevengadoHastaHoy(). */
    private function interesHoy(Cronograma $c, Credito $credito): float
    {
        $vto = Carbon::parse($c->fecha);
        $hoy = now();
        $to  = $hoy->lessThan($vto) ? $hoy : $vto;

        $inicioPeriodo = $c->numero > 1
            ? optional(Cronograma::where('id_prestamo', $credito->id)->where('numero', $c->numero - 1)->first())->fecha
            : $credito->fecha_desembolso;
        $inicioPeriodo = Carbon::parse($inicioPeriodo);

        $diasPeriodo = max(1, $inicioPeriodo->diffInDays($vto));
        $diasDev     = max(0, $inicioPeriodo->diffInDays($to));
        $programado  = (float) ($c->interes ?? 0);
        $interes     = min($programado * ($diasDev / $diasPeriodo), $programado);

        return round($interes, 2);
    }

    private function concepto(Ingreso $p): string
    {
        $porModo = [
            'interes'      => 'Pago de interés (renueva)',
            'adelanto'     => 'Interés + adelanto de capital (renueva)',
            'cuota'        => 'Pago de cuota (cancela)',
            'totalhoy'     => 'Cancelación total',
            'parcialcuota' => 'Abono parcial',
        ];

        return $porModo[$p->modo] ?? ($p->tipo === 'abono' ? 'Abono parcial' : 'Pago');
    }
}
