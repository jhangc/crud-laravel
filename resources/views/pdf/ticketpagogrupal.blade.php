<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        .ticket {
            width: 200px;
            margin: auto;
            page-break-after: always;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header img {
            width: 80px;
            height: auto;
        }

        .header h2 {
            margin: 0;
            font-size: 12px;
        }

        .header h4 {
            margin: 5px 0;
            font-size: 10px;
        }

        .content p {
            margin: 2px 0;
        }

        .content .label {
            font-weight: bold;
        }

        .content .value {
            float: right;
        }

        .signature {
            margin-top: 10px;
        }

        .signature .line {
            border-top: 1px solid #000;
            margin: 5px 0;
            width: 100%;
        }
    </style>
</head>

<body>
    @foreach ($data as $item)
        <div class="ticket">
            <div class="header">
                <img src="{{ asset('logo.png') }}" alt="Logo">
                <h2>Grupo Credipalmo</h2>
                <h4>Comprobante de Pago</h4>
            </div>
            <div class="content">
                <p><span class="label">Fecha:</span> <span class="value">{{ $item['ingreso']->created_at->format('d/m/Y H:i:s') }}</span></p>
                <p><span class="label">DNI:</span> <span class="value">{{ $item['cliente']->documento_identidad ?? '------' }}</span></p>
                <p><span class="label">Nombres:</span> <span class="value">{{ $item['cliente']->nombre ?? 'Cuota General' }}</span></p>
                <p><span class="label">N° de Cuota:</span> <span class="value">{{ $item['ingreso']->numero_cuota }}</span></p>
                <p><span class="label">Monto  de Pago:</span> <span class="value">S/.{{ number_format($item['ingreso']->monto_total_pago_final, 2) }}</span></p>
                <p><span class="label">Días de Mora:</span> <span class="value">{{ $item['ingreso']->dias_mora }}</span></p>
                <p><span class="label">Monto de Mora:</span> <span class="value">S/.{{ number_format($item['ingreso']->monto_mora, 2) }}</span></p>
                <p><span class="label">Monto Total a Pagar:</span> <span class="value">S/.{{ number_format($item['ingreso']->monto, 2) }}</span></p>
                <br>
                <p><span class="label">Observaciones:</span></p>
                <p><span class="label">Interés:</span> <span class="value">S/.{{ number_format($item['cronograma']->interes, 2) }}</span></p>
                <p><span class="label">Amortización:</span> <span class="value">S/.{{ number_format($item['cronograma']->amortizacion, 2) }}</span></p>
                <p><span class="label">Saldo de Deuda:</span> <span class="value">S/.{{ number_format($item['cronograma']->saldo_deuda, 2) }}</span></p>
                <br>
                <p><span class="label">Fecha Venc. Siguiente Cuota:</span> <span class="label">{{ $item['fechaSiguienteCuota'] }}</span></p>
            </div>
            <div class="signature">
                <p><strong>Firma:</strong></p>
                <br>
                <div class="line"></div>
            </div>
        </div>
    @endforeach
</body>

</html>
