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
        /* .ticket {
            width: 205px;
            padding: 10px;
            border: 1px solid #000;
        } */
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
            margin: 5px 0;
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
    @foreach($creditos as $credito)
    <div class="ticket">
        <div class="header">
            <img src="{{ asset('logo.png')}}" alt="Logo">
            <h2>Grupo Credipalmo</h2>
        </div>
        <div class="content">
            <p><strong>Grupo:</strong> {{  $prestamo->nombre_prestamo }}</p>
            <p><strong>Fecha:{{ now()->format('d/m/Y H:i:s') }}</strong> </p>
            <p><strong>DNI:</strong> {{ $credito->clientes->documento_identidad }}</p>
            <p><strong>Nombres:</strong> {{$credito->clientes->nombre }}</p>
            <p><strong>Monto:</strong> S/.{{ $credito->monto_indivual}}</p>
        </div>
        <div class="signature">
            <p><strong>Firma:</strong></p>
            <div class="line"></div>
        </div>
    </div>
    <br>
    <div class="line">
    </div>
    @endforeach
</body>
</html>
