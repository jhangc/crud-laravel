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
    <!-- Ticket inicial con el monto total del grupo -->
    <div class="ticket">
        <div class="header">
            <img src="{{ asset('logo.png') }}" alt="Logo">
            <br>
            <br>
            <h2>Grupo Credipalmo</h2>
        </div>
        <div class="content">
            <p><strong>Grupo:</strong> {{ $prestamo->nombre_prestamo }}</p>
            <p><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
            <p><strong>Monto Total del Grupo:</strong> S/.{{ number_format($montoTotalGrupo, 2) }}</p>
        </div>
        <div class="signature">
            <p><strong>Firma:</strong></p>
            <br>
            <br>
            <div class="line"></div>
        </div>
    </div>

    @foreach ($creditos as $index => $credito)
        <div class="{{ $index === count($creditos) - 1 ? '' : 'ticket' }}">
            <div class="header">
                <img src="{{ asset('logo.png') }}" alt="Logo">
                <br>
                <br>
                <h2>Grupo Credipalmo</h2>
            </div>
            <div class="content">
                <p><strong>Grupo:</strong> {{ $prestamo->nombre_prestamo }}</p>
                <p><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
                <p><strong>DNI:</strong> {{ $credito->clientes->documento_identidad }}</p>
                <p><strong>Nombres:</strong> {{ $credito->clientes->nombre }}</p>
                <p><strong>Monto:</strong> S/.{{ number_format($credito->monto_indivual, 2) }}</p>
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
    @endforeach
</body>

</html>
