<?php

namespace App\Services;

use App\Models\CorrelativoCredito;
use App\Models\Credito;
use App\Models\Cronograma;
use App\Models\Ingreso;
use App\Models\Sucursal;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Estado de cuenta de un crédito (individual o grupal).
 *
 * SOLO LECTURA: no crea ni modifica ingresos ni cronogramas.
 *
 * Usa las mismas reglas que la pantalla de cobro para que los números cuadren
 * con caja:
 *  - Cronograma::liquidada()  → si la cuota está cancelada.
 *  - Cronograma::saldoYMora() → saldo y mora vigente de cada cuota.
 *  - "Para cancelar hoy" replica CreditoController::detallePagoTotal()
 *    (cuotas vencidas completas, cuota en curso con interés devengado a la
 *    fecha, cuotas futuras solo capital). Si cambias esa fórmula en caja,
 *    cámbiala también aquí.
 *
 * En crédito grupal el estado del grupo se arma SUMANDO las cuotas de los
 * integrantes (igual que verpagocuota), nunca con los ingresos "generales".
 */
class EstadoCuentaService
{
    private Carbon $hoy;

    /** Vencimiento por número de cuota del crédito (para el periodo de interés). */
    private array $fechasPorNumero = [];

    public function __construct()
    {
        $this->hoy = Carbon::now()->startOfDay();
    }

    /**
     * Devuelve la lista de estados de cuenta a imprimir.
     *
     *  - Individual: un solo estado (el titular).
     *  - Grupal sin $soloCliente: estado consolidado del grupo + (opcional) uno por integrante.
     *  - Grupal con $soloCliente: solo ese integrante.
     */
    public function generar(Credito $credito, ?int $soloCliente = null, bool $conIntegrantes = true): array
    {
        $credito->loadMissing(['user.sucursal', 'creditoClientes.clientes']);

        $this->fechasPorNumero = Cronograma::where('id_prestamo', $credito->id)
            ->selectRaw('numero, MIN(fecha) as fecha')
            ->groupBy('numero')
            ->pluck('fecha', 'numero')
            ->toArray();

        // unique: hay grupos con el mismo cliente registrado dos veces en credito_cliente;
        // sus cuotas existen una sola vez, así que se cuenta una sola vez (igual que caja).
        $integrantes = $credito->creditoClientes
            ->filter(fn ($cc) => $cc->clientes)
            ->unique('cliente_id')
            ->values();

        if ($credito->categoria !== 'grupal') {
            $cc = $integrantes->first();
            abort_if(!$cc, 404, 'El crédito no tiene cliente asociado.');
            return [$this->deCliente($credito, $cc)];
        }

        if ($soloCliente) {
            $cc = $integrantes->firstWhere('cliente_id', $soloCliente);
            abort_if(!$cc, 404, 'El cliente no pertenece a este crédito.');
            return [$this->deCliente($credito, $cc)];
        }

        $porIntegrante = $integrantes->map(fn ($cc) => $this->deCliente($credito, $cc))->values();
        $estados = [$this->deGrupo($credito, $porIntegrante)];

        return $conIntegrantes ? array_merge($estados, $porIntegrante->all()) : $estados;
    }

    // ---------------------------------------------------------------------
    // Estado por cliente (titular o integrante)
    // ---------------------------------------------------------------------

    private function deCliente(Credito $credito, $creditoCliente): array
    {
        $cliente = $creditoCliente->clientes;

        $cuotas = Cronograma::where('id_prestamo', $credito->id)
            ->where('cliente_id', $cliente->id)
            ->orderBy('numero')
            ->orderBy('fecha')
            ->get();

        // Créditos individuales antiguos pueden tener el cronograma sin cliente_id.
        if ($cuotas->isEmpty() && $credito->categoria !== 'grupal') {
            $cuotas = Cronograma::where('id_prestamo', $credito->id)
                ->whereNull('cliente_id')
                ->orderBy('numero')
                ->orderBy('fecha')
                ->get();
        }

        $filas = $this->filasCuotas($credito, $cuotas);

        $pagosCapital = $this->filasPagoCapital($credito, $cliente->id);

        $desembolso = $credito->categoria === 'grupal'
            ? (float) $creditoCliente->monto_indivual
            : (float) ($credito->monto_total ?: $creditoCliente->monto_indivual);

        $correlativo = CorrelativoCredito::where('id_prestamo', $credito->id)
            ->where('id_cliente', $cliente->id)
            ->first();

        return $this->armar($credito, [
            'es_grupo'    => false,
            'cliente_id'  => $cliente->id,
            'titulo'      => $cliente->nombre,
            'cliente'     => $cliente->nombre,
            'documento'   => $cliente->documento_identidad,
            'codigo'      => $correlativo ? $correlativo->serie . '-' . $correlativo->correlativo : null,
            'desembolso'  => $desembolso,
            'integrantes' => [],
        ], $filas, $pagosCapital);
    }

