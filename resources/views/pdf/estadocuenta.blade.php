<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Estado de Cuenta - Crédito {{ $credito->id }}</title>
    <style>
        @page { margin: 34px 30px 40px 30px; }

        * { box-sizing: border-box; }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9px;
            color: #1f2933;
            margin: 0;
        }
        table { width: 100%; border-collapse: collapse; }

        /* ---------- Encabezado ---------- */
        .head td { vertical-align: middle; }
        .logo-box {
            width: 92px;
            background: #0b4a6b;
            padding: 5px 8px;
            text-align: center;
        }
        .logo-box img { width: 76px; }
        .titulo {
            padding-left: 14px;
        }
        .titulo h1 {
            margin: 0;
            font-size: 20px;
            color: #0b4a6b;
            letter-spacing: 0.5px;
        }
        .titulo .sub {
            font-size: 10px;
            color: #52606d;
            margin-top: 2px;
        }
        .fecha-box { width: 210px; text-align: right; }
        .fecha-box .lbl { font-size: 8px; color: #7b8794; text-transform: uppercase; }
        .fecha-box .val { font-size: 11px; font-weight: bold; color: #1f2933; }

        .cond {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 9px;
            margin-top: 4px;
        }
        .cond-normal    { background: #e3f4e8; color: #1e7b3c; }
        .cond-vencido   { background: #fde8e8; color: #b42318; }
        .cond-cancelado { background: #e1ecf3; color: #0b4a6b; }
        .cond-sin       { background: #eceff1; color: #52606d; }

        .regla { height: 3px; background: #e8962a; margin: 8px 0 8px 0; }

        /* ---------- Datos del crédito ---------- */
        .info td { padding: 2px 4px; font-size: 9px; vertical-align: top; }
        .info .l { color: #7b8794; width: 9%; white-space: nowrap; }
        .info .v { font-weight: bold; width: 16%; }

        /* ---------- Tabla de cuotas ---------- */
        .cuotas { margin-top: 10px; }
        .cuotas th {
            background: #0b4a6b;
            color: #fff;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 4px;
            text-align: center;
        }
        .cuotas td {
            padding: 4px 4px;
            border-bottom: 1px solid #e4e7eb;
            font-size: 8.5px;
            text-align: center;
        }
        .cuotas td.num, .cuotas th.num { text-align: right; }
        .cuotas tr.fila-d td { background: #f3f6f9; color: #52606d; font-style: italic; }
        .cuotas tr.fila-k td { background: #fff8ec; }
        .cuotas tr.total td {
            border-top: 1.5px solid #0b4a6b;
            border-bottom: none;
            font-weight: bold;
            background: #f8fafc;
            padding-top: 5px;
        }
        .cuotas .det { display: block; font-size: 7px; color: #7b8794; font-style: normal; }
        .muted { color: #9aa5b1; }
        .rojo { color: #b42318; font-weight: bold; }
        .fuerte { font-weight: bold; }

        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 8px;
            font-size: 7.5px;
            font-weight: bold;
        }
        .b-pagado    { background: #e3f4e8; color: #1e7b3c; }
        .b-parcial   { background: #e5edfc; color: #1d4ed8; }
        .b-vencido   { background: #fde8e8; color: #b42318; }
        .b-pendiente { background: #eceff1; color: #52606d; }
        .b-capital   { background: #fdebd0; color: #a35a00; }
        .b-desemb    { background: #e1ecf3; color: #0b4a6b; }

        /* ---------- Resúmenes ---------- */
        .resumenes { margin-top: 14px; }
        .resumenes > tbody > tr > td { vertical-align: top; padding: 0; }
        .caja {
            border: 1px solid #d9e2ec;
            border-radius: 4px;
        }
        .caja .cab {
            background: #f0f4f8;
            color: #0b4a6b;
            font-weight: bold;
            font-size: 8.5px;
            text-transform: uppercase;
            padding: 5px 8px;
            border-bottom: 1px solid #d9e2ec;
        }
        .caja .cab.naranja { background: #fdf1e1; color: #a35a00; }
        .caja table td { padding: 3px 8px; font-size: 9px; }
        .caja table td.r { text-align: right; font-weight: bold; }
        .caja table tr.t td {
            border-top: 1px dashed #9aa5b1;
            font-size: 10.5px;
            font-weight: bold;
            color: #0b4a6b;
            padding-top: 4px;
        }
        .caja .nota { font-size: 7px; color: #7b8794; padding: 0 8px 5px 8px; }

        /* ---------- Integrantes (grupal) ---------- */
        .subtitulo {
            margin: 14px 0 4px 0;
            font-size: 10px;
            font-weight: bold;
            color: #0b4a6b;
            text-transform: uppercase;
        }
        .integrantes th {
            background: #f0f4f8;
            color: #0b4a6b;
            font-size: 7.5px;
            text-transform: uppercase;
            padding: 4px;
            border-bottom: 1px solid #d9e2ec;
        }
        .integrantes td {
            padding: 3px 4px;
            border-bottom: 1px solid #e4e7eb;
            font-size: 8.5px;
        }
        .integrantes .num { text-align: right; }
        .integrantes .c { text-align: center; }

        /* ---------- Notas y pie ---------- */
        .notas { margin-top: 10px; font-size: 7.5px; color: #616e7c; line-height: 1.45; }

        .pie {
            position: fixed;
            bottom: -26px;
            left: 0;
            right: 0;
            font-size: 7px;
            color: #9aa5b1;
            border-top: 1px solid #e4e7eb;
            padding-top: 4px;
        }
        .pie .pag:after { content: "Página " counter(page); }
    </style>
</head>

<body>
    @php
        $n = fn ($v) => number_format((float) $v, 2, '.', ',');
        $f = fn ($d) => $d ? \Carbon\Carbon::parse($d)->format('d/m/Y') : '—';
        $claseEstado = [
            'Pagado'       => 'b-pagado',
            'Parcial'      => 'b-parcial',
            'Vencido'      => 'b-vencido',
            'Pendiente'    => 'b-pendiente',
            'Pago capital' => 'b-capital',
        ];
        $claseCond = [
            'Normal'         => 'cond-normal',
            'Vencido'        => 'cond-vencido',
            'Cancelado'      => 'cond-cancelado',
            'Sin cronograma' => 'cond-sin',
        ];
    @endphp

    <div class="pie">
        <table>
            <tr>
                <td>Grupo CrediPalmo · Documento informativo, no es comprobante de pago.
                    Emitido el {{ $emitido->format('d/m/Y H:i') }}{{ $usuario ? ' por ' . $usuario : '' }}.</td>
                <td style="text-align:right;" class="pag"></td>
            </tr>
        </table>
    </div>

    @foreach ($estados as $e)
        @php
            $c = $e['cabecera'];
            $esGrupo = $c['es_grupo'];
        @endphp
        <div @if (!$loop->last) style="page-break-after: always;" @endif>

            {{-- ================= Encabezado ================= --}}
            <table class="head">
                <tr>
                    @if ($logo)
                        <td class="logo-box"><img src="{{ $logo }}" alt="Grupo CrediPalmo"></td>
                    @endif
                    <td class="titulo">
                        <h1>ESTADO DE CUENTA</h1>
                        <div class="sub">
                            {{ $c['modalidad'] }}
                            @if ($esGrupo)
                                · Consolidado del grupo
                            @elseif ($credito->categoria === 'grupal')
                                · Integrante
                            @endif
                            · Operación N° {{ $c['operacion'] }}
                        </div>
                    </td>
                    <td class="fecha-box">
                        <div class="lbl">A fecha</div>
                        <div class="val">{{ $emitido->format('d/m/Y') }}</div>
                        <span class="cond {{ $claseCond[$c['condicion']] ?? 'cond-sin' }}">
                            {{ $c['condicion'] }}{{ $c['condicion'] === 'Vencido' ? ' · ' . $c['dias_atraso'] . ' días' : '' }}
                        </span>
                    </td>
                </tr>
            </table>

            <div class="regla"></div>

            {{-- ================= Datos del crédito ================= --}}
            <table class="info">
                <tr>
                    <td class="l">{{ $esGrupo ? 'Grupo:' : 'Cliente:' }}</td>
                    <td class="v" colspan="3">{{ $c['cliente'] }}</td>
                    <td class="l">{{ $esGrupo ? 'Integrantes:' : 'DNI / RUC:' }}</td>
                    <td class="v">{{ $esGrupo ? count($c['integrantes']) : ($c['documento'] ?: '—') }}</td>
                    <td class="l">Oficina:</td>
                    <td class="v">{{ $c['oficina'] ?: '—' }}</td>
                </tr>
                <tr>
                    <td class="l">Operación:</td>
                    <td class="v">{{ $c['operacion'] }}{{ $c['codigo'] ? '  (' . $c['codigo'] . ')' : '' }}</td>
                    <td class="l">Desembolso:</td>
                    <td class="v">S/ {{ $n($c['desembolso']) }}</td>
                    <td class="l">Fecha desemb.:</td>
                    <td class="v">{{ $f($c['fecha_desembolso']) }}</td>
                    <td class="l">Asesor:</td>
                    <td class="v">{{ $c['asesor'] ?: '—' }}</td>
                </tr>
                <tr>
                    <td class="l">Producto:</td>
                    <td class="v">{{ $c['producto'] ?: $c['modalidad'] }}</td>
                    <td class="l">Frecuencia:</td>
                    <td class="v">{{ ucfirst($c['frecuencia'] ?? '—') }} · {{ $c['num_cuotas'] }} cuotas</td>
                    <td class="l">TEA:</td>
                    <td class="v">{{ $c['tea'] !== null ? rtrim(rtrim(number_format((float) $c['tea'], 2, '.', ''), '0'), '.') . '%' : '—' }}</td>
                    <td class="l">Vence crédito:</td>
                    <td class="v">{{ $f($c['fecha_fin']) }}</td>
                </tr>
                <tr>
                    @if (!$esGrupo && $c['grupo'])
                        <td class="l">Grupo:</td>
                        <td class="v">{{ $c['grupo'] }}</td>
                    @else
                        <td class="l">Tipo:</td>
                        <td class="v">{{ $c['tipo'] ? ucfirst($c['tipo']) : '—' }}</td>
                    @endif
                    <td class="l">Próx. venc.:</td>
                    <td class="v">
                        @if ($c['proximo_venc'])
                            {{ $f($c['proximo_venc']) }} · S/ {{ $n($c['proximo_monto']) }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="l">Días atraso:</td>
                    <td class="v {{ $c['dias_atraso'] > 0 ? 'rojo' : '' }}">{{ $c['dias_atraso'] }}</td>
                    <td class="l">Último pago:</td>
                    <td class="v">{{ $f($e['pagos']['ultimo_pago']) }}</td>
                </tr>
            </table>

            {{-- ================= Cuotas ================= --}}
            <table class="cuotas">
                <thead>
                    <tr>
                        <th style="width:4%;">Nro</th>
                        <th style="width:4%;">D/P</th>
                        <th style="width:8%;">Vencimiento</th>
                        <th style="width:8%;">Fecha pago</th>
                        <th style="width:5%;">Atraso</th>
                        <th class="num" style="width:9%;">Capital</th>
                        <th class="num" style="width:8%;">Interés</th>
                        <th class="num" style="width:7%;">Mora</th>
                        <th class="num" style="width:9%;">Cuota</th>
                        <th class="num" style="width:10%;">Total pagado</th>
                        <th class="num" style="width:10%;">Total pendiente</th>
                        <th style="width:12%;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Desembolso (no suma en los totales) --}}
                    <tr class="fila-d">
                        <td>0</td>
                        <td>D</td>
                        <td>—</td>
                        <td>{{ $f($c['fecha_desembolso']) }}</td>
                        <td>—</td>
                        <td class="num">{{ $n($c['desembolso']) }}*</td>
                        <td class="num">—</td>
                        <td class="num">—</td>
                        <td class="num">—</td>
                        <td class="num">—</td>
                        <td class="num">—</td>
                        <td><span class="badge b-desemb">Desembolsado</span></td>
                    </tr>

                    @forelse ($e['filas'] as $fila)
                        @php $esK = $fila['tipo'] === 'K'; @endphp
                        <tr class="{{ $esK ? 'fila-k' : '' }}">
                            <td>{{ $fila['numero'] }}</td>
                            <td>{{ $esK ? 'K' : 'P' }}</td>
                            <td>{{ $esK ? '—' : $f($fila['vencimiento']) }}</td>
                            <td>{{ $fila['fecha_pago'] ? $f($fila['fecha_pago']) : '—' }}</td>
                            <td class="{{ $fila['atraso'] > 0 && !$fila['liquidada'] ? 'rojo' : '' }}">
                                {{ $esK ? '—' : $fila['atraso'] }}
                            </td>
                            <td class="num">{{ $n($fila['capital']) }}</td>
                            <td class="num">{{ $n($fila['interes']) }}</td>
                            <td class="num {{ $fila['mora'] > 0 ? '' : 'muted' }}">{{ $n($fila['mora']) }}</td>
                            <td class="num">{{ $esK ? '—' : $n($fila['cuota']) }}</td>
                            <td class="num {{ $fila['pagado'] > 0 ? '' : 'muted' }}">{{ $n($fila['pagado']) }}</td>
                            <td class="num {{ $fila['pendiente'] > 0 ? 'fuerte' : 'muted' }}">{{ $n($fila['pendiente']) }}</td>
                            <td>
                                <span class="badge {{ $claseEstado[$fila['estado']] ?? 'b-pendiente' }}">{{ $fila['estado'] }}</span>
                                @if ($fila['detalle'])
                                    <span class="det">{{ $fila['detalle'] }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="muted">Este crédito no tiene cuotas registradas en el cronograma.</td>
                        </tr>
                    @endforelse

                    <tr class="total">
                        <td colspan="5" style="text-align:right;">TOTALES</td>
                        <td class="num">{{ $n($e['totales']['capital']) }}</td>
                        <td class="num">{{ $n($e['totales']['interes']) }}</td>
                        <td class="num">{{ $n($e['totales']['mora']) }}</td>
                        <td class="num">{{ $n($e['totales']['cuota']) }}</td>
                        <td class="num">{{ $n($e['totales']['pagado']) }}</td>
                        <td class="num">{{ $n($e['totales']['pendiente']) }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            {{-- ================= Resúmenes ================= --}}
            <table class="resumenes">
                <tr>
                    <td style="width:32%;">
                        <div class="caja">
                            <div class="cab">Saldo pendiente según cronograma</div>
                            <table>
                                <tr><td>Capital pendiente</td><td class="r">{{ $n($e['saldo']['capital']) }}</td></tr>
                                <tr><td>Interés pendiente</td><td class="r">{{ $n($e['saldo']['interes']) }}</td></tr>
                                <tr><td>Mora pendiente</td><td class="r">{{ $n($e['saldo']['mora']) }}</td></tr>
                                <tr class="t"><td>TOTAL S/</td><td class="r">{{ $n($e['saldo']['total']) }}</td></tr>
                            </table>
                        </div>
                    </td>
                    <td style="width:2%;"></td>
                    <td style="width:32%;">
                        <div class="caja">
                            <div class="cab naranja">Para cancelar hoy {{ $emitido->format('d/m/Y') }}</div>
                            <table>
                                <tr><td>Capital</td><td class="r">{{ $n($e['cancelar']['capital']) }}</td></tr>
                                <tr><td>Interés devengado</td><td class="r">{{ $n($e['cancelar']['interes']) }}</td></tr>
                                <tr><td>Mora</td><td class="r">{{ $n($e['cancelar']['mora']) }}</td></tr>
                                <tr class="t"><td>TOTAL S/</td><td class="r">{{ $n($e['cancelar']['total']) }}</td></tr>
                            </table>
                            <div class="nota">Cancelación anticipada: no cobra el interés de los días que aún no corren.</div>
                        </div>
                    </td>
                    <td style="width:2%;"></td>
                    <td style="width:32%;">
                        <div class="caja">
                            <div class="cab">Resumen de pagos</div>
                            <table>
                                <tr><td>Cuotas pagadas</td><td class="r">{{ $e['pagos']['cuotas_pagadas'] }} de {{ $e['pagos']['cuotas_total'] }}</td></tr>
                                <tr><td>Cuotas vencidas</td><td class="r {{ $e['pagos']['cuotas_vencidas'] ? 'rojo' : '' }}">{{ $e['pagos']['cuotas_vencidas'] }}</td></tr>
                                <tr><td>Mora pagada</td><td class="r">{{ $n($e['pagos']['mora_pagada']) }}</td></tr>
                                <tr class="t"><td>TOTAL PAGADO S/</td><td class="r">{{ $n($e['pagos']['total_pagado']) }}</td></tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>

            {{-- ================= Integrantes (solo hoja del grupo) ================= --}}
            @if ($esGrupo && count($c['integrantes']))
                <div class="subtitulo">Detalle por integrante</div>
                <table class="integrantes">
                    <thead>
                        <tr>
                            <th style="width:4%;">#</th>
                            <th style="text-align:left;">Integrante</th>
                            <th style="width:10%;">DNI</th>
                            <th class="num" style="width:10%;">Desembolso</th>
                            <th class="num" style="width:10%;">Total pagado</th>
                            <th class="num" style="width:10%;">Saldo cronograma</th>
                            <th class="num" style="width:9%;">Mora pend.</th>
                            <th class="num" style="width:11%;">Cancelar hoy</th>
                            <th style="width:6%;">Atraso</th>
                            <th style="width:9%;">Condición</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($c['integrantes'] as $i => $m)
                            <tr>
                                <td class="c">{{ $i + 1 }}</td>
                                <td>{{ $m['nombre'] }}</td>
                                <td class="c">{{ $m['documento'] ?: '—' }}</td>
                                <td class="num">{{ $n($m['desembolso']) }}</td>
                                <td class="num">{{ $n($m['pagado']) }}</td>
                                <td class="num">{{ $n($m['saldo']) }}</td>
                                <td class="num">{{ $n($m['mora']) }}</td>
                                <td class="num fuerte">{{ $n($m['cancelar']) }}</td>
                                <td class="c {{ $m['dias_atraso'] > 0 ? 'rojo' : '' }}">{{ $m['dias_atraso'] }}</td>
                                <td class="c">
                                    <span class="badge {{ ['Normal' => 'b-pagado', 'Vencido' => 'b-vencido', 'Cancelado' => 'b-desemb'][$m['condicion']] ?? 'b-pendiente' }}">{{ $m['condicion'] }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            {{-- ================= Notas ================= --}}
            <div class="notas">
                (*) El monto del desembolso NO se considera en los totales.
                D = desembolso · P = cuota del cronograma · K = pago adelantado a capital.
                "Atraso" son los días entre el vencimiento y la fecha de pago (o hoy, si la cuota sigue pendiente).
                La mora vigente se calcula a 1.5 por mil diario sobre el saldo vencido de cada cuota.
                @if ($esGrupo)
                    Los montos del grupo son la suma de los integrantes; cada integrante tiene su propia hoja.
                @endif
                Montos en soles (S/).
            </div>
        </div>
    @endforeach
</body>

</html>
