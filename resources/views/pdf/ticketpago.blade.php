<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket</title>
    <style>
        /* dompdf ignora el margen de @page cuando el papel se fija por tamaÃ±o, y no respeta
           box-sizing. SoluciÃ³n fiable: padding en el contenedor SIN width:100% â€" el bloque
           llena el ancho del papel y el padding queda por dentro (deja margen, no desborda). */
        @page { margin: 0; }
        html, body { margin: 0; padding: 0; }

        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 9px;
            color: #000;
            line-height: 1.25;
        }

        .ticket { padding: 8px 14px; }

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

        .tag {
            text-align: center; font-weight: bold; font-size: 9.5px;
            border: 1px solid #000; border-radius: 3px; padding: 2px 0; margin: 4px 0;
        }
        .note { font-size: 8px; color: #333; margin: 2px 0 0; }
        .sec { font-weight: bold; text-align: center; margin: 4px 0 1px; }

        .sign { margin-top: 14px; text-align: center; }
        .sign .line { border-top: 1px solid #000; width: 80%; margin: 16px auto 2px; }
    </style>
</head>

<body>
    <div class="ticket">
        <div class="header">
            <img src="{{ asset('logo.png') }}" alt="Logo">
            <div class="brand">Grupo Credipalmo</div>
            <div class="sub">Comprobante de Pago</div>
        </div>

        <hr>

        <table class="kv">
            <tr><td class="l">Fecha</td><td class="r">{{ $ingreso->created_at->format('d/m/Y H:i') }}</td></tr>
            <tr><td class="l">DNI</td><td class="r">{{ $cliente->documento_identidad }}</td></tr>
            <tr><td class="l">N&deg; Cuota</td><td class="r">{{ $ingreso->numero_cuota }}</td></tr>
            <tr><td colspan="2" class="l">Cliente: <span style="font-weight:normal">{{ $cliente->nombre }}</span></td></tr>
        </table>

        <div class="tag">{{ $tipoPagoTexto ?? 'PAGO DE CUOTA' }}</div>
         <hr>
         <table class="kv">
            @if($cronograma->pago_capital != null)
                <tr><td class="l">Observaci&oacute;n</td><td class="r">Pago Capital &mdash; {{ $cronograma->pago_capital == 1 ? 'Reducir cuota' : 'Reducir plazo' }}</td></tr>
            @endif
            <tr><td class="l">Inter&eacute;s</td><td class="r">S/ {{ number_format($cronograma->pago_capital == null ? $cronograma->interes : ($cronograma->intereses_capital ?? 0), 2) }}</td></tr>
            <tr><td class="l">Amortizaci&oacute;n</td><td class="r">S/ {{ number_format($cronograma->pago_capital == null ? ($cronograma->amortizacion ?? 0) : ($cronograma->monto_capital ?? 0), 2) }}</td></tr>
        </table>
        <hr>
        <table class="kv">
            <tr><td class="l">Pago a cuota</td><td class="r">S/ {{ number_format($ingreso->monto_total_pago_final, 2) }}</td></tr>
            @php
                $diasAtrasoCuota = 0;
                if (!empty($cronograma)) {
                    $vencTk = \Carbon\Carbon::parse($cronograma->fecha)->startOfDay();
                    $pagoTk = \Carbon\Carbon::parse($ingreso->fecha_pago ?? $ingreso->created_at)->startOfDay();
                    $diasAtrasoCuota = $pagoTk->greaterThan($vencTk) ? (int) $vencTk->diffInDays($pagoTk) : 0;
                }
            @endphp
            <tr><td class="l">D&iacute;as de mora</td><td class="r">{{ $diasAtrasoCuota }}</td></tr>
            <tr><td class="l">Mora</td><td class="r">S/ {{ number_format($ingreso->monto_mora, 2) }}</td></tr>
            <tr class="tot"><td class="l">Total pagado</td><td class="r">S/ {{ number_format($ingreso->monto, 2) }}</td></tr>
            @if(($diferencia ?? 0) > 0)
                <tr><td class="l">Adelanto sig. cuota</td><td class="r">S/ {{ number_format($diferencia, 2) }}</td></tr>
                <tr class="tot"><td class="l">Total recibido</td><td class="r">S/ {{ number_format($diferencia + $ingreso->monto, 2) }}</td></tr>
            @endif
        </table>

        @if(!empty($esParcial))
            <table class="kv">
                <tr class="tot"><td class="l">Saldo restante cuota</td><td class="r">S/ {{ number_format($saldoRestante ?? 0, 2) }}</td></tr>
            </table>
            @if(!empty($moraCorre))
                <div class="note" style="text-align:center;">La mora seguir&aacute; corriendo sobre el saldo restante desde hoy.</div>
            @endif
        @else
            <div class="note" style="text-align:center;">Cuota cancelada.</div>
        @endif

        <hr>

        <div class="sec">Pr&oacute;xima cuota</div>
        <table class="kv">
            <tr><td class="l">Monto</td><td class="r">S/ {{ $fechaSiguienteCuota != 'N/A' ? number_format($siguienteCuota->monto, 2) : '0.00' }}</td></tr>
            <tr><td class="l">Vence</td><td class="r">{{ $fechaSiguienteCuota != 'N/A' ? \Carbon\Carbon::parse($fechaSiguienteCuota)->format('d/m/Y') : '&mdash;' }}</td></tr>
            @if($fechaSiguienteCuota != 'N/A')
                <tr><td class="l">Amortizaci&oacute;n</td><td class="r">S/ {{ number_format($siguienteCuota->pago_capital == null ? ($siguienteCuota->amortizacion ?? 0) : ($siguienteCuota->monto_capital ?? 0), 2) }}</td></tr>
                <tr><td class="l">Inter&eacute;s</td><td class="r">S/ {{ number_format($siguienteCuota->pago_capital == null ? $siguienteCuota->interes : ($siguienteCuota->intereses_capital ?? 0), 2) }}</td></tr>
            @endif
            @if($fechaSiguienteCuota != 'N/A' && isset($sigCuotaMora) && ($sigCuotaMora['mora'] ?? 0) > 0)
                @php $sigSaldo = $sigCuotaMora['saldo'] ?? $siguienteCuota->monto; @endphp
                @if($sigSaldo < $siguienteCuota->monto - 0.009)
                    <tr><td class="l">Saldo pend.</td><td class="r">S/ {{ number_format($sigSaldo, 2) }}</td></tr>
                @endif
                <tr><td class="l">D&iacute;as mora acum.</td><td class="r">{{ $sigCuotaMora['dias'] }}</td></tr>
                <tr><td class="l">Mora acum.</td><td class="r">S/ {{ number_format($sigCuotaMora['mora'], 2) }}</td></tr>
                <tr class="tot"><td class="l">Total a pagar</td><td class="r">S/ {{ number_format($sigSaldo + $sigCuotaMora['mora'], 2) }}</td></tr>
            @endif
        </table>

        <div class="sign">
            <div class="line"></div>
            Firma
        </div>
    </div>
</body>

</html>
