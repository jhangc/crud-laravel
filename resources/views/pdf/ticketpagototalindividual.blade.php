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
        table.kv td.l { font-weight: bold; padding-right: 4px; width: 58%; }
        table.kv td.r { text-align: right; width: 42%; }
        tr.tot td { border-top: 1px solid #000; font-weight: bold; padding-top: 2px; }

        .tag {
            text-align: center; font-weight: bold; font-size: 9.5px;
            border: 1px solid #000; border-radius: 3px; padding: 2px 0; margin: 4px 0;
        }
        .note { font-size: 8px; color: #333; margin: 2px 0 0; }

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
            <tr>
                <td class="l">{{ isset($cliente->documento_identidad) ? 'DNI' : 'Grupo' }}</td>
                <td class="r">{{ isset($cliente->documento_identidad) ? $cliente->documento_identidad : $prestamo->nombre_prestamo }}</td>
            </tr>
            <tr><td class="l">N&deg; Cuota</td><td class="r">{{ $ingreso->numero_cuota }}</td></tr>
            <tr><td colspan="2" class="l">{{ isset($cliente->documento_identidad) ? 'Cliente' : 'Detalle' }}: <span style="font-weight:normal">{{ $cliente->nombre ?? 'Cuota General' }}</span></td></tr>
        </table>

        <div class="tag">PAGO TOTAL DEL CR&Eacute;DITO</div>

        <hr>

        <table class="kv">
            <tr><td class="l">Pago a cr&eacute;dito</td><td class="r">S/ {{ number_format($total_final_pago, 2) }}</td></tr>
            <tr><td class="l">D&iacute;as de mora</td><td class="r">{{ $ingreso->dias_mora }}</td></tr>
            <tr><td class="l">Mora</td><td class="r">S/ {{ number_format($total_mora, 2) }}</td></tr>
            <tr><td class="l">Inter&eacute;s</td><td class="r">S/ {{ number_format($total_intereses, 2) }}</td></tr>
            <tr class="tot"><td class="l">Total pagado</td><td class="r">S/ {{ number_format($total_pago, 2) }}</td></tr>
        </table>

        <div class="note" style="text-align:center;">Se realiz&oacute; el pago total del cr&eacute;dito.</div>

        <div class="sign">
            <div class="line"></div>
            Firma
        </div>
    </div>
</body>

</html>
