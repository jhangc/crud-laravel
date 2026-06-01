<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket</title>
    <style>
        @page { margin: 0; }
        html, body { margin: 0; padding: 0; }

        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 9px;
            color: #000;
            line-height: 1.25;
        }

        .ticket { padding: 8px 14px; page-break-after: always; }
        .ticket:last-child { page-break-after: auto; }

        .header { text-align: center; margin-bottom: 4px; }
        .header img { width: 60px; height: auto; }
        .brand { font-size: 11px; font-weight: bold; margin-top: 2px; }
        .sub { font-size: 9px; margin-top: 1px; }

        hr { border: 0; height: 0; margin: 5px 0; }

        table.kv { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.kv td { padding: 1px 0; vertical-align: top; word-wrap: break-word; overflow-wrap: break-word; }
        table.kv td.l { font-weight: bold; padding-right: 4px; width: 58%; }
        table.kv td.r { text-align: right; width: 42%; }
        tr.tot td { border-top: 1px solid #000; font-weight: bold; padding-top: 2px; }

        .sec { font-weight: bold; text-align: center; margin: 4px 0 1px; }

        .sign { margin-top: 14px; text-align: center; }
        .sign .line { border-top: 1px solid #000; width: 80%; margin: 16px auto 2px; }
    </style>
</head>

<body>
    @foreach ($data as $item)
        <div class="ticket">
            <div class="header">
                <img src="{{ asset('logo.png') }}" alt="Logo">
                <div class="brand">Grupo Credipalmo</div>
                <div class="sub">Comprobante de Pago</div>
            </div>

            <hr>

            <table class="kv">
                <tr><td class="l">Fecha</td><td class="r">{{ $item['ingreso']->created_at->format('d/m/Y H:i') }}</td></tr>
                <tr>
                    <td class="l">{{ isset($item['cliente']->documento_identidad) ? 'DNI' : 'Grupo' }}</td>
                    <td class="r">{{ isset($item['cliente']->documento_identidad) ? $item['cliente']->documento_identidad : $item['prestamo']->nombre_prestamo }}</td>
                </tr>
                <tr><td class="l">N&deg; Cuota</td><td class="r">{{ $item['ingreso']->numero_cuota }}</td></tr>
                <tr><td colspan="2" class="l">{{ isset($item['cliente']->documento_identidad) ? 'Cliente' : 'Detalle' }}: <span style="font-weight:normal">{{ $item['cliente']->nombre ?? 'Cuota General' }}</span></td></tr>
            </table>

            <hr>

            <table class="kv">
                <tr><td class="l">Abono a capital</td><td class="r">S/ {{ number_format($item['ingreso']->monto_total_pago_final, 2) }}</td></tr>
                <tr><td class="l">D&iacute;as de mora</td><td class="r">{{ $item['ingreso']->dias_mora }}</td></tr>
                <tr><td class="l">Mora pagada</td><td class="r">S/ {{ number_format($item['ingreso']->monto_mora, 2) }}</td></tr>
                <tr class="tot"><td class="l">Total abonado hoy</td><td class="r">S/ {{ number_format($item['ingreso']->monto, 2) }}</td></tr>
            </table>

            <hr>

            <table class="kv">
                <tr><td class="l">Saldo restante cuota</td><td class="r">S/ {{ number_format((float)($item['saldo_restante'] ?? 0), 2) }}</td></tr>
                <tr><td class="l">Mora vigente restante</td><td class="r">S/ {{ number_format((float)($item['mora_vigente'] ?? 0), 2) }}</td></tr>
                <tr class="tot"><td class="l">Total pendiente cuota</td><td class="r">S/ {{ number_format((float)($item['total_pendiente'] ?? 0), 2) }}</td></tr>
            </table>

            <hr>

            <table class="kv">
                @if($item['cronograma']->pago_capital != null)
                    <tr><td class="l">Observaci&oacute;n</td><td class="r">Pago Capital &mdash; {{ $item['cronograma']->pago_capital == 1 ? 'Reducir cuota' : 'Reducir plazo' }}</td></tr>
                @endif
                <tr><td class="l">Inter&eacute;s</td><td class="r">S/ {{ number_format($item['cronograma']->pago_capital == null ? $item['cronograma']->interes : ($item['cronograma']->intereses_capital ?? 0), 2) }}</td></tr>
                <tr><td class="l">Amortizaci&oacute;n</td><td class="r">S/ {{ number_format($item['cronograma']->pago_capital == null ? ($item['cronograma']->amortizacion ?? 0) : ($item['cronograma']->monto_capital ?? 0), 2) }}</td></tr>
            </table>

            <hr>

            <div class="sec">Pr&oacute;xima cuota</div>
            @php $sigMora = $item['sig_cuota_mora'] ?? null; @endphp
            <table class="kv">
                <tr><td class="l">Monto</td><td class="r">S/ {{ $item['fechaSiguienteCuota'] != 'N/A' ? number_format($item['siguienteCuota']->monto, 2) : '0.00' }}</td></tr>
                <tr><td class="l">Vence</td><td class="r">{{ $item['fechaSiguienteCuota'] != 'N/A' ? \Carbon\Carbon::parse($item['fechaSiguienteCuota'])->format('d/m/Y') : '&mdash;' }}</td></tr>
                @if($item['fechaSiguienteCuota'] != 'N/A' && $sigMora && ($sigMora['mora'] ?? 0) > 0)
                    @php $sigSaldo = $sigMora['saldo'] ?? $item['siguienteCuota']->monto; @endphp
                    @if($sigSaldo < $item['siguienteCuota']->monto - 0.009)
                        <tr><td class="l">Saldo pend.</td><td class="r">S/ {{ number_format($sigSaldo, 2) }}</td></tr>
                    @endif
                    <tr><td class="l">D&iacute;as mora acum.</td><td class="r">{{ $sigMora['dias'] }}</td></tr>
                    <tr><td class="l">Calculado al</td><td class="r">{{ \Carbon\Carbon::today()->format('d/m/Y') }}</td></tr>
                    <tr><td class="l">Mora acum.</td><td class="r">S/ {{ number_format($sigMora['mora'], 2) }}</td></tr>
                    <tr class="tot"><td class="l">Total a pagar</td><td class="r">S/ {{ number_format($sigSaldo + $sigMora['mora'], 2) }}</td></tr>
                @endif
            </table>

            <div class="sign">
                <div class="line"></div>
                Firma
            </div>
        </div>
    @endforeach
</body>

</html>
