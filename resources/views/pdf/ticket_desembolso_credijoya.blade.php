<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Ticket Desembolso</title>
  <style>
    /* ===== Configuración para térmica 58mm ===== */
    @page { margin: 0; }               /* sin márgenes al imprimir */
    html, body { margin: 0; padding: 0; }

    body{
      font-family: "DejaVu Sans","DejaVu Sans Mono", monospace;
      font-size: 10.5px;
      line-height: 1.25;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    /* Contenedor del ticket: 58mm (~220px) */
    .ticket{
      width: 58mm;               /* si usas 80mm, cambia a 72mm */
      max-width: 220px;          /* fallback en pantalla */
      margin: 0 auto 10px auto;
      padding: 6px 8px 10px;
      border-bottom: 1px dashed #999;
      page-break-inside: avoid;
    }
    /* si vas a imprimir varios tickets, cada uno salta a la siguiente “hoja” */
    @media print{
      .ticket{ border:0; margin:0 auto; page-break-after: always; }
      .ticket:last-child{ page-break-after: auto; }
    }

    /* Encabezado con logo */
    .logo{ text-align:center; margin: 2px 0 4px; }
    .logo img{ width: 80px; height:auto; display:block; margin:0 auto; }
    .title{ text-align:center; font-weight:700; margin: 4px 0 6px; }

    /* Pares etiqueta/valor en grilla (más estable que flex/float) */
    .kv{
      display:grid;
      grid-template-columns: 1fr auto;
      gap: 8px;
      align-items:center;
      margin: 2px 0;
      word-break: break-word;
    }
    .kv .lbl{ font-weight:700; }
    .kv .val{ text-align:right; font-variant-numeric: tabular-nums; } /* números alineaditos */

    .muted{ color:#666; }
    hr{ border:0; border-top:1px dashed #999; margin:6px 0; }

    /* Firma */
    .firma{ margin-top: 12px; text-align:center; }
    .firma .line{ border-top:1px solid #000; margin: 12px 0 2px; }

    /* Utilidad */
    .u--strong{ font-weight:700; }
  </style>
</head>
<body>

@php
  // Usa public_path() para PDF (dompdf) y asset() como respaldo en navegador
  $logo = file_exists(public_path('logo.png')) ? public_path('logo.png') : asset('logo.png');
  $clienteNombres = $prestamo->clientes->pluck('nombre')->implode(', ');
  $dniCliente = optional($prestamo->clientes->first())->documento_identidad;
@endphp

<div class="ticket">
  <!-- Logo + título -->
  <div class="logo"><img src="{{ $logo }}" alt="Logo"></div>
  <div class="title">DESEMBOLSO CREDIJOYA</div>

  <!-- Cabecera -->
  <div class="kv"><span class="lbl">Fecha</span>
    <span class="val">{{$fechae}} {{$horae}}</span></div>
  <div class="kv"><span class="lbl">Crédito</span>
    <span class="val">#{{ $prestamo->id }}</span></div>
  <div class="kv"><span class="lbl">Cliente</span>
    <span class="val">{{ $clienteNombres }}</span></div>
  @if($dniCliente)
    <div class="kv"><span class="lbl">DNI</span>
      <span class="val">{{ $dniCliente }}</span></div>
  @endif

  <hr>

  <!-- Montos -->
  <div class="kv"><span class="lbl">Monto desembolso</span>
    <span class="val">S/ {{ number_format($montoPrestamo ?? 0, 2, '.', '') }}</span></div>
  <div class="kv"><span class="lbl">Deuda anterior</span>
    <span class="val">S/ {{ number_format($Deuda ?? 0, 2, '.', '') }}</span></div>
  <div class="kv"><span class="lbl">Neto a recibir</span>
    <span class="val u--strong">S/ {{ number_format($netoEntregado ?? 0, 2, '.', '') }}</span></div>
  <div class="kv">
    <span class="lbl">ITF {{ $aplicaITF ? '(0.005%)' : '(no aplica)' }}</span>
    <span class="val">S/ {{ number_format($itf ?? 0, 2, '.', '') }}</span>
  </div>

  <hr>

  <div class="kv">
    <span class="lbl">Pago final</span>
    <span class="val u--strong">S/ {{ number_format($netoAPagar ?? 0, 2, '.', '') }}</span>
  </div>
  <div class="muted" style="text-align:right;">(después de descontar ITF)</div>

  <!-- Firma -->
  <div class="firma">
    <div class="line"></div>
    <div>Firma del cliente</div>
  </div>

  <hr>
  <div class="muted" style="text-align:center;">Gracias por su preferencia</div>
</div>

</body>
</html>
