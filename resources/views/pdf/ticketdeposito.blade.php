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

        .ticket { padding: 8px 14px; }

        .header { text-align: center; margin-bottom: 4px; }
        .header img { width: 60px; height: auto; }
        .brand { font-size: 11px; font-weight: bold; margin-top: 2px; }
        .sub { font-size: 9px; margin-top: 1px; }

        hr { border: 0; border-top: 1px dashed #000; margin: 5px 0; }

        table.kv { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.kv td { padding: 1px 0; vertical-align: top; word-wrap: break-word; overflow-wrap: break-word; }
        table.kv td.l { font-weight: bold; padding-right: 4px; width: 50%; }
        table.kv td.r { text-align: right; width: 50%; }
        tr.tot td { border-top: 1px solid #000; font-weight: bold; padding-top: 2px; }

        .sign { margin-top: 14px; text-align: center; }
        .sign .line { border-top: 1px solid #000; width: 80%; margin: 16px auto 2px; }
    </style>
</head>

<body>
    <div class="ticket">
        <div class="header">
            <img src="{{ asset('logo.png') }}" alt="Logo">
            <div class="brand">Grupo Credipalmo</div>
            <div class="sub">Dep&oacute;sito a cuenta CTS</div>
        </div>

        <hr>

        <table class="kv">
            <tr><td class="l">Fecha</td><td class="r">{{ now()->format('d/m/Y H:i') }}</td></tr>
            <tr><td class="l">DNI</td><td class="r">{{ $deposito->ctsUsuario->user->dni }}</td></tr>
            <tr><td colspan="2" class="l">Nombres: <span style="font-weight:normal">{{ $deposito->ctsUsuario->user->name }}</span></td></tr>
            <tr class="tot"><td class="l">Monto</td><td class="r">S/ {{ number_format($montoTotal, 2) }}</td></tr>
        </table>

        <div class="sign">
            <div class="line"></div>
            Firma
        </div>
    </div>
</body>

</html>
