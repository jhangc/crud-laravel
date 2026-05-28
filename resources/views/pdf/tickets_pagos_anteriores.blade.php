<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Tickets</title>
<style>
  @page { margin: 0; }
  html, body { margin: 0; padding: 0; }

  body {
    font-family: "DejaVu Sans", Arial, sans-serif;
    font-size: 9px;
    color: #000;
    line-height: 1.25;
  }

  .ticket { padding: 8px 14px; page-break-after: always; }
  .ticket:last-child { page-break-after: auto; }

  .header { text-align: center; margin-bottom: 4px; }
  .header img { width: 62px; height: auto; }
  .brand { font-size: 11px; font-weight: bold; margin-top: 2px; }
  .sub { font-size: 9px; margin-top: 1px; }

  hr { border: 0; border-top: 1px dashed #000; margin: 5px 0; }

  table.kv { width: 100%; border-collapse: collapse; table-layout: fixed; }
  table.kv td { padding: 1px 0; vertical-align: top; word-wrap: break-word; overflow-wrap: break-word; }
  table.kv td.l { font-weight: bold; padding-right: 4px; width: 56%; }
  table.kv td.r { text-align: right; width: 44%; }
  tr.tot td { border-top: 1px solid #000; font-weight: bold; padding-top: 2px; }

  .sec { font-weight: bold; text-align: center; margin: 4px 0 1px; }

  .sign { margin-top: 14px; text-align: center; }
  .sign .line { border-top: 1px solid #000; width: 80%; margin: 16px auto 2px; }
</style>
</head>
<body>

@foreach($tickets as $t)
  @php $diferencia = $t['diferencia'] ?? 0; $pc = $t['cronograma']->pago_capital ?? null; @endphp
  <div class="ticket">
    <div class="header">
      <img src="{{ asset('logo.png') }}" alt="Logo">
      <div class="brand">Grupo Credipalmo</div>
      <div class="sub">Comprobante de Pago</div>
    </div>

    <hr>

    <table class="kv">
      <tr><td class="l">Fecha</td><td class="r">{{ \Carbon\Carbon::parse($t['ingreso']->created_at)->format('d/m/Y H:i') }}</td></tr>
      <tr><td class="l">Cr&eacute;dito</td><td class="r">#{{ $t['prestamo']->id }}</td></tr>
      <tr><td class="l">DNI</td><td class="r">{{ $t['cliente']->documento_identidad }}</td></tr>
      <tr><td class="l">Cuota N&deg;</td><td class="r">{{ $t['ingreso']->numero_cuota }}</td></tr>
      <tr><td class="l">Vcto. cuota</td><td class="r">{{ \Carbon\Carbon::parse($t['cronograma']->fecha)->format('d/m/Y') }}</td></tr>
      <tr><td colspan="2" class="l">Cliente: <span style="font-weight:normal">{{ $t['cliente']->nombre }}</span></td></tr>
    </table>

    <hr>

    <table class="kv">
      <tr><td class="l">Monto de cuota</td><td class="r">S/ {{ number_format($t['cronograma']->monto, 2) }}</td></tr>
      <tr><td class="l">Inter&eacute;s</td><td class="r">S/ {{ number_format($t['cronograma']->interes ?? 0, 2) }}</td></tr>
      <tr><td class="l">Amortizaci&oacute;n</td><td class="r">S/ {{ number_format($t['cronograma']->amortizacion ?? 0, 2) }}</td></tr>
      <tr><td class="l">Mora</td><td class="r">S/ {{ number_format($t['ingreso']->monto_mora ?? 0, 2) }}</td></tr>
      <tr class="tot"><td class="l">Monto pagado</td><td class="r">S/ {{ number_format($t['ingreso']->monto, 2) }}</td></tr>
      @if($diferencia > 0)
        <tr><td class="l">Adelanto</td><td class="r">S/ {{ number_format($diferencia, 2) }}</td></tr>
        <tr class="tot"><td class="l">Total pagado</td><td class="r">S/ {{ number_format($diferencia + $t['ingreso']->monto, 2) }}</td></tr>
      @endif
    </table>

    @if($pc !== null)
      <div class="note" style="font-size:8px;color:#333;margin-top:2px;">Observaci&oacute;n: Pago Capital &mdash; {{ $pc == 1 ? 'Reducir cuota' : 'Reducir plazo' }}</div>
    @endif

    <hr>

    <div class="sec">Pr&oacute;xima cuota</div>
    <table class="kv">
      <tr><td class="l">Monto</td><td class="r">S/ {{ $t['sig_cuota'] ? number_format($t['sig_cuota']->monto, 2) : '0.00' }}</td></tr>
      <tr><td class="l">Vence</td><td class="r">{{ $t['fecha_sig'] ?? '—' }}</td></tr>
    </table>

    <div class="sign">
      <div class="line"></div>
      Firma
    </div>
  </div>
@endforeach

</body>
</html>