    // ---------------------------------------------------------------------
    // Estado consolidado del grupo
    // ---------------------------------------------------------------------

    private function deGrupo(Credito $credito, Collection $porIntegrante): array
    {
        // Sumar por número de cuota las filas "P" de todos los integrantes.
        $porNumero = [];
        foreach ($porIntegrante as $estado) {
            foreach ($estado['filas'] as $f) {
                if ($f['tipo'] !== 'P') {
                    continue;
                }
                $porNumero[$f['numero']][] = $f;
            }
        }
        ksort($porNumero);

        $filas = [];
        foreach ($porNumero as $numero => $grupo) {
            $filas[] = $this->sumarFilas($numero, $grupo);
        }

        $pagosCapital = $this->filasPagoCapital($credito, null);

        $correlativo = CorrelativoCredito::where('id_prestamo', $credito->id)
            ->whereNull('id_cliente')
            ->first();

        $integrantes = $porIntegrante->map(fn ($e) => [
            'cliente_id'    => $e['cabecera']['cliente_id'],
            'nombre'        => $e['cabecera']['cliente'],
            'documento'     => $e['cabecera']['documento'],
            'desembolso'    => $e['cabecera']['desembolso'],
            'pagado'        => $e['totales']['pagado'],
            'saldo'         => $e['saldo']['total'],
            'mora'          => $e['saldo']['mora'],
            'cancelar'      => $e['cancelar']['total'],
            'dias_atraso'   => $e['cabecera']['dias_atraso'],
            'condicion'     => $e['cabecera']['condicion'],
        ])->all();

        return $this->armar($credito, [
            'es_grupo'    => true,
            'cliente_id'  => null,
            'titulo'      => $credito->nombre_prestamo ?: 'Grupo #' . $credito->id,
            'cliente'     => $credito->nombre_prestamo ?: 'Grupo #' . $credito->id,
            'documento'   => null,
            'codigo'      => $correlativo ? $correlativo->serie . '-' . $correlativo->correlativo : null,
            'desembolso'  => (float) $credito->monto_total,
            'integrantes' => $integrantes,
        ], $filas, $pagosCapital);
    }

    private function sumarFilas(int $numero, array $grupo): array
    {
        $suma = fn (string $k) => round(array_sum(array_column($grupo, $k)), 2);

        $todas    = count($grupo);
        $liquidas = count(array_filter($grupo, fn ($f) => $f['liquidada']));
        $conPago  = count(array_filter($grupo, fn ($f) => $f['pagado'] > 0));
        $vto      = $grupo[0]['vencimiento'];
        $vencida  = $this->hoy->greaterThan($vto);
        $fechas   = array_filter(array_column($grupo, 'fecha_pago'));

        if ($liquidas === $todas) {
            $estado = 'Pagado';
            $atraso = max(array_column($grupo, 'atraso'));
        } else {
            $estado = ($liquidas > 0 || $conPago > 0) ? 'Parcial' : ($vencida ? 'Vencido' : 'Pendiente');
            $atraso = $vencida ? (int) $vto->diffInDays($this->hoy) : 0;
        }

        return [
            'tipo'             => 'P',
            'numero'           => $numero,
            'vencimiento'      => $vto,
            'fecha_pago'       => $fechas ? max($fechas) : null,
            'atraso'           => $atraso,
            'capital'          => $suma('capital'),
            'interes'          => $suma('interes'),
            'cuota'            => $suma('cuota'),
            'mora'             => $suma('mora'),
            'mora_pagada'      => $suma('mora_pagada'),
            'mora_pendiente'   => $suma('mora_pendiente'),
            'pagado'           => $suma('pagado'),
            'saldo'            => $suma('saldo'),
            'pendiente'        => $suma('pendiente'),
            'capital_pend'     => $suma('capital_pend'),
            'interes_pend'     => $suma('interes_pend'),
            'cancelar_capital' => $suma('cancelar_capital'),
            'cancelar_interes' => $suma('cancelar_interes'),
            'cancelar_mora'    => $suma('cancelar_mora'),
            'liquidada'        => $liquidas === $todas,
            'vencida'          => $vencida,
            'estado'           => $estado,
            'detalle'          => $estado === 'Parcial' ? $liquidas . '/' . $todas . ' integrantes pagaron' : null,
        ];
    }

