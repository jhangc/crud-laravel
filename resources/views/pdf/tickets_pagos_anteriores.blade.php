<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body{ font-family: Arial, sans-serif; font-size:10px; }
  .ticket{ width:200px; margin:0 auto 14px auto; padding-bottom:8px; border-bottom:1px dashed #999; }
  .hdr{ text-align:center; margin-bottom:6px; }
  .row{ display:flex; justify-content:space-between; margin:2px 0; }
  .bold{ font-weight:bold; }
</style>
</head>
<body>
@foreach($tickets as $t)
  <div class="ticket">
    <div class="hdr">
      <div class="bold">Comprobante de Pago</div>
      <div>{{ $fecha }}</div>
    </div>

    <div class="row"><span class="bold">Crédito:</span><span>#{{ $t['prestamo']->id }}</span></div>
    <div class="row"><span class="bold">Cliente:</span><span>{{ $t['cliente']->nombre }}</span></div>
    <div class="row"><span class="bold">DNI:</span><span>{{ $t['cliente']->documento_identidad }}</span></div>
    <div class="row"><span class="bold">Cuota N°:</span><span>{{ $t['ingreso']->numero_cuota }}</span></div>
    <div class="row"><span>Interés</span>
      <span>
        S/. {{ number_format(($t['cronograma']->interes ?? 0),2) }}
      </span>
    </div>
    <div class="row"><span>Amortización</span>
      <span>S/. {{ number_format(($t['cronograma']->amortizacion ?? 0),2) }}</span>
    </div>
    <div class="row"><span>Mora</span>
      <span>S/. {{ number_format(($t['ingreso']->monto_mora ?? 0),2) }}</span>
    </div>
    <div class="row bold"><span>Monto Pagado</span>
      <span>S/. {{ number_format($t['ingreso']->monto,2) }}</span>
    </div>

    <div class="row"><span>Próx. venc:</span>
      <span>{{ $t['fecha_sig'] }}</span>
    </div>
    @if($t['sig_cuota'])
      <div class="row"><span>Monto próx. cuota:</span>
        <span>S/. {{ number_format($t['sig_cuota']->monto,2) }}</span>
      </div>
    @endif

    <div class="hdr">_________________________<br>Firma</div>
  </div>
@endforeach
</body>
</html>
