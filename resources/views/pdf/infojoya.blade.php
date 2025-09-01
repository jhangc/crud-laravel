<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Información para el Cliente</title>
  <style>
   

    body { font-family: Arial, Helvetica, sans-serif; }

    .sheet{
      padding: 8mm 10mm;
      /* Altura útil: 297mm - (20+20) = 257mm.
         Usamos min-height para evitar “segunda hoja fantasma”. */
      min-height: 257mm;
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
      page-break-inside: avoid; /* no partir este bloque */
    }

    h1 { text-align:center; font-size:18px; margin:0 0 10px; letter-spacing:.2px; }
    h2 { font-size:13px; margin:12px 0 6px; }
    p, li { font-size:12px; text-align:justify; margin:0 0 8px; }

    .spacer { flex: 1 1 auto; } /* empuja la firma al pie */
    .firma { margin-top: 2mm; }
    .line{
      border-top: 1px solid #000;
      width: 30%;
      height: 0;
      margin-top: 5mm;
      margin-bottom: 2mm;
      margin-left: 0; /* alineada a la izquierda */
    }
    .small { font-size:11px; }
  </style>
</head>
<body>
  <div class="sheet">
    <h1>INFORMACIÓN PARA EL CLIENTE</h1>

    <h2>PRINCIPALES DERECHOS DEL CLIENTE</h2>
    <p>
      Al ser un crédito a una sola cuota, EL CLIENTE podrá realizar pago anticipado total, cuando se realice el
      pago anticipado del total de la obligación se procederá, con la reducción de los intereses, comisiones y
      gastos al día del pago, así mismo queda claramente establecido que no se cobra ninguna comisión por el
      ejercicio de este derecho y este se ejercerá de acuerdo a lo establecido en el reglamento.
    </p>

    <h2>OBLIGACIONES DEL CLIENTE</h2>
    <p>
      Los pagos deberán efectuarse en efectivo, en la misma moneda en que fue desembolsado el crédito, en
      cualquier momento posterior al incumplimiento CREDIPALMO podrá adjudicarse el lote de oro de
      acuerdo a la deuda total existente a dicha fecha. El mismo que es aceptado expresamente por EL CLIENTE;
      en caso el valor fuese menor o insuficiente, CREDIPALMO se reserva el derecho a cobrar la diferencia.
      EL CLIENTE conoce y acepta que en tanto no cancele dicha diferencia y/o demás obligaciones que hubiera
      pactado con CREDIPALMO será reportado negativamente o mantendrá dicha condición en las centrales
      de riesgo y/o ante las autoridades correspondientes.
    </p>
    <p>
      EL CLIENTE que haya cancelado su crédito, pero por cualquier motivo no haya retirado o recogido su lote de
      oro dentro de los 15 días posteriores a la fecha de cancelación del crédito, pagará adicionalmente una
      comisión por el servicio de custodia brindado por CREDIPALMO.
    </p>

    <h2>COBERTURA DE LA GARANTÍA</h2>
    <p>
      La garantía mobiliaria sobre las piezas de oro que se otorgan en respaldo de la obligación contraída mediante
      el presente documento, también respalda todas las deudas y/o obligaciones directas o indirectas (en calidad
      de avales, fiadores solidarios o garantes) existentes o futuras que se encuentran materializados en títulos
      valores y contratos de préstamos otorgados por GRUPO CREDIPALMO SAC.
    </p>

    <h2>SOBRE LA ADJUDICACIÓN DEL BIEN EN GARANTÍA</h2>
    <p>
      En cualquier momento posterior al incumplimiento, CREDIPALMO podrá adjudicarse el lote de oro de
      acuerdo a la deuda total existente a dicha fecha, el mismo que es aceptado expresamente por EL CLIENTE;
      en caso el valor fuese menor o insuficiente, CREDIPALMO se reserva el derecho de cobrar la diferencia.
    </p>

    <h2>MODIFICACIÓN DE INTERÉS, COMISIONES, GASTOS Y OTRAS CONDICIONES DEL CONTRATO</h2>
    <p>
      CREDIPALMO podrá modificar la tasa de interés. Asimismo, podrá modificar las comisiones, gastos y otras
      estipulaciones contractuales de acuerdo a la normativa vigente aplicable, cuando se presenten algunos de
      los supuestos indicados en el contrato de crédito con garantía de oro – CrediJoya.
    </p>

    <h2>DECLARACIÓN JURADA DEL CLIENTE</h2>
    <p>
      El CLIENTE declara que ha sido instruido de manera clara, explícita y comprensible sobre los alcances y
      finalidades de la HOJA RESUMEN y CONTRATO – CREDIJOYA, luego de los cuales se le ha otorgado copia
      de cada uno de ellos, suscribiendo en la parte final de este documento en conformidad sobre lo antes indicado.
    </p>

    <p class="small"><strong>Fecha de suscripción:</strong> ……………/………………/………………</p>

    <div class="spacer"></div>

    <div class="firma">
      <div class="line"></div>
      <div class="small"><strong>FIRMA DEL CLIENTE</strong></div>
      <p class="small" style="margin-top:6mm;">
        Ante el incumplimiento del pago según las condiciones pactadas, se procederá a realizar el reporte,
        con la calificación correspondiente, a la central de riesgos.
      </p>
    </div>
  </div>
</body>
</html>
