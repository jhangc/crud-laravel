<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Historial</title>
    <style>
        @page { margin: 0; }
        html, body { margin: 0; padding: 0; }
        body { font-family: "DejaVu Sans", Arial, sans-serif; font-size: 9px; color: #000; line-height: 1.25; }
        .ticket { padding: 8px 14px; page-break-after: always; }
        .ticket:last-child { page-break-after: auto; }
        .header { text-align: center; margin-bottom: 4px; }
        .header img { width: 55px; height: auto; }
        .brand { font-size: 11px; font-weight: bold; margin-top: 2px; }
        .sub { font-size: 9px; margin-top: 1px; }
        hr { border: 0; height: 0; margin: 5px 0; }
        table.kv { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.kv td { padding: 1px 0; vertical-align: top; word-wrap: break-word; overflow-wrap: break-word; }
        table.kv td.l { font-weight: bold; padding-right: 4px; width: 58%; }
        table.kv td.r { text-align: right; width: 42%; }
        tr.tot td { border-top: 1px solid #000; font-weight: bold; padding-top: 2px; }
        table.movs { width: 100%; border-collapse: collapse; margin-top: 2px; }
        table.movs th, table.movs td { border-bottom: 1px dashed #999; padding: 2px 0; text-align: right; }
        table.movs th:first-child, table.movs td:first-child { text-align: left; }
        .tag { text-align: center; font-size: 8px; font-weight: bold; }
    </style>
</head>
<body>
    @foreach ($gruposIntegrantes as $grupo)
        <div class="ticket">
            <div class="header">
                <img src="{{ asset('logo.png') }}" alt="Logo">
                <div class="brand">Grupo Credipalmo</div>
                <div class="sub">{{ $titulo ?? 'Ticket Historial' }}</div>
                @if (!empty($subtitulo))
                    <div class="sub">{{ $subtitulo }}</div>
                @endif
            </div>

            <hr>

            <table class="kv">
                <tr><td class="l">Fecha</td><td class="r">{{ now()->format('d/m/Y H:i') }}</td></tr>
                <tr><td class="l">Grupo</td><td class="r">{{ $prestamo->nombre_prestamo ?? '-' }}</td></tr>
                <tr><td class="l">Cr&eacute;dito</td><td class="r">#{{ $prestamo->id }}</td></tr>
                <tr><td class="l">N&deg; Cuota</td><td class="r">{{ $cuotaGeneral->numero }}</td></tr>
                <tr><td class="l">Integrante</td><td class="r">{{ $grupo['integrante'] }}</td></tr>
            </table>

            <hr>

            <div class="tag">Abonos del integrante</div>
            <table class="movs">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Cap.</th>
                        <th>Mora</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($grupo['movimientos'] as $mov)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse(($mov->fecha_pago ?? now()->toDateString()) . ' ' . ($mov->hora_pago ?? '00:00:00'))->format('d/m H:i') }}</td>
                            <td>S/ {{ number_format((float) $mov->monto_total_pago_final, 2) }}</td>
                            <td>S/ {{ number_format((float) $mov->monto_mora, 2) }}</td>
                            <td>S/ {{ number_format((float) $mov->monto, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <hr>

            <table class="kv">
                <tr><td class="l">Capital abonado</td><td class="r">S/ {{ number_format((float) $grupo['capital'], 2) }}</td></tr>
                <tr><td class="l">Mora pagada</td><td class="r">S/ {{ number_format((float) $grupo['mora_pagada'], 2) }}</td></tr>
                <tr class="tot"><td class="l">Total abonado</td><td class="r">S/ {{ number_format((float) $grupo['total_abonado'], 2) }}</td></tr>
            </table>

            <hr>

            <table class="kv">
                <tr><td class="l">Saldo actual</td><td class="r">S/ {{ number_format((float) $grupo['saldo_actual'], 2) }}</td></tr>
                <tr><td class="l">Mora vigente</td><td class="r">S/ {{ number_format((float) $grupo['mora_actual'], 2) }}</td></tr>
                <tr class="tot"><td class="l">Pendiente actual</td><td class="r">S/ {{ number_format((float) $grupo['pendiente_actual'], 2) }}</td></tr>
            </table>
        </div>
    @endforeach

    @if (isset($movimientosGenerales) && $movimientosGenerales->count() > 0)
        <div class="ticket">
            <div class="header">
                <img src="{{ asset('logo.png') }}" alt="Logo">
                <div class="brand">Grupo Credipalmo</div>
                <div class="sub">Ticket Historial - Movimiento General</div>
            </div>

            <hr>

            <table class="kv">
                <tr><td class="l">Grupo</td><td class="r">{{ $prestamo->nombre_prestamo ?? '-' }}</td></tr>
                <tr><td class="l">Cr&eacute;dito</td><td class="r">#{{ $prestamo->id }}</td></tr>
                <tr><td class="l">N&deg; Cuota</td><td class="r">{{ $cuotaGeneral->numero }}</td></tr>
            </table>

            <hr>

            <table class="movs">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Cap.</th>
                        <th>Mora</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($movimientosGenerales as $movg)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse(($movg->fecha_pago ?? now()->toDateString()) . ' ' . ($movg->hora_pago ?? '00:00:00'))->format('d/m H:i') }}</td>
                            <td>S/ {{ number_format((float) $movg->monto_total_pago_final, 2) }}</td>
                            <td>S/ {{ number_format((float) $movg->monto_mora, 2) }}</td>
                            <td>S/ {{ number_format((float) $movg->monto, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <hr>

            <div class="tag">Resumen de Cuota</div>
            <table class="kv">
                <tr><td class="l">Capital abonado</td><td class="r">S/ {{ number_format((float) $resumen['capital'], 2) }}</td></tr>
                <tr><td class="l">Mora pagada</td><td class="r">S/ {{ number_format((float) $resumen['mora'], 2) }}</td></tr>
                <tr class="tot"><td class="l">Total abonado</td><td class="r">S/ {{ number_format((float) $resumen['total'], 2) }}</td></tr>
            </table>

            <hr>

            <table class="kv">
                <tr><td class="l">Saldo capital actual</td><td class="r">S/ {{ number_format((float) $resumen['saldo_actual'], 2) }}</td></tr>
                <tr><td class="l">Mora vigente actual</td><td class="r">S/ {{ number_format((float) $resumen['mora_actual'], 2) }}</td></tr>
                <tr class="tot"><td class="l">Pendiente actual</td><td class="r">S/ {{ number_format((float) $resumen['pendiente_actual'], 2) }}</td></tr>
            </table>
        </div>
    @endif

    @if (!isset($movimientosGenerales) || $movimientosGenerales->count() == 0)
    <div class="ticket">
        <div class="header">
            <img src="{{ asset('logo.png') }}" alt="Logo">
            <div class="brand">Grupo Credipalmo</div>
            <div class="sub">Resumen de Cuota</div>
        </div>

        <hr>

        <table class="kv">
            <tr><td class="l">Cr&eacute;dito</td><td class="r">#{{ $prestamo->id }}</td></tr>
            <tr><td class="l">N&deg; Cuota</td><td class="r">{{ $cuotaGeneral->numero }}</td></tr>
            <tr><td class="l">Capital abonado</td><td class="r">S/ {{ number_format((float) $resumen['capital'], 2) }}</td></tr>
            <tr><td class="l">Mora pagada</td><td class="r">S/ {{ number_format((float) $resumen['mora'], 2) }}</td></tr>
            <tr class="tot"><td class="l">Total abonado</td><td class="r">S/ {{ number_format((float) $resumen['total'], 2) }}</td></tr>
        </table>

        <hr>

        <table class="kv">
            <tr><td class="l">Saldo capital actual</td><td class="r">S/ {{ number_format((float) $resumen['saldo_actual'], 2) }}</td></tr>
            <tr><td class="l">Mora vigente actual</td><td class="r">S/ {{ number_format((float) $resumen['mora_actual'], 2) }}</td></tr>
            <tr class="tot"><td class="l">Pendiente actual</td><td class="r">S/ {{ number_format((float) $resumen['pendiente_actual'], 2) }}</td></tr>
        </table>
    </div>
    @endif
</body>
</html>
