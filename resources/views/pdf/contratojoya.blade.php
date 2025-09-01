<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Contrato CrediJoya</title>
  <style>
    body { font-family: Arial, sans-serif; line-height: 1.6; }
    .header { position: fixed; top: -70px; left: -45px; }
    .header h3 { padding: 5px; background-color: #044B6B; color: white; font-style: italic; }
    .container { margin: 0 20px; }
    h1 { text-align: center; color: black; font-size: 18px; margin-top: 8mm; }
    h2 { color: black; font-size: 15px; margin-top: 6mm; }
    p, li, td { font-size: 12px; line-height: 1.55; text-align: justify; }
    .muted{ color:#555; }
    .grid{ width:100%; border-collapse:collapse; margin-top:4mm; }
    .grid th,.grid td{ border:0.6px solid #000; padding:6px; font-size:12px; vertical-align:top; }
    .grid th{ background:#f5f5f5; text-align:left; }
    .num{ text-align:right; font-variant-numeric: tabular-nums; }
    .tiny{ font-size:10px; line-height:1.35; }
    .signs{ width:100%; margin-top:14mm; }
    .signs td{ width:50%; text-align:center; vertical-align:bottom; padding-top:8mm; }
    .line{ border-top:1px solid #000; width:70%; height:0; margin:0 auto 3mm; }
    .center{ text-align:center; }
  </style>
</head>
<body>
  <header class="header">
    <h3>Grupo Credipalmo</h3>
  </header>

  <div class="container">
    <h1>CONTRATO PRIVADO DE CRÉDITO CON GARANTÍA MOBILIARIA – CREDIJOYA</h1>

    <!-- Identificación de la ENTIDAD -->
    <p class="tiny muted">
      <strong>GRUPO CREDIPALMO S.A.C.</strong>, RUC <strong>20610069542</strong>, con domicilio en
      <strong>URB. Monterrico Etapa V Mz 15 Lote 9, Chiclayo, Lambayeque</strong>.
    </p>

    <!-- Datos principales del crédito -->
    <table class="grid">
      <tr>
        <th>Cliente</th>
        <td>{{ $credito_cliente->clientes->nombre }}</td>
        <th>Documento</th>
        <td>{{ $credito_cliente->clientes->documento_identidad }}</td>
      </tr>
      <tr>
        <th>Dirección</th>
        <td colspan="3">{{ $credito_cliente->clientes->direccion ?? '—' }}</td>
      </tr>
      <tr>
        <th>Monto del Préstamo</th>
        <td colspan="3" class="num">S/ {{ number_format((float)$prestamo->monto_total,2,'.',',') }}</td>
      </tr>
      <tr>
        <th>TEA</th>
        <td>{{ number_format((float)$prestamo->tasa,2) }}%</td>
        <th>Plazo</th>
        <td>{{ $prestamo->tiempo }} {{ strtolower($prestamo->recurrencia ?? 'meses') }}</td>
      </tr>
      <tr>
        <th>Fecha de Desembolso</th>
        <td>{{ $prestamo->fecha_desembolso }}</td>
        <th>Vencimiento</th>
        <td>{{ $prestamo->proximo_vencimiento ?? $prestamo->fecha_fin }}</td>
      </tr>
    </table>

    <p class="mt-6">
      Conste por el presente documento el contrato de crédito con garantía mobiliaria que celebran, de una parte
      <strong>GRUPO CREDIPALMO S.A.C.</strong> (en adelante, la <strong>ENTIDAD</strong>), y de la otra,
      <strong>{{ $credito_cliente->clientes->nombre }}</strong> con DNI {{ $credito_cliente->clientes->documento_identidad }}
      (en adelante, el <strong>CLIENTE</strong>), conforme a las cláusulas siguientes:
    </p>

    <h2>PRIMERA: Crédito</h2>
    <p>
      La ENTIDAD otorga al CLIENTE un crédito por el monto y condiciones señalados en el cuadro precedente y en el
      cronograma de pago que forma parte integrante del presente contrato. El crédito se destina a necesidades personales
      del CLIENTE bajo la modalidad <strong>CrediJoya</strong>.
    </p>

    <h2>SEGUNDA: Garantía mobiliaria – Piezas de oro</h2>
    <p>
      Para asegurar el cumplimiento del crédito, el CLIENTE constituye a favor de la ENTIDAD una
      <strong>garantía mobiliaria</strong> sobre las piezas de oro descritas en la Hoja Resumen y/o anexo de inventario,
      que quedan <strong>en custodia</strong> de la ENTIDAD hasta la cancelación total de la deuda. La constitución,
      perfeccionamiento y ejecución de esta garantía se rigen por el Decreto Legislativo N.º 1400 y normas conexas.
    </p>
    <p>
      El CLIENTE, de manera voluntaria a la firma del presente documento, <strong>entrega a CREDIPALMO la posesión</strong>
      del bien o bienes otorgados en garantía mobiliaria. Declara encontrarse <strong>legitimado</strong> para constituir
      la garantía, sin impedimento legal alguno. La garantía afecta a las joyas descritas en la Hoja Resumen, sus partes
      integrantes y accesorios y eventualmente el precio de venta; garantiza la deuda principal otorgada en préstamo, los
      intereses compensatorios y moratorios, las comisiones, gastos y servicios de custodia pactados en este contrato.
    </p>

    <h2>TERCERA: Tasación e inventario</h2>
    <p>
      Las piezas son tasadas por personal autorizado de la ENTIDAD con base en kilataje, peso, merma y condiciones
      físicas, dejándose constancia de: descripción, kilates, piezas, peso bruto, peso neto y valor de tasación. El
      CLIENTE declara su conformidad con la tasación y firma del inventario. La ENTIDAD asigna código único a cada pieza
      y conserva el inventario como parte del presente contrato.
    </p>

    <h2>CUARTA: Entrega y custodia</h2>
    <p>
      Las piezas quedan bajo custodia en ambientes de seguridad de la ENTIDAD, asumiendo ésta la diligencia debida de un
      depositario responsable. En caso fortuito o fuerza mayor, la responsabilidad se rige por la normativa aplicable.
    </p>
        <br>
    <h2>QUINTA: Intereses, comisiones y gastos</h2>
    <p>
      El crédito devenga intereses compensatorios a la <strong>TEA {{ number_format((float)$prestamo->tasa,2) }}%</strong>.
      Las comisiones y gastos aplicables (por ejemplo, envío de estado de cuenta) se encuentran detallados en la Hoja
      Resumen y se incorporan a este contrato. El CLIENTE declara haber sido informado y haberlos aceptado.
    </p>

    <h2>SEXTA: Pagos, prórrogas y cronograma</h2>
    <p>
      El plazo del crédito es <strong>a una sola cuota</strong>. Al llegar la fecha de vencimiento indicada en la Hoja
      Resumen, el CLIENTE podrá <strong>ampliar el plazo</strong> siempre que pague los intereses compensatorios, moratorios,
      comisiones, impuestos (de corresponder) y <strong>parte del capital</strong>. El nuevo vencimiento constará en el
      comprobante que emitirá CREDIPALMO y se entregará al CLIENTE. El <strong>número de prórrogas es ilimitado</strong> y
      las condiciones pactadas en este contrato se mantienen vigentes hasta el pago total de las obligaciones.
    </p>
    <p>
      Los pagos se realizan en moneda nacional, en efectivo o a través de los medios que disponga la ENTIDAD. La imputación
      de pagos será en el siguiente orden: gastos/moras, intereses y capital.
    </p>

    <h2>SÉPTIMA: Mora, cobranza y adjudicación</h2>
    <p>
      El retraso genera mora y gastos de cobranza conforme a la Hoja Resumen y normativa vigente. La ENTIDAD podrá
      comunicar al CLIENTE los importes actualizados y, de persistir el incumplimiento, <strong>ejecutar la garantía
      mobiliaria</strong> según Ley.
    </p>
    <p>
      En cualquier momento posterior al incumplimiento de pago, CREDIPALMO podrá <strong>adjudicarse el lote de oro</strong>
      de acuerdo a la <strong>deuda total existente</strong> a dicha fecha, lo que es aceptado expresamente por el CLIENTE.
      Si el valor fuese menor o insuficiente, CREDIPALMO se reserva el derecho a cobrar la diferencia. En tanto no se cancele
      dicha diferencia y/o demás obligaciones pactadas, el CLIENTE podrá ser <strong>reportado a centrales de riesgo</strong>
      y/o ante autoridades correspondientes.
    </p>
    <p>
      Las partes acuerdan que, en caso de incumplimiento de pago de cualquiera de las obligaciones asumidas por el CLIENTE
      <strong>teniendo uno o varios créditos diferentes a CrediJoya</strong>, CREDIPALMO podrá <strong>proceder a la venta del
      lote de oro</strong> para cancelar los otros créditos distintos al CrediJoya.
    </p>

    <h2>OCTAVA: Devolución de joyas y cargo de custodia</h2>
    <p>
      Una vez <strong>cancelado totalmente</strong> el crédito, el CLIENTE tiene <strong>15 (quince) días</strong> para recoger sus
      piezas. Vencido dicho plazo, se aplica un cargo de <strong>custodia del 26.82% mensual sobre la tasación</strong>, calculado
      hasta la fecha efectiva de recojo, el cual deberá pagarse para la entrega. La ENTIDAD mantiene <strong>derecho de retención</strong>
      hasta dicho pago.
    </p>

    <h2>NOVENA: Renovación</h2>
    <p>
      El CLIENTE podrá solicitar renovación del crédito. De aprobarse, se emitirá un nuevo crédito con su respectivo
      cronograma, pudiendo mantenerse la garantía sobre las mismas piezas previo inventario y tasación actualizados.
    </p>

    <h2>DÉCIMA: Comunicaciones y datos personales</h2>
    <p>
      Las notificaciones se cursarán a los domicilios, teléfonos y/o correos informados por el CLIENTE, quien se obliga a
      mantenerlos actualizados. El CLIENTE autoriza el tratamiento de sus datos personales conforme a la normativa vigente
      y a la política de privacidad de la ENTIDAD.
    </p>

    <h2>DÉCIMO PRIMERA: Resolución y ejecución de garantía</h2>
    <p>
      Ante incumplimiento esencial, la ENTIDAD podrá resolver el contrato y ejecutar la garantía mobiliaria por las vías
      previstas en el D. Leg. 1400, sin perjuicio de las acciones legales correspondientes.
    </p>

    <h2>DÉCIMO SEGUNDA: Solución de controversias</h2>
    <p>
      Las partes procurarán resolver de buena fe cualquier controversia. De persistir, se someten a
      <strong>arbitraje de derecho</strong> administrado por el <em>Colegio de Abogados de Lambayeque</em>, conforme a su
      reglamento. El laudo arbitral será definitivo e inapelable.
    </p>

    <h2>DÉCIMO TERCERA: Disposiciones finales</h2>
    <ul>
      <li>El CLIENTE declara haber recibido la Hoja Resumen y comprender términos, tasas, comisiones y condiciones.</li>
      <li>La Hoja Resumen, el inventario/tasación y el cronograma forman parte integrante de este contrato.</li>
    </ul>

    <h2>DÉCIMO CUARTA: Declaraciones del CLIENTE</h2>
    <ul>
      <li>Declara <strong>ser propietario</strong> de las piezas de oro cuyos detalles constan en la Hoja Resumen; asume las
          responsabilidades civiles o penales por falsedad o inexactitud de esta declaración.</li>
      <li>Declara que <strong>los bienes otorgados son de oro</strong>; caso contrario, asumirá las responsabilidades civiles y penales
          que correspondan.</li>
      <li>Declara que la <strong>descripción de los bienes</strong> señalados en la Hoja Resumen es la correcta.</li>
      <li>Declara que ha <strong>leído y comprendido</strong> el contenido del presente contrato y que se le absolvieron las dudas previas
          a la firma, suscribiéndolo con conocimiento pleno de sus condiciones.</li>
    </ul>

    <!-- Cronograma resumido (opcional) -->
    @if(isset($cuotas) && $cuotas->count())
      <h2>ANEXO: Cronograma de Pagos (resumen)</h2>
      <table class="grid">
        <thead>
          <tr>
            <th class="center">#</th>
            <th class="center">Vencimiento</th>
            <th class="num">Amortización</th>
            <th class="num">Interés</th>
            <th class="num">Cuota</th>
          </tr>
        </thead>
        <tbody>
          @foreach($cuotas as $c)
            <tr>
              <td class="center">{{ $c->numero }}</td>
              <td class="center">{{ $c->fecha }}</td>
              <td class="num">{{ number_format((float)$c->amortizacion,2,'.',',') }}</td>
              <td class="num">{{ number_format((float)$c->interes,2,'.',',') }}</td>
              <td class="num">{{ number_format((float)$c->monto,2,'.',',') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif

    <p class="muted" style="margin-top:6mm;">
      En señal de conformidad, las partes suscriben en Tarapoto a los {{ $formattedDate }}.
    </p>

    <!-- Firmas -->
    <table class="signs">
      <tr>
        <td>
          <div class="line"></div>
          <div><strong>EL CLIENTE</strong></div>
          <div class="tiny">{{ $credito_cliente->clientes->nombre }} — DNI {{ $credito_cliente->clientes->documento_identidad }}</div>
        </td>
        <td>
          <div class="line"></div>
          <div><strong>POR LA ENTIDAD</strong></div>
          <div class="tiny">GRUPO CREDIPALMO S.A.C. — RUC 20610069542</div>
        </td>
      </tr>
    </table>

    <p class="tiny muted" style="margin-top:6mm;">
      Documento contractual. Este modelo es base operativa; su aplicación debe adecuarse a la normativa vigente y a las
      políticas internas de GRUPO CREDIPALMO S.A.C.
    </p>
  </div>
</body>
</html>
