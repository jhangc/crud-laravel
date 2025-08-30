<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Hoja Resumen CrediJoya</title>
  <style>
    /* ===== Página y centrado en A4 ===== */
    @page { size: A4; margin: 18mm 12mm 18mm; }
    html, body { margin: 0; padding: 0; font-family: Arial, sans-serif; color:#111; }
    .container { width: 180mm; margin: 0 auto; } /* centra todo el contenido */

    /* ===== Utilidades ===== */
    h1 { text-align: center; font-size: 16px; margin: 2mm 0 4mm; }
    .section { margin-top: 2mm; }
    .tiny { font-size: 10px; line-height: 1.35; }
    .right { text-align: right; }
    .center { text-align: center; }
    .nowrap { white-space: nowrap; }

    /* Anchuras de columnas */
    .w-60{width:60%}.w-50{width:50%}.w-40{width:40%}.w-33{width:33.33%}
    .w-30{width:30%}.w-25{width:25%}.w-20{width:20%}.w-15{width:15%}.w-10{width:10%}

    /* ===== Tablas ===== */
    .grid{
      width:100%;
      border-collapse: collapse;
      table-layout: fixed;
      word-wrap: break-word;
      font-size: 11px;
    }
    .grid th, .grid td{
      border: 0.6px solid #000;
      padding: 6px 6px;
      vertical-align: top;
    }
    .grid th{
      background: #f2f2f2;
      font-weight: bold;
    }
    .grid .num{ text-align: right; font-variant-numeric: tabular-nums; }
    .grid tfoot th, .grid tfoot td{
      font-weight: bold; background: #f8f8f8;
    }

    /* ===== Firmas ===== */
    .sign-table { width:100%; border-collapse:collapse; margin-top: 10mm; }
    .sign-table td { width:50%; text-align:center; vertical-align:bottom; padding-top:8mm; }
    .sign-line { border-top:1px solid #000; margin:0 auto 3mm; width:70%; height:0; }
  </style>
</head>
<body>
  @php
    $filasJoyas = isset($prestamo) ? $prestamo->joyas->count() : 0;
    $compact = $filasJoyas > 28;
  @endphp

  <div class="container ">
    <h1>HOJA RESUMEN DEL CONTRATO DE CRÉDITO  – CREDIJOYA</h1>

    <table class="grid section">
      <tr>
        <th class="w-20">Cliente</th>
        <td class="w-30">{{ $cliente?->nombre }}</td>
        <th class="w-20">Documento</th>
        <td class="w-30">{{ $cliente?->documento_identidad }}</td>
      </tr>
      <tr>
        <th>Dirección</th>
        <td>{{ $cliente?->direccion ?? '—' }}</td>
        <th>Ubicación</th>
        <td>
         
        </td>
      </tr>
      <tr>
        <th>Monto a Pagar</th>
        <td class="num">S/ {{ number_format($monto_prestamo,2,'.',',') }}</td>
        <th>Fecha de Desembolso</th>
        <td>{{ $fecha_desembolso }}</td>
      </tr>
      <tr>
        <th>Fecha de Vencimiento</th>
        <td>{{ $fecha_venc }}</td>
        <th>Plazo</th>
        <td>{{ $plazo_humano }}</td>
      </tr>
    </table>
    <table class="grid section">
      <tr>
        <th class="w-25">Tasa Efectiva Anual (TEA)</th>
        <td class="w-25 num">{{ number_format($tea,2) }}%</td>
        <th class="w-25">Tipo de Tasa</th>
        <td class="w-25">{{ $tipo_tasa }}</td>
      </tr>
    </table>
    <div class="section"><strong>COMISIONES</strong></div>
    <table class="grid">
      <thead>
        <tr>
          <th class="w-20">Categoría</th>
          <th class="w-40">Denominación / Servicio Incluido</th>
          <th class="w-20 center">Soles</th>
          <th class="w-20 center">Oportunidad</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Servicios asociados al crédito</td>
          <td>
            <strong>Envío de estado de cuenta</strong><br>
            Envío de información a solicitud del cliente (física o digital). Incluye reportes y estados mensuales o a requerimiento.
          </td>
          <td class="center">S/ {{ number_format($comision_envio,2,'.',',') }}</td>
          <td>Al tipo de cambio vigente, cuando corresponda.</td>
        </tr>
      </tbody>
    </table>

    <div class="section"><strong>SERVICIOS</strong></div>
    <table class="grid">
      <thead>
        <tr>
          <th class="w-60">Descripción</th>
          <th class="w-20 center">Porcentaje</th>
          <th class="w-20 center">Soles</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <strong>Servicio de Custodia</strong><br>
            Resguardo de las piezas de oro en caja de seguridad.
          </td>
          <td class="center">{{ number_format($porc_serv_custodia,2) }}%</td>
          <td class="center">—</td>
        </tr>
        <tr>
          <td colspan="3" class="tiny">
            <em>Oportunidad:</em>
            Se cobra <strong>26.82% mensual</strong> sobre la tasación <strong>solo si, después de cancelar la totalidad del crédito,</strong>
            el cliente <strong>no recoge sus joyas dentro de 15 días</strong>. El pago se realiza al momento del recojo.
          </td>
        </tr>
      </tbody>
    </table>
    <div class="section"><strong>Descripción de las Piezas de Oro</strong></div>
    <table class="grid">
      <thead>
        <tr>
          <th class="w-10 center">N°</th>
          <th class="w-40">Detalle</th>
          <th class="w-15 right">Precio (g)</th>
          <th class="w-15 right">Oro Bruto (g)</th>
          <th class="w-15 right">Oro Neto (g)</th>
          <th class="w-15 right">Valor Tasación (S/)</th>
        </tr>
      </thead>
      <tbody>
        @forelse($prestamo->joyas as $idx => $j)
          <tr>
            <td class="center">{{ $idx+1 }}</td>
            <td>
              {{ $j->descripcion ?? '—' }}
              @if(!empty($j->kilate)) — {{ $j->kilate }}K @endif
              @if(!empty($j->piezas)) — {{ $j->piezas }} pieza(s) @endif
            </td>
            <td class="num">{{ $j->precio_gramo !== null ? number_format((float)$j->precio_gramo,2,'.',',') : '—' }}</td>
            <td class="num">{{ number_format((float)($j->peso_bruto ?? 0), 2, '.', ',') }}</td>
            <td class="num">{{ number_format((float)($j->peso_neto ?? 0), 2, '.', ',') }}</td>
            <td class="num">{{ number_format((float)($j->valor_tasacion ?? 0), 2, '.', ',') }}</td>
          </tr>
        @empty
          <tr><td colspan="6" class="center">Sin joyas registradas.</td></tr>
        @endforelse
      </tbody>
      <tfoot>
        <tr>
          <th colspan="3" class="right">Totales</th>
          <td class="num">{{ number_format($oro_bruto,2,'.',',') }}</td>
          <td class="num">{{ number_format($oro_neto,2,'.',',') }}</td>
          <td class="num">{{ number_format($valor_tasacion,2,'.',',') }}</td>
        </tr>
      </tfoot>
    </table>

    @if(!empty($mostrar_cronograma))
      <div class="section"><strong>Cronograma</strong></div>
      <table class="grid">
        <thead>
          <tr>
            <th class="w-10 center">#</th>
            <th class="w-20 center">Vencimiento</th>
            <th class="w-20 right">Amortización</th>
            <th class="w-20 right">Interés</th>
            <th class="w-20 right">Cuota</th>
          </tr>
        </thead>
        <tbody>
          @foreach($cronograma as $c)
            <tr>
              <td class="center">{{ $c->numero }}</td>
              <td class="center">{{ $c->fecha }}</td>
              <td class="num">{{ number_format((float)$c->amortizacion,2,'.',',') }}</td>
              <td class="num">{{ number_format((float)$c->interes,2,'.',',') }}</td>
              <td class="num">{{ number_format((float)$c->monto,2,'.',',') }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <th colspan="2" class="right">Totales</th>
            <td class="num">{{ number_format($totAmort,2,'.',',') }}</td>
            <td class="num">{{ number_format($totInteres,2,'.',',') }}</td>
            <td class="num">{{ number_format($totCuota,2,'.',',') }}</td>
          </tr>
        </tfoot>
      </table>
    @endif
    <table class="sign-table">
      <tr>
        <td>
          <div class="sign-line"></div>
          <div>Auxiliar de Operaciones</div>
          <div class="tiny">{{ $asesor->name ?? '—' }}</div>
        </td>
        <td>
          <div class="sign-line"></div>
          <div>Cliente</div>
          <div class="tiny">{{ $cliente?->nombre }} — {{ $cliente?->documento_identidad }}</div>
        </td>
      </tr>
    </table>
    <div class="section tiny">
      <strong>Notas y Declaraciones:</strong>
      <ul>
        <li>Las piezas de oro entregadas por el cliente <strong>quedan en garantía mobiliaria</strong> a favor de GRUPO CREDIPALMO hasta la <strong>cancelación total</strong> del crédito.</li>
        <li><strong>Custodia:</strong> cargo del <strong>26.82% mensual</strong> sobre la tasación, aplicable únicamente si, luego de cancelar el crédito, el cliente no recoge sus joyas dentro de <strong>15 días</strong>. Se paga al momento del recojo.</li>
        <li>El cliente declara conocer y aceptar las <strong>normas aplicables</strong> al producto CrediJoya y las políticas internas vigentes.</li>
      </ul>
    </div>
  </div>
</body>
</html>