    // ---------------------------------------------------------------------
    // Filas
    // ---------------------------------------------------------------------

    /** Una fila "P" por cuota del cronograma, con lo pagado y lo pendiente. */
    private function filasCuotas(Credito $credito, Collection $cuotas): array
    {
        $ingresosPorCuota = Ingreso::whereIn('cronograma_id', $cuotas->pluck('id'))
            ->orderBy('fecha_pago')
            ->orderBy('id')
            ->get()
            // Los pagos a capital se muestran en su propia fila, no dentro de la cuota.
            ->reject(fn ($i) => $this->esPagoCapital($i))
            ->groupBy('cronograma_id');

        $filas = [];
        foreach ($cuotas as $cuota) {
            $ingresos = $ingresosPorCuota->get($cuota->id, collect());
            $vto       = Carbon::parse($cuota->fecha)->startOfDay();
            $vencida   = $this->hoy->greaterThan($vto);
            $liquidada = $cuota->liquidada();
            $est       = $cuota->saldoYMora();

            $pagado      = round((float) $ingresos->sum('monto'), 2);
            $moraPagada  = round((float) $ingresos->sum('monto_mora'), 2);
            $pagadoCuota = max(0.0, round($pagado - $moraPagada, 2));
            $ultimoPago  = optional($ingresos->last())->fecha_pago;

            if ($liquidada) {
                $saldo   = 0.0;
                $moraPen = 0.0;
                $estado  = 'Pagado';
                $atraso  = $ultimoPago ? max(0, (int) $vto->diffInDays(Carbon::parse($ultimoPago)->startOfDay(), false)) : 0;
            } else {
                $saldo   = (float) $est['saldo'];
                $moraPen = (float) $est['mora'];
                $estado  = $ingresos->isNotEmpty() ? 'Parcial' : ($vencida ? 'Vencido' : 'Pendiente');
                $atraso  = $vencida ? (int) $vto->diffInDays($this->hoy) : 0;
            }

            // El saldo de la cuota se reparte: primero interés, luego capital.
            $interesPend = round(min(max((float) $cuota->interes - $pagadoCuota, 0), $saldo), 2);
            $capitalPend = round(max($saldo - $interesPend, 0), 2);

            $filas[] = [
                'tipo'             => 'P',
                'numero'           => (int) $cuota->numero,
                'vencimiento'      => $vto,
                'fecha_pago'       => $ultimoPago,
                'atraso'           => $atraso,
                'capital'          => round((float) $cuota->amortizacion, 2),
                'interes'          => round((float) $cuota->interes, 2),
                'cuota'            => round((float) $cuota->monto, 2),
                'mora'             => round($moraPagada + $moraPen, 2),
                'mora_pagada'      => $moraPagada,
                'mora_pendiente'   => round($moraPen, 2),
                'pagado'           => $pagado,
                'saldo'            => round($saldo, 2),
                'pendiente'        => round($saldo + $moraPen, 2),
                'capital_pend'     => $capitalPend,
                'interes_pend'     => $interesPend,
                'liquidada'        => $liquidada,
                'vencida'          => $vencida,
                'estado'           => $estado,
                'detalle'          => null,
                '_cuota'           => $cuota,
            ];
        }

        return $this->calcularCancelacion($credito, $filas);
    }

