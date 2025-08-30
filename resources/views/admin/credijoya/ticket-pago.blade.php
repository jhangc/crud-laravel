<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Ticket de Pago - CrediJoya</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
    /* ===== Config térmica ===== */
    @page { margin: 0; }               /* sin márgenes al imprimir */
    html, body { margin: 0; padding: 0; }

    body{
      font-family: "DejaVu Sans","DejaVu Sans Mono", Arial, sans-serif;
      font-size: 10.5px;
      line-height: 1.28;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    /* Contenedor ticket: se ajusta por mm desde el controlador (widthMm) */
    .ticket{
      width: {{ ($widthMm ?? 58) }}mm;    /* 58mm o 80mm */
      max-width: {{ ($widthMm ?? 58) * 3.78 }}px; /* fallback en pantalla */
      margin: 0 auto 10px auto;
      padding: 8px 10px 10px;
      border-bottom: 1px dashed #999;
      page-break-inside: avoid;
    }
    @media print{
      .ticket{ border:0; margin:0 auto; page-break-after: always; }
      .ticket:last-child{ page-break-after: auto; }
    }

    /* Encabezado */
    .logo{ text-align:center; margin: 2px 0 4px; }
    .logo img{ width: 90px; height:auto; display:block; margin:0 auto; }
    .title{ text-align:center; font-weight:700; margin: 4px 0 6px; }

    /* Grid etiqueta / valor */
    .kv{
      display:grid;
      grid-template-columns: 1fr auto;
      gap: 6px;
      align-items:center;
      margin: 2px 0;
      word-break: break-word;
    }
    .kv .lbl{ font-weight:700; }
    .kv .val{ text-align:right; font-variant-numeric: tabular-nums; }

    hr{ border:0; border-top:1px dashed #999; margin:6px 0; }
    .muted{ color:#666; }
    .mono{ font-family: "DejaVu Sans Mono", Consolas, monospace; }
    .small{ font-size: 10px; }
    .right{ text-align: right; }
    .center{ text-align: center; }
    .bold{ font-weight: 700; }

    /* Tabla simple */
    table{ width:100%; border-collapse:collapse; }
    th, td{ padding: 3px 2px; border-bottom:1px solid #eee; }
    th{ text-align:left; }
  </style>
</head>
<body>
@php
  // Logo: para PDF (dompdf) usar path absoluto; en navegador sirve asset()
  $logo = file_exists(public_path('logo.png')) ? public_path('logo.png') : asset('logo.png');

  $cliente  = $clienteNombre ?? ($credito->clientes[0]->nombre ?? '---');
  $fechaStr = \Carbon\Carbon::parse($fechaPago)->format('d/m/Y');
  $horaStr  = is_string($horaPago) ? $horaPago : optional($horaPago)->format('H:i:s');

  $moraPag   = number_format((float)($desglose['mora_pagada'] ?? 0), 2, '.', '');
  $intPag    = number_format((float)($desglose['interes_pagado'] ?? 0), 2, '.', '');
  $capPag    = number_format((float)($desglose['capital_pagado'] ?? 0), 2, '.', '');
  $totalPag  = number_format((float)($desglose['monto'] ?? 0),        2, '.', '');
  $saldoRest = number_format((float)($saldo ?? 0),                     2, '.', '');
@endphp

<div class="ticket">
  <!-- Logo + título -->
  <div class="logo"><img src="{{ $logo }}" alt="Logo"></div>
  <div class="title">TICKET DE PAGO - CREDIJOYA</div>

  <!-- Cabecera -->
  <div class="kv"><span class="lbl">Fecha:</span><span class="val">{{ $fechaStr }} {{ $horaStr }}</span></div>
  <div class="kv"><span class="lbl">Crédito:</span><span class="val">#{{ $credito->id }}</span></div>
  <div class="kv"><span class="lbl">Cliente:</span><span class="val">{{ $cliente }}</span></div>
  @if(!empty($desglose['modo']))
  <div class="kv"><span class="lbl">Modo:</span><span class="val">{{ strtoupper($desglose['modo']) }}</span></div>
  @endif
  <div class="kv"><span class="lbl">Tipo:</span><span class="val">{{ strtoupper($desglose['tipo'] ?? 'PARCIAL') }}</span></div>

  <hr>

  <!-- Desglose -->
  <table>
    <tr><th>Concepto</th><th class="right">Importe (S/)</th></tr>
    <tr><td>Mora pagada:</td>    <td class="right">{{ $moraPag }}</td></tr>
    <tr><td>Interés pagado:</td> <td class="right">{{ $intPag }}</td></tr>
    <tr><td>Capital pagado:</td> <td class="right">{{ $capPag }}</td></tr>
    <tr><td class="bold">Total:</td><td class="right bold">{{ $totalPag }}</td></tr>
  </table>

  <!-- Estado del crédito -->

  <!-- Block de RENOVACIÓN / NUEVO CRÉDITO -->
  @if(!empty($desglose['renovado']) && !empty($desglose['nuevo_credito']))
    @php($nc = $desglose['nuevo_credito'])
    <hr>
    <div class="bold center">RENOVACIÓN GENERADA</div>
    <div class="kv"><span class="lbl">Nuevo crédito</span><span class="val">#{{ $nc['id'] }}</span></div>
    <div class="kv"><span class="lbl">Origen</span><span class="val">#{{ $nc['deuda_prev'] ?? $credito->id }}</span></div>
    <div class="kv"><span class="lbl">Vencimiento</span><span class="val">{{ \Carbon\Carbon::parse($nc['vencimiento'])->format('d/m/Y') }}</span></div>

    @if(!empty($desglose['nueva_cuota']))
      @php($cq = $desglose['nueva_cuota'])
      <div class="mono small" style="margin-top:4px">
        Cuota #{{ $cq['numero'] }} — {{ \Carbon\Carbon::parse($cq['fecha'])->format('d/m/Y') }}<br>
        Capital: S/ {{ number_format((float)$cq['amortizacion'],2,'.','') }},
        Interés: S/ {{ number_format((float)$cq['interes'],2,'.','') }},
        Monto: S/ {{ number_format((float)$cq['monto'],2,'.','') }}
      </div>
    @endif
  @endif
  <hr>
  <div class="center muted small">Gracias por su pago</div>
</div>

</body>
</html>
