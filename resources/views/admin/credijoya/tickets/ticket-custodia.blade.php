<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Recibo de Custodia</title>
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
  .header img { width: 62px; height: auto; }
  .brand { font-size: 11px; font-weight: bold; margin-top: 2px; }
  .sub { font-size: 9px; margin-top: 1px; }

  hr { border: 0; border-top: 1px dashed #000; margin: 5px 0; }

  table.kv { width: 100%; border-collapse: collapse; table-layout: fixed; }
  table.kv td { padding: 1px 0; vertical-align: top; word-wrap: break-word; overflow-wrap: break-word; }
  table.kv td.l { font-weight: bold; padding-right: 4px; width: 58%; }
  table.kv td.r { text-align: right; width: 42%; }
  tr.tot td { border-top: 1px solid #000; font-weight: bold; padding-top: 2px; }

  .sec { font-weight: bold; text-align: center; margin: 4px 0 1px; }
  .note { font-size: 8px; color: #333; }
</style>
</head>
<body>

@php
  $logoPath = public_path('logo.png');
  $logo = file_exists($logoPath) ? $logoPath : (filter_var(asset('logo.png'), FILTER_VALIDATE_URL) ? asset('logo.png') : null);
@endphp

<div class="ticket">
  <div class="header">
    @if($logo)<img src="{{ $logo }}" alt="Logo">@endif
    <div class="brand">Grupo Credipalmo</div>
    <div class="sub">Recibo de Custodia</div>
  </div>

  <hr>

  <table class="kv">
    <tr><td class="l">Fecha</td><td class="r">{{ $fecha }} {{ $hora }}</td></tr>
    <tr><td class="l">Motivo</td><td class="r">Custodia de Joyas</td></tr>
    <tr class="tot"><td class="l">Monto del ticket</td><td class="r">S/ {{ number_format($ing->monto, 2) }}</td></tr>
  </table>

  <hr>

  <div class="sec">Estado de custodia</div>
  <table class="kv">
    <tr><td class="l">% mensual</td><td class="r">{{ number_format($estado['porcentaje_mensual'] ?? 0, 2) }}%</td></tr>
    <tr><td class="l">D&iacute;as cobrados</td><td class="r">{{ $estado['dias_cobra'] ?? 0 }} (desde d&iacute;a {{ $estado['desde_dia'] ?? 16 }})</td></tr>
    <tr><td class="l">Acumulado a la fecha</td><td class="r">S/ {{ number_format($estado['acumulado'] ?? 0, 2) }}</td></tr>
    <tr><td class="l">Pagado total</td><td class="r">S/ {{ number_format($estado['pagado'] ?? 0, 2) }}</td></tr>
    <tr class="tot"><td class="l">Saldo pendiente</td><td class="r">S/ {{ number_format($estado['pendiente'] ?? 0, 2) }}</td></tr>
  </table>

  <hr>
  <div class="note" style="text-align:center;">
    Gracias por su pago.<br>
    El cobro de custodia aplica a partir del d&iacute;a 16 despu&eacute;s de cancelado el cr&eacute;dito.
  </div>
</div>

</body>
</html>