    /**
     * Monto para cancelar HOY cada cuota (misma fórmula que el "pago total" de caja).
     */
    private function calcularCancelacion(Credito $credito, array $filas): array
    {
        // La cuota en curso: la primera no liquidada que aún no vence.
        $enCurso = null;
        foreach ($filas as $f) {
            if (!$f['liquidada'] && !$f['vencida']) {
                $enCurso = $f['numero'];
                break;
            }
        }

        foreach ($filas as &$f) {
            $f['cancelar_capital'] = 0.0;
            $f['cancelar_interes'] = 0.0;
            $f['cancelar_mora']    = 0.0;

            if ($f['liquidada']) {
                unset($f['_cuota']);
                continue;
            }

            $cuota  = $f['_cuota'];
            $saldo  = $f['saldo'];
            // Lo ya abonado a la cuota (igual que en caja).
            $pagado = max(0.0, round($f['cuota'] - $saldo, 2));

            if ($f['vencida']) {
                $base      = $saldo;
                $intPart   = $f['interes_pend'];
            } else {
                $intDev = 0.0;
                if ($f['numero'] === $enCurso) {
                    $inicio      = $this->inicioPeriodo($cuota, $credito);
                    $vence       = $f['vencimiento'];
                    $diasPeriodo = (int) $inicio->diffInDays($vence);
                    if ($diasPeriodo > 0) {
                        // Igual que caja: diferencia en días SIN signo. Si el periodo aún no
                        // empieza (cuota anterior pagada por adelantado) caja igual cuenta
                        // esos días; se replica para que el monto coincida con lo que cobra.
                        $diasInteres = min($diasPeriodo, (int) $inicio->diffInDays($this->hoy));
                        $intDev      = round($f['interes'] * $diasInteres / $diasPeriodo, 2);
                    } else {
                        $intDev = $f['interes'];
                    }
                }
                $base    = max(0.0, round($f['capital'] + $intDev - $pagado, 2));
                $intPart = max(0.0, round($intDev - $pagado, 2));
            }

            $base    = round(min($base, $saldo), 2);
            $intPart = round(min($intPart, $base), 2);

            $f['cancelar_capital'] = round($base - $intPart, 2);
            $f['cancelar_interes'] = $intPart;
            $f['cancelar_mora']    = $f['mora_pendiente'];

            unset($f['_cuota']);
        }
        unset($f);

        return $filas;
    }

    /** Inicio del periodo de una cuota (copia de CreditoController::inicioPeriodoCuota). */
    private function inicioPeriodo(Cronograma $cuota, Credito $credito): Carbon
    {
        $numero = (int) $cuota->numero;

        if (!empty($this->fechasPorNumero[$numero - 1])) {
            return Carbon::parse($this->fechasPorNumero[$numero - 1])->startOfDay();
        }

        if ($credito->fecha_desembolso) {
            return Carbon::parse($credito->fecha_desembolso)->startOfDay()
                ->addDays((int) ($credito->periodo_gracia_dias ?? 0));
        }

        $vence = Carbon::parse($cuota->fecha)->startOfDay();
        $dias  = 30;
        if (!empty($this->fechasPorNumero[$numero + 1])) {
            $dias = (int) $vence->diffInDays(Carbon::parse($this->fechasPorNumero[$numero + 1])->startOfDay());
        }

        return $vence->copy()->subDays($dias > 0 ? $dias : 30);
    }

    /** Filas "K": pagos adelantados a capital (Pago Capital en caja). */
    private function filasPagoCapital(Credito $credito, ?int $clienteId): array
    {
        $q = Ingreso::where('prestamo_id', $credito->id)->orderBy('fecha_pago')->orderBy('id');
        $clienteId ? $q->where('cliente_id', $clienteId) : $q->whereNull('cliente_id');

        return $q->get()
            ->filter(fn ($i) => $this->esPagoCapital($i))
            ->map(function ($i) {
                $interes = round((float) $i->interes_pago_capital, 2);
                $monto   = round((float) $i->monto, 2);
                return [
                    'tipo'           => 'K',
                    'numero'         => (int) $i->numero_cuota,
                    'vencimiento'    => null,
                    'fecha_pago'     => $i->fecha_pago,
                    'atraso'         => 0,
                    'capital'        => round(max($monto - $interes, 0), 2),
                    'interes'        => $interes,
                    'cuota'          => 0.0,
                    'mora'           => 0.0,
                    'mora_pagada'    => 0.0,
                    'mora_pendiente' => 0.0,
                    'pagado'         => $monto,
                    'saldo'          => 0.0,
                    'pendiente'      => 0.0,
                    'liquidada'      => true,
                    'vencida'        => false,
                    'estado'         => 'Pago capital',
                    'detalle'        => (int) $i->pago_capital === 1 ? 'Reduce cuota' : 'Reduce plazo',
                ];
            })
            ->values()
            ->all();
    }

    private function esPagoCapital($ingreso): bool
    {
        return in_array((int) $ingreso->pago_capital, [1, 2], true);
    }

    // ---------------------------------------------------------------------
    // Ensamblado final: cabecera, totales y resúmenes
    // ---------------------------------------------------------------------

