<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
  /* ===== Config térmica ===== */
  @page { margin: 2; }
  html, body { margin: 2; padding: 2; }

  body{
    font-family: "DejaVu Sans","DejaVu Sans Mono", Arial, sans-serif;
    font-size: 10.5px;
    line-height: 1.28;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  /* Utilidades */
  .c{ text-align:center }
  .b{ font-weight:700 }
  .sm{ font-size:10px }
  .mt6{ margin-top:6px }

  /* Encabezado */
  .logo{ text-align:center; margin: 4px 0 6px; }
  .logo img{
    display:block;
    margin: 0 auto;
    /* Evita desbordes en 58mm y 80mm */
    max-width: 50%;
    height: auto;
    object-fit: contain;
  }

  /* Grid etiqueta / valor */
  .kv{
    display:grid;
    grid-template-columns: 1fr auto;
    gap: 6px;
    align-items:center;
    margin: 2px 0;
    word-break: break-word;
  }
  .kv .b{ font-weight:700; }
  .kv .val{ text-align:right; font-variant-numeric: tabular-nums; }

  hr{ border:0; border-top:1px dashed #999; margin:6px 0; }
  .muted{ color:#666; }

  /* Tabla simple */
  table{ width:100%; border-collapse:collapse; }
  th, td{ padding: 3px 2px; border-bottom:1px solid #eee; }
  th{ text-align:left; }
</style>
</head>
<body>

  {{-- HEADER --}}
  @php
    // Para Dompdf, usa path absoluto si existe; de lo contrario, intenta con asset()
    $logoPath = public_path('logo.png');
    $logo = file_exists($logoPath) ? $logoPath : (filter_var(asset('logo.png'), FILTER_VALIDATE_URL) ? asset('logo.png') : null);
  @endphp

  @if($logo)
    <div class="logo">
      <img src="{{ $logo }}" alt="Logo">
    </div>
  @endif

  <div class="c b mt6">RECIBO DE CUSTODIA</div>
  <hr>

  {{-- METADATA --}}
  <div class="kv sm">
    <div>Fecha: <span class="b">{{ $fecha }}</span></div>
    <div>Hora: <span class="b">{{ $hora }}</span></div>
  </div>

  <hr>

  {{-- DETALLE DEL PAGO --}}
  <div class="kv">
    <div>Motivo</div>
    <div class="b">Custodia de Joyas</div>
  </div>
  <div class="kv">
    <div>Monto del ticket</div>
    <div class="b">S/ {{ number_format($ing->monto,2,'.','') }}</div>
  </div>

  {{-- RESUMEN FINANCIERO --}}
  <div class="mt6 b">Estado de custodia</div>
  <table class="sm">
    <tr>
      <td>% mensual</td>
      <td style="text-align:right">{{ number_format($estado['porcentaje_mensual'] ?? 0,2) }}% (prorrateo diario)</td>
    </tr>
    <tr>
      <td>Días cobrados</td>
      <td style="text-align:right">{{ $estado['dias_cobra'] ?? 0 }} (desde día {{ $estado['desde_dia'] ?? 16 }})</td>
    </tr>
    <tr>
      <td>Acumulado a la fecha</td>
      <td class="b" style="text-align:right">S/ {{ number_format($estado['acumulado'] ?? 0,2,'.','') }}</td>
    </tr>
    <tr>
      <td>Pagado total</td>
      <td style="text-align:right">S/ {{ number_format($estado['pagado'] ?? 0,2,'.','') }}</td>
    </tr>
    <tr>
      <td>Saldo pendiente</td>
      <td class="b" style="text-align:right">S/ {{ number_format($estado['pendiente'] ?? 0,2,'.','') }}</td>
    </tr>
  </table>

  <hr>
    <div class="c sm">
    Gracias por su pago<br>
    <span class="muted">El cobro de custodia aplica a partir del día 16 después de cancelado el crédito.</span>
  </div>

</body>
</html>