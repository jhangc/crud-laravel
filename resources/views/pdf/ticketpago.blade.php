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
    <div class="ticket">
        <div class="header">
            <img src="{{ asset('logo.png') }}" alt="Logo">
            <h2>Grupo Credipalmo</h2>
            <h4>Comprobante de Pago</h4>
        </div>
        <div class="content">
            <p><span class="label">Fecha:</span> <span class="value">{{ $ingreso->created_at->format('d/m/Y H:i:s') }}</span></p>
            <p><span class="label">DNI:</span> <span class="value">{{ $cliente->documento_identidad }}</span></p>
            <p><span class="label">Nombres:</span> <span class="value">{{ $cliente->nombre }}</span></p>
            <p><span class="label">N° de Cuota:</span> <span class="value">{{ $ingreso->numero_cuota }}</span></p>
            <p><span class="label">Monto de Pago:</span> <span class="value">S/.{{ number_format($ingreso->monto_total_pago_final, 2) }}</span></p>
            <p><span class="label">Días de Mora:</span> <span class="value">{{ $ingreso->dias_mora }}</span></p>
            <p><span class="label">Monto de Mora:</span> <span class="value">S/.{{ number_format($ingreso->monto_mora, 2) }}</span></p>
            <p><span class="label">Monto Total a Pagar:</span> <span class="value">S/.{{ number_format($ingreso->monto, 2) }}</span></p>
            <br>
            <p><span class="label">Observaciones:</span><span class="value">
                    @if($cronograma->pago_capital == null)
                    @else
                    Pago Capital-{{ $cronograma->pago_capital == 1 ? 'Reducir cuota' : 'Reducir plazo' }}
                    @endif
                </span></p>
                <br>
            <p><span class="label">Interés:</span> <span class="value">S/.
                    @if($cronograma->pago_capital == null)
                    {{ number_format($cronograma->interes, 2) }}

                    @else
                    {{ number_format($cronograma->intereses_capital ?? 0, 2) }}

                    @endif
                </span></p>
            <p><span class="label">Amortización:</span> <span class="value">S/.

                    @if($cronograma->pago_capital == null)
                    {{ number_format($cronograma->amortizacion ?? 0, 2) }}

                    @else
                    {{ number_format($cronograma->monto_capital ?? 0, 2) }}

                    @endif
                </span></p>
            <!-- <p><span class="label">Saldo de Deuda:</span> <span class="value">S/.{{ $cronograma->pago_capital == null 
                            ? number_format($cronograma->saldo_deuda, 2) 
                            : number_format($cronograma->nuevo_saldo_deuda ?? 0, 2) 
                        }}</span></p> -->
                        <p><span class="label">Monto sig.Cuota:</span> <span class="value">S/.{{ $fechaSiguienteCuota !='N/A'?($siguienteCuota->monto) :'0.00';
                        }}</span></p>
            <br>

            <p><span class="label">Fecha Venc. Siguiente Cuota:</span> <span class="label">{{ $fechaSiguienteCuota }}</span></p>
        </div>
        <div class="signature">
            <p><strong>Firma:</strong></p>
            <br>
            <div class="line"></div>
        </div>
    </div>
</body>

</html>