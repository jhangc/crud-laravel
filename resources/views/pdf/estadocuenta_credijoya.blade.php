<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Estado de Cuenta CrediJoya - Crédito {{ $credito->id }}</title>
    <style>
        @page { margin: 34px 30px 40px 30px; }
        * { box-sizing: border-box; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 9px; color: #1f2933; margin: 0; }
        table { width: 100%; border-collapse: collapse; }

        .head td { vertical-align: middle; }
        .logo-box { width: 92px; background: #0b4a6b; padding: 5px 8px; text-align: center; }
        .logo-box img { width: 76px; }
        .titulo { padding-left: 14px; }
        .titulo h1 { margin: 0; font-size: 20px; color: #0b4a6b; letter-spacing: 0.5px; }
        .titulo .sub { font-size: 10px; color: #52606d; margin-top: 2px; }
        .fecha-box { width: 210px; text-align: right; }
        .fecha-box .lbl { font-size: 8px; color: #7b8794; text-transform: uppercase; }
        .fecha-box .val { font-size: 11px; font-weight: bold; }
        .cond { display: inline-block; padding: 3px 10px; border-radius: 10px; font-weight: bold; font-size: 9px; margin-top: 4px; }
        .cond-vigente   { background: #e3f4e8; color: #1e7b3c; }
        .cond-vencido   { background: #fde8e8; color: #b42318; }
        .cond-cancelado { background: #e1ecf3; color: #0b4a6b; }
        .regla { height: 3px; background: #e8962a; margin: 8px 0; }

        .info td { padding: 2px 4px; font-size: 9px; vertical-align: top; }
        .info .l { color: #7b8794; width: 9%; white-space: nowrap; }
        .info .v { font-weight: bold; width: 16%; }

        .subtitulo { margin: 12px 0 4px 0; font-size: 10px; font-weight: bold; color: #0b4a6b; text-transform: uppercase; }
        .tabla th { background: #0b4a6b; color: #fff; font-size: 7.5px; font-weight: bold; text-transform: uppercase; padding: 5px 4px; text-align: center; }
        .tabla td { padding: 4px; border-bottom: 1px solid #e4e7eb; font-size: 8.5px; text-align: center; }
        .tabla .num { text-align: right; }
        .tabla .izq { text-align: left; }
        .tabla tr.total td { border-top: 1.5px solid #0b4a6b; border-bottom: none; font-weight: bold; background: #f8fafc; }
        .muted { color: #9aa5b1; }
        .det { display: block; font-size: 7px; color: #7b8794; }

        .badge { display: inline-block; padding: 1px 6px; border-radius: 8px; font-size: 7.5px; font-weight: bold; }
        .b-ok   { background: #e3f4e8; color: #1e7b3c; }
        .b-info { background: #e5edfc; color: #1d4ed8; }
        .b-azul { background: #e1ecf3; color: #0b4a6b; }
        .b-amb  { background: #fdebd0; color: #a35a00; }

        .resumenes { margin-top: 12px; }
        .resumenes > tbody > tr > td { vertical-align: top; padding: 0; }
        .caja { border: 1px solid #d9e2ec; border-radius: 4px; }
        .caja .cab { background: #f0f4f8; color: #0b4a6b; font-weight: bold; font-size: 8.5px; text-transform: uppercase; padding: 5px 8px; border-bottom: 1px solid #d9e2ec; }
        .caja .cab.naranja { background: #fdf1e1; color: #a35a00; }
        .caja table td { padding: 3px 8px; font-size: 9px; }
        .caja table td.r { text-align: right; font-weight: bold; }
        .caja table tr.t td { border-top: 1px dashed #9aa5b1; font-size: 10.5px; font-weight: bold; color: #0b4a6b; padding-top: 4px; }
        .caja .nota { font-size: 7px; color: #7b8794; padding: 0 8px 5px 8px; }

        .notas { margin-top: 10px; font-size: 7.5px; color: #616e7c; line-height: 1.45; }
        .pie { position: fixed; bottom: -26px; left: 0; right: 0; font-size: 7px; color: #9aa5b1; border-top: 1px solid #e4e7eb; padding-top: 4px; }
        .pie .pag:after { content: "Página " counter(page); }
    </style>
</head>

<body>
    @php
        $c = $e['cabecera'];
        $h = $e['hoy'];
        $n = fn ($v) => number_format((float) $v, 2, '.', ',');
        $f = fn ($d) => $d ? \Carbon\Carbon::parse($d)->format('d/m/Y') : '—';
        $tea = fn ($v) => rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.') . '%';
        $claseCond = ['Vigente' => 'cond-vigente', 'Vencido' => 'cond-vencido', 'Cancelado' => 'cond-cancelado'];
        $claseRes = ['Renovado' => 'b-info', 'Cancelado' => 'b-azul', 'Vigente' => 'b-ok'];
        $claseJoya = ['En custodia' => 'b-amb', 'Liberada' => 'b-info', 'Entregada' => 'b-ok'];
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

    {{-- ================= Encabezado ================= --}}
    <table class="head">
        <tr>
            @if ($logo)
                <td class="logo-box"><img src="{{ $logo }}" alt="Grupo CrediPalmo"></td>
            @endif
            <td class="titulo">
                <h1>ESTADO DE CUENTA · CREDIJOYA</h1>
                <div class="sub">
                    Crédito vigente N° {{ $c['actual_id'] }}
                    @if ($c['renovaciones'] > 0)
                        · original N° {{ $c['original_id'] }} · {{ $c['renovaciones'] }} {{ $c['renovaciones'] == 1 ? 'renovación' : 'renovaciones' }}
                    @endif
                </div>
            </td>
            <td class="fecha-box">
                <div class="lbl">A fecha</div>
                <div class="val">{{ $emitido->format('d/m/Y') }}</div>
                <span class="cond {{ $claseCond[$c['condicion']] ?? 'cond-cancelado' }}">
                    {{ $c['condicion'] }}{{ $c['condicion'] === 'Vencido' ? ' · ' . $c['dias_atraso'] . ' días' : '' }}
                </span>
            </td>
        </tr>
    </table>

    <div class="regla"></div>

    <table class="info">
        <tr>
            <td class="l">Cliente:</td>
            <td class="v" colspan="3">{{ $c['cliente'] }}</td>
            <td class="l">DNI / RUC:</td>
            <td class="v">{{ $c['documento'] ?: '—' }}</td>
            <td class="l">Oficina:</td>
            <td class="v">{{ $c['oficina'] ?: '—' }}</td>
        </tr>
        <tr>
            <td class="l">Primer desemb.:</td>
            <td class="v">S/ {{ $n($c['monto_original']) }} · {{ $f($c['fecha_original']) }}</td>
            <td class="l">Tasación:</td>
            <td class="v">S/ {{ $n($c['tasacion'] ?: $e['totales']['tasacion']) }}</td>
            <td class="l">TEA vigente:</td>
            <td class="v">{{ $tea($c['tea']) }}</td>
            <td class="l">Asesor:</td>
            <td class="v">{{ $c['asesor'] ?: '—' }}</td>
        </tr>
        <tr>
            <td class="l">Vence:</td>
            <td class="v">{{ $f($c['vence']) }}</td>
            <td class="l">Días atraso:</td>
            <td class="v" style="{{ $c['dias_atraso'] > 0 ? 'color:#b42318;' : '' }}">{{ $c['dias_atraso'] }}</td>
            <td class="l">Renovaciones:</td>
            <td class="v">{{ $c['renovaciones'] }}</td>
            <td class="l">Total pagado:</td>
            <td class="v">S/ {{ $n($e['totales']['pagado']) }}</td>
        </tr>
    </table>

    {{-- ================= Resúmenes de hoy ================= --}}
    <table class="resumenes">
        <tr>
            <td style="width:32%;">
                <div class="caja">
                    <div class="cab naranja">Para cancelar hoy {{ $emitido->format('d/m/Y') }}</div>
                    <table>
                        <tr><td>Capital</td><td class="r">{{ $n($h['cancelar']['capital']) }}</td></tr>
                        <tr><td>Interés</td><td class="r">{{ $n($h['cancelar']['interes']) }}</td></tr>
                        <tr><td>Mora</td><td class="r">{{ $n($h['cancelar']['mora']) }}</td></tr>
                        <tr class="t"><td>TOTAL S/</td><td class="r">{{ $n($h['cancelar']['total']) }}</td></tr>
                    </table>
                    <div class="nota">{{ $h['vigente'] ? 'Paga todo y se liberan las joyas.' : 'El préstamo está cancelado.' }}</div>
                </div>
            </td>
            <td style="width:2%;"></td>
            <td style="width:32%;">
                <div class="caja">
                    <div class="cab">Para renovar hoy</div>
                    <table>
                        <tr><td>Interés</td><td class="r">{{ $n($h['renovar']['interes']) }}</td></tr>
                        <tr><td>Mora</td><td class="r">{{ $n($h['renovar']['mora']) }}</td></tr>
                        <tr><td>Capital que sigue</td><td class="r">{{ $n($h['capital']) }}</td></tr>
                        <tr class="t"><td>TOTAL S/</td><td class="r">{{ $n($h['renovar']['total']) }}</td></tr>
                    </table>
                    <div class="nota">Se abre un crédito nuevo por un mes con las mismas joyas.</div>
                </div>
            </td>
            <td style="width:2%;"></td>
            <td style="width:32%;">
                <div class="caja">
                    <div class="cab">Resumen de pagos</div>
                    <table>
                        <tr><td>Interés pagado</td><td class="r">{{ $n($e['totales']['interes']) }}</td></tr>
                        <tr><td>Mora pagada</td><td class="r">{{ $n($e['totales']['mora']) }}</td></tr>
                        <tr><td>Capital pagado</td><td class="r">{{ $n($e['totales']['capital']) }}</td></tr>
                        <tr class="t"><td>TOTAL PAGADO S/</td><td class="r">{{ $n($e['totales']['pagado']) }}</td></tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    {{-- ================= Joyas ================= --}}
    <div class="subtitulo">Joyas en garantía</div>
    <table class="tabla">
        <thead>
            <tr>
                <th>Código</th>
                <th class="izq">Descripción</th>
                <th>Kilate</th>
                <th>Piezas</th>
                <th class="num">Peso bruto (g)</th>
                <th class="num">Peso neto (g)</th>
                <th class="num">Tasación (S/)</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($e['joyas'] as $j)
                <tr>
                    <td>{{ $j['codigo'] ?: '—' }}</td>
                    <td class="izq">{{ $j['descripcion'] ?: '—' }}</td>
                    <td>{{ $j['kilate'] }}k</td>
                    <td>{{ $j['piezas'] }}</td>
                    <td class="num">{{ number_format($j['peso_bruto'], 3) }}</td>
                    <td class="num">{{ number_format($j['peso_neto'], 3) }}</td>
                    <td class="num">{{ $n($j['tasacion']) }}</td>
                    <td>
                        <span class="badge {{ $claseJoya[$j['estado']] ?? 'b-azul' }}">{{ $j['estado'] }}</span>
                        @if ($j['entregada'])<span class="det">{{ $f($j['entregada']) }}</span>@endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="muted">No hay joyas registradas en este préstamo.</td></tr>
            @endforelse
            @if (count($e['joyas']))
                <tr class="total">
                    <td colspan="6" class="num">TOTAL TASACIÓN</td>
                    <td class="num">{{ $n($e['totales']['tasacion']) }}</td>
                    <td></td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- ================= Historial ================= --}}
    <div class="subtitulo">Historial del préstamo (original y renovaciones)</div>
    <table class="tabla">
        <thead>
            <tr>
                <th>#</th>
                <th>Crédito</th>
                <th>Desembolso</th>
                <th>Vencimiento</th>
                <th class="num">Capital</th>
                <th class="num">TEA</th>
                <th class="num">Interés prog.</th>
                <th>Fecha pago</th>
                <th class="num">Interés pag.</th>
                <th class="num">Mora pag.</th>
                <th class="num">Capital pag.</th>
                <th class="num">Total pagado</th>
                <th>Resultado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($e['creditos'] as $cr)
                <tr>
                    <td>{{ $cr['secuencia'] }}</td>
                    <td>N° {{ $cr['credito_id'] }}</td>
                    <td>{{ $f($cr['desembolso']) }}</td>
                    <td>{{ $f($cr['vencimiento']) }}</td>
                    <td class="num">{{ $n($cr['capital']) }}</td>
                    <td class="num">{{ $tea($cr['tea']) }}</td>
                    <td class="num">{{ $n($cr['interes_prog']) }}</td>
                    <td>{{ $f($cr['fecha_pago']) }}</td>
                    <td class="num {{ $cr['interes_pag'] > 0 ? '' : 'muted' }}">{{ $n($cr['interes_pag']) }}</td>
                    <td class="num {{ $cr['mora_pag'] > 0 ? '' : 'muted' }}">{{ $n($cr['mora_pag']) }}</td>
                    <td class="num {{ $cr['capital_pag'] > 0 ? '' : 'muted' }}">{{ $n($cr['capital_pag']) }}</td>
                    <td class="num">{{ $n($cr['total_pag']) }}</td>
                    <td>
                        <span class="badge {{ $claseRes[$cr['resultado']] ?? 'b-azul' }}">{{ $cr['resultado'] }}</span>
                        @if ($cr['renovado_a'])<span class="det">a N° {{ $cr['renovado_a'] }}</span>@endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ================= Pagos ================= --}}
    <div class="subtitulo">Pagos realizados</div>
    <table class="tabla">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Crédito</th>
                <th class="izq">Concepto</th>
                <th class="num">Interés</th>
                <th class="num">Mora</th>
                <th class="num">Capital</th>
                <th class="num">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($e['pagos'] as $p)
                <tr>
                    <td>{{ $f($p['fecha']) }}</td>
                    <td>N° {{ $p['credito_id'] }}</td>
                    <td class="izq">{{ $p['concepto'] }}{{ $p['renueva_a'] ? ' → N° ' . $p['renueva_a'] : '' }}</td>
                    <td class="num">{{ $n($p['interes']) }}</td>
                    <td class="num">{{ $n($p['mora']) }}</td>
                    <td class="num">{{ $n($p['capital']) }}</td>
                    <td class="num">{{ $n($p['total']) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">Aún no hay pagos registrados.</td></tr>
            @endforelse
            @if (count($e['pagos']))
                <tr class="total">
                    <td colspan="3" class="num">TOTALES</td>
                    <td class="num">{{ $n($e['totales']['interes']) }}</td>
                    <td class="num">{{ $n($e['totales']['mora']) }}</td>
                    <td class="num">{{ $n($e['totales']['capital']) }}</td>
                    <td class="num">{{ $n($e['totales']['pagado']) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="notas">
        En CrediJoya, cuando el cliente paga solo el interés, el crédito se renueva: se cierra y se abre uno nuevo por un mes
        con las mismas joyas. Este estado de cuenta junta toda la cadena desde el primer desembolso.
        "Para cancelar hoy" es el mismo monto que cobra caja. La mora se calcula a 1.5 por mil diario sobre la cuota vencida.
        Montos en soles (S/).
    </div>
</body>

</html>
