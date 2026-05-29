<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Ticket de Pago - CrediJoya</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
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

    hr { border: 0; height: 0; margin: 5px 0; }

    table.kv { width: 100%; border-collapse: collapse; table-layout: fixed; }
    table.kv td { padding: 1px 0; vertical-align: top; word-wrap: break-word; overflow-wrap: break-word; }
    table.kv td.l { font-weight: bold; padding-right: 4px; width: 56%; }
    table.kv td.r { text-align: right; width: 44%; }
    tr.tot td { border-top: 1px solid #000; font-weight: bold; padding-top: 2px; }

    .tag {
      text-align: center; font-weight: bold; font-size: 9.5px;
      border: 1px solid #000; border-radius: 3px; padding: 2px 0; margin: 4px 0;
    }
    .sec { font-weight: bold; text-align: center; margin: 4px 0 1px; }
    .note { font-size: 8px; color: #333; margin: 2px 0 0; }

    .sign { margin-top: 14px; text-align: center; }
    .sign .line { border-top: 1px solid #000; width: 80%; margin: 16px auto 2px; }
  </style>
</head>
<body>
@php
  $logo = file_exists(public_path('logo.png')) ? public_path('logo.png') : asset('logo.png');

  $cliente  = $clienteNombre ?? ($credito->clientes[0]->nombre ?? '---');
  $fechaStr = \Carbon\Carbon::parse($fechaPago)->format('d/m/Y');
  $horaStr  = is_string($horaPago) ? $horaPago : optional($horaPago)->format('H:i');

  $tipo     = $desglose['tipo'] ?? '';
  $saldoR   = (float)($desglose['saldo_restante'] ?? 0);

  if ($tipo === 'abono') {
      $tipoTexto = $saldoR > 0.009 ? 'PAGO PARCIAL (ABONO)' : 'PAGO TOTAL DE CUOTA';
  } elseif ($tipo === 'interes') {
      $tipoTexto = 'PAGO DE INTERÉS';
  } else {
      $tipoTexto = 'PAGO DE CUOTA';
  }
@endphp

<div class="ticket">
  <div class="header">
    <img src="{{ $logo }}" alt="Logo">
    <div class="brand">Grupo Credipalmo</div>
    <div class="sub">Comprobante de Pago &mdash; CrediJoya</div>
  </div>

  <hr>

  <table class="kv">
    <tr><td class="l">Fecha</td><td class="r">{{ $fechaStr }} {{ $horaStr }}</td></tr>
    <tr><td class="l">Cr&eacute;dito</td><td class="r">#{{ $credito->id }}</td></tr>
    <tr><td colspan="2" class="l">Cliente: <span style="font-weight:normal">{{ $cliente }}</span></td></tr>
  </table>

  <div class="tag">{{ $tipoTexto }}</div>

  <hr>

  <table class="kv">
    <tr><td class="l">Mora pagada</td><td class="r">S/ {{ number_format((float)($desglose['mora_pagada'] ?? 0), 2) }}</td></tr>
    <tr><td class="l">Inter&eacute;s pagado</td><td class="r">S/ {{ number_format((float)($desglose['interes_pagado'] ?? 0), 2) }}</td></tr>
    <tr><td class="l">Capital pagado</td><td class="r">S/ {{ number_format((float)($desglose['capital_pagado'] ?? 0), 2) }}</td></tr>
    <tr class="tot"><td class="l">Total pagado</td><td class="r">S/ {{ number_format((float)($desglose['monto'] ?? 0), 2) }}</td></tr>
  </table>

  {{-- Bloque de abono / pago parcial --}}
  @if($tipo === 'abono')
    @php
      $moraDesde = !empty($desglose['mora_desde'])
                    ? \Carbon\Carbon::parse($desglose['mora_desde'])->format('d/m/Y')
                    : $fechaStr;
    @endphp
    <table class="kv">
      <tr class="tot"><td class="l">Saldo restante cuota</td><td class="r">S/ {{ number_format($saldoR, 2) }}</td></tr>
    </table>
    @if($saldoR > 0.009)
      @if((float)($desglose['mora_vigente'] ?? 0) > 0)
        <div class="note">La mora corre desde {{ $moraDesde }} sobre el saldo restante.</div>
      @endif
    @else
      <div class="note" style="text-align:center;">Cuota cancelada. Joyas liberadas.</div>
    @endif
  @endif

  {{-- Bloque de renovación / nuevo crédito --}}
  @if(!empty($desglose['renovado']) && !empty($desglose['nuevo_credito']))
    @php($nc = $desglose['nuevo_credito'])
    <hr>
    <div class="sec">Renovaci&oacute;n generada</div>
    <table class="kv">
      <tr><td class="l">Nuevo cr&eacute;dito</td><td class="r">#{{ $nc['id'] }}</td></tr>
      <tr><td class="l">TEA</td><td class="r">{{ $nc['tasa'] }}</td></tr>
      <tr><td class="l">Origen</td><td class="r">#{{ $nc['deuda_prev'] ?? $credito->id }}</td></tr>
      <tr><td class="l">Vencimiento</td><td class="r">{{ \Carbon\Carbon::parse($nc['vencimiento'])->format('d/m/Y') }}</td></tr>
    </table>
    @if(!empty($desglose['nueva_cuota']))
      @php($cq = $desglose['nueva_cuota'])
      <div class="sec">Pr&oacute;xima cuota</div>
      <table class="kv">
        <tr><td class="l">Cuota N&deg;</td><td class="r">{{ $cq['numero'] }} &middot; {{ \Carbon\Carbon::parse($cq['fecha'])->format('d/m/Y') }}</td></tr>
        <tr><td class="l">Capital</td><td class="r">S/ {{ number_format((float)$cq['amortizacion'], 2) }}</td></tr>
        <tr><td class="l">Inter&eacute;s</td><td class="r">S/ {{ number_format((float)$cq['interes'], 2) }}</td></tr>
        <tr class="tot"><td class="l">Monto</td><td class="r">S/ {{ number_format((float)$cq['monto'], 2) }}</td></tr>
      </table>
    @endif
  @endif

  <div class="sign">
    <div class="line"></div>
    Firma
  </div>

  <hr>
  <div class="note" style="text-align:center;">Gracias por su pago</div>
</div>

</body>
</html>