    private function armar(Credito $credito, array $cab, array $filas, array $pagosCapital): array
    {
        // Intercalar cada pago a capital después de la cuota con la que se hizo.
        $todas = [];
        foreach ($filas as $f) {
            $todas[] = $f;
            foreach ($pagosCapital as $k => $pc) {
                if ($pc['numero'] <= $f['numero']) {
                    $todas[] = $pc;
                    unset($pagosCapital[$k]);
                }
            }
        }
        foreach ($pagosCapital as $pc) {
            $todas[] = $pc;
        }

        $cuotas = array_values(array_filter($filas, fn ($f) => $f['tipo'] === 'P'));
        $sum    = fn (array $rows, string $k) => round(array_sum(array_column($rows, $k)), 2);

        $totales = [
            'capital'   => $sum($todas, 'capital'),
            'interes'   => $sum($todas, 'interes'),
            'cuota'     => $sum($cuotas, 'cuota'),
            'mora'      => $sum($todas, 'mora'),
            'pagado'    => $sum($todas, 'pagado'),
            'pendiente' => $sum($todas, 'pendiente'),
        ];

        $saldo = [
            'capital' => $sum($cuotas, 'capital_pend'),
            'interes' => $sum($cuotas, 'interes_pend'),
            'mora'    => $sum($cuotas, 'mora_pendiente'),
        ];
        $saldo['total'] = round($saldo['capital'] + $saldo['interes'] + $saldo['mora'], 2);

        $cancelar = [
            'capital' => $sum($cuotas, 'cancelar_capital'),
            'interes' => $sum($cuotas, 'cancelar_interes'),
            'mora'    => $sum($cuotas, 'cancelar_mora'),
        ];
        $cancelar['total'] = round($cancelar['capital'] + $cancelar['interes'] + $cancelar['mora'], 2);

        $pendientes = array_values(array_filter($cuotas, fn ($f) => !$f['liquidada']));
        $vencidas   = array_values(array_filter($pendientes, fn ($f) => $f['vencida']));
        $proxima    = $pendientes[0] ?? null;

        $diasAtraso = $vencidas ? max(array_column($vencidas, 'atraso')) : 0;

        if (empty($cuotas)) {
            $condicion = 'Sin cronograma';
        } elseif (empty($pendientes)) {
            $condicion = 'Cancelado';
        } elseif ($vencidas) {
            $condicion = 'Vencido';
        } else {
            $condicion = 'Normal';
        }

        $fechasPago = array_filter(array_column($todas, 'fecha_pago'));

        $sucursal = optional(optional($credito->user)->sucursal)->nombre
            ?? optional(Sucursal::first())->nombre;

        return [
            'cabecera' => $cab + [
                'operacion'        => $credito->id,
                'fecha_desembolso' => $credito->fecha_desembolso ? Carbon::parse($credito->fecha_desembolso) : null,
                'modalidad'        => $credito->categoria === 'grupal' ? 'Crédito Grupal' : 'Crédito Individual',
                'producto'         => ucfirst(trim(implode(' - ', array_filter([$credito->producto, $credito->subproducto])))),
                'tipo'             => $credito->tipo,
                'frecuencia'       => $credito->recurrencia,
                'num_cuotas'       => count($cuotas),
                'tea'              => $credito->tasa,
                'oficina'          => $sucursal,
                'asesor'           => optional($credito->user)->name,
                'grupo'            => $credito->categoria === 'grupal' ? $credito->nombre_prestamo : null,
                'condicion'        => $condicion,
                'dias_atraso'      => $diasAtraso,
                'proximo_venc'     => $proxima ? $proxima['vencimiento'] : null,
                'proximo_monto'    => $proxima ? $proxima['pendiente'] : null,
                'fecha_fin'        => $cuotas ? end($cuotas)['vencimiento'] : null,
            ],
            'filas'    => $todas,
            'totales'  => $totales,
            'saldo'    => $saldo,
            'cancelar' => $cancelar,
            'pagos'    => [
                'cuotas_pagadas' => count($cuotas) - count($pendientes),
                'cuotas_total'   => count($cuotas),
                'cuotas_vencidas'=> count($vencidas),
                'total_pagado'   => $totales['pagado'],
                'mora_pagada'    => $sum($todas, 'mora_pagada'),
                'ultimo_pago'    => $fechasPago ? Carbon::parse(max($fechasPago)) : null,
            ],
        ];
    }
}
