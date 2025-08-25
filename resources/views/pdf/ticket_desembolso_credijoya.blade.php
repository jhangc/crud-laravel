<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Ticket Desembolso</title>
  <style>
    /* === Ticket térmico 58mm, alto dinámico === */
    html, body { margin: 0; padding: 0; }
    body {
      font-family: "DejaVu Sans","DejaVu Sans Mono", monospace;
      font-size: 10.5px; line-height: 1.25;
      word-wrap: break-word; overflow-wrap: anywhere;
    }
    .logo { text-align: center; margin: 2px 0 4px; }
    .logo img { max-width: 40%; height: auto; }
    .title { text-align: center; font-weight: 700; margin: 2px 0 6px; }

    .kv { display: flex; justify-content: space-between; gap: 6px; }
    .kv b { font-weight: 700; }
    .center { text-align: center; }
    .muted { color: #666; }

    hr { border: 0; border-top: 1px dashed #999; margin: 6px 0; }

    .firma { margin-top: 10px; }
    .firma .line { border-top: 1px solid #000; margin: 14px 0 2px; }

    /* Evita que el contenido “crezca” de más */
    .wrap { width: 100%; }
  </style>
</head>
<body>
  <div class="wrap">
    {{-- Logo local. Si usas dompdf, public_path() es más seguro que asset() --}}
    <div class="logo">
      <img src="{{ public_path('logo.png') }}" alt="Logo">
    </div>

    <div class="title">DESEMBOLSO CREDIJOYA</div>

    {{-- Cabecera mínima --}}
    <div class="kv"><b>Fecha</b><span>{{ now()->format('d/m/Y H:i:s') }}</span></div>
    <div class="kv"><b>Crédito</b><span>#{{ $prestamo->id }}</span></div>
    @php
      $cliente = $prestamo->clientes->pluck('nombre')->implode(', ');
      $dni     = optional($prestamo->clientes->first())->documento_identidad;
    @endphp
    <div class="kv"><b>Cliente</b><span>{{ $cliente }}</span></div>
    @if($dni)
      <div class="kv"><b>DNI</b><span>{{ $dni }}</span></div>
    @endif

    <hr>

    {{-- Montos --}}
    <div class="kv"><b>Monto desembolso</b><span>S/ {{ number_format($montoPrestamo, 2, '.', '') }}</span></div>
    <div class="kv"><b>Deuda anterior:</b><span>S/ {{ number_format($Deuda ?? 0, 2, '.', '') }}</span></div>
    <div class="kv"><b>Neto a recibir:</b><span>S/ {{ number_format($netoEntregado, 2, '.', '') }}</span></div>
    <div class="kv">
      <b>ITF :{{ $aplicaITF ? '' : '(no aplica)' }}</b>
      <span>S/ {{ number_format($itf, 2, '.', '') }}</span>
    </div>

    <hr>

    <div class="kv">
      <b>Pago final:</b>
      <span><b>S/ {{ number_format($netoAPagar, 2, '.', '') }}(Despues de descontar ITF)</b></span>
    </div>


    {{-- Firma --}}
    <div class="firma">
      <div class="line"></div>
      <div class="center">Firma del cliente</div>
    </div>

    <hr>
    <div class="center muted">Gracias por su preferencia</div>
  </div>
</body>
</html>
