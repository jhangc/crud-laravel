<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .ticket {
            page-break-after: always;
        }

        .ticket:last-child {
            page-break-after: none;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 100px;
            height: auto;
        }

        .header h2 {
            margin: 0;
            font-size: 14px;
        }

        .content p {
            margin: 10px 0;
        }

        .signature {
            margin-top: 20px;
        }

        .signature .line {
            border-top: 1px solid #000;
            margin: 10px 0;
            width: 100%;
        }
    </style>
</head>

<body>


    <div class="ticket">
        <div class="header">
            <img src="{{ asset('logo.png') }}" alt="Logo">
            <br>
            <br>
            <h2>Grupo Credipalmo</h2>
        </div>
        <div class="content">
            <p><strong>Asunto:</strong> Pagó CTS</p>
            <p><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
            <p><strong>DNI:</strong> {{ $desembolso->ctsUsuario->user->dni}}</p>
            <p><strong>Nombres:</strong> {{ $desembolso->ctsUsuario->user->name }}</p>
            <p><strong>Monto:</strong> S/.{{ number_format($montoTotal, 2) }}</p>
        </div>
        <div class="signature">
            <p><strong>Firma:</strong></p>
            <br>
            <br>
            <div class="line"></div>
        </div>
    </div>
    <br>
    <div class="line"></div>

</body>

</html>
