<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Tickets</title>
<style>
  /* Base */
  * { box-sizing: border-box; }
  body{ font-family: Arial, sans-serif; font-size:10px; margin:0; padding:0; }

  /* Contenedor del ticket (200px como pediste) */
  .ticket{
    width:200px;
    margin:0 auto 14px auto;
    padding-bottom:8px;
    border-bottom:1px dashed #999;
    page-break-inside: avoid;
  }

  /* Header con logo */
  .hdr{ text-align:center; margin-bottom:8px; }
  .hdr img{ width:80px; height:auto; display:block; margin:0 auto 4px auto; }
  .hdr .title{ font-weight:bold; font-size:12px; }
  .hdr .subtitle{ font-size:10px; margin-top:2px; }

  /* Filas con 2 columnas fijas (mejor que floats) */
  .row{
    display:grid;
    grid-template-columns: 1fr auto;
    gap:8px;
    align-items:center;
    margin:2px 0;
    word-break: break-word;
  }
  .label{ font-weight:bold; }
  .right{ text-align:right; }

  /* Firma */
  .firma{ text-align:center; margin-top:10px; }
  .firma .line{ border-top:1px solid #000; margin:5px 0; }

  /* Impresión: cada ticket en una hoja/salto */
  @media print{
    .ticket{
      border:0;
      margin:0 auto;
      page-break-after: always;
    }
    .ticket:last-child{
      page-break-after: auto;
    }
  }
</style>
</head>
<body>

@foreach($tickets as $t)
  <div class="ticket">
    <div class="hdr">
      <img src="{{ asset('logo.png') }}" alt="Logo">
      <div class="title">Grupo Credipalmo</div>
      <div class="subtitle">Comprobante de Pago</div>
    </div>

    <div class="row"><span class="label">Fecha</span>
      <span class="right">{{ \Carbon\Carbon::parse($t['ingreso']->created_at)->format('d/m/Y H:i:s') }}</span>
    </div>
    <div class="row"><span class="label">Crédito</span>
      <span class="right">#{{ $t['prestamo']->id }}</span>
    </div>
    <div class="row"><span class="label">Cliente</span>
      <span class="right">{{ $t['cliente']->nombre }}</span>
    </div>
    <div class="row"><span class="label">DNI</span>
      <span class="right">{{ $t['cliente']->documento_identidad }}</span>
    </div>
    <div class="row"><span class="label">Cuota N°</span>
      <span class="right">{{ $t['ingreso']->numero_cuota }}</span>
    </div>
    <div class="row"><span class="label">Vcto. Cuota</span>
      <span class="right">{{ $t['cronograma']->fecha }}</span>
    </div>
    <div class="row"><span class="label">Monto de Cuota </span>
      <span class="right">{{$t['cronograma']->monto}}</span>
    </div>

    <div class="row"><span>Interés</span>
      <span class="right">S/. {{ number_format(($t['cronograma']->interes ?? 0),2) }}</span>
    </div>
    <div class="row"><span>Amortización</span>
      <span class="right">S/. {{ number_format(($t['cronograma']->amortizacion ?? 0),2) }}</span>
    </div>
    <div class="row"><span>Mora</span>
      <span class="right">S/. {{ number_format(($t['ingreso']->monto_mora ?? 0),2) }}</span>
    </div>

    {{-- Adelanto / Total pagado opcional --}}
    @php $diferencia = $t['diferencia'] ?? 0; @endphp
    @if($diferencia > 0)
      <div class="row"><span class="label">Adelanto</span>
        <span class="right">S/. {{ number_format($diferencia,2) }}</span>
      </div>
      <div class="row"><span class="label">Total Pagado</span>
        <span class="right">S/. {{ number_format($diferencia + $t['ingreso']->monto,2) }}</span>
      </div>
    @endif

    <div class="row label"><span>Monto Pagado</span>
      <span class="right">S/. {{ number_format($t['ingreso']->monto,2) }}</span>
    </div>

    {{-- Observación de pago de capital --}}
    @php $pc = $t['cronograma']->pago_capital ?? null; @endphp
    <div class="row"><span class="label">Observaciones</span>
      <span class="right">
        @if ($pc === null)
          —
        @else
          Pago Capital - {{ $pc == 1 ? 'Reducir cuota' : 'Reducir plazo' }}
        @endif
      </span>
    </div>

    {{-- Próxima cuota --}}
    <div class="row"><span>Próx. venc.</span>
      <span class="right">{{ $t['fecha_sig'] }}</span>
    </div>
    @if($t['sig_cuota'])
      <div class="row"><span>Monto próx. cuota</span>
        <span class="right">S/. {{ number_format($t['sig_cuota']->monto,2) }}</span>
      </div>
    @endif

    <div class="firma">
      <div class="line"></div>
      <div>Firma</div>
    </div>
  </div>
@endforeach

</body>
</html>
