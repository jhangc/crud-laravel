<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrato Crédito Individual</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        .header {
            position: fixed;
            top: -70px;
            left: -45px;
        }

        .header h3 {
            padding: 5px;
            background-color: #044B6B;
            color: white;
            font-style: italic;
        }

        h1 {
            text-align: center;
            color: black;
            font-size: 18px;
        }

        p {
            margin: 10px 0;
            font-size: 14px;
            text-align: justify;
        }

        .tabla td {
            font-size: 14px;
        }

        .signature {
            margin-top: 20px;
        }

        .signature div {
            display: inline-block;
            width: 45%;
            text-align: center;
        }

        .content {
            margin: 0px 20px;
        }

        .page-break {
            page-break-before: always;
        }

        .avoid-page-break {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    <header class="header">
        <h3>Grupo Credipalmo</h3>
    </header>

    <div class="content">
        <h1>CONTRATO PRIVADO DE MUTUO DINERARIO</h1>
        <p>Conste por el presente documento el contrato de mutuo dinerario que celebran de una parte el Sr.
            {{ $credito_cliente->clientes->nombre }} peruano soltero identificado con DNI. N° {{ $credito_cliente->clientes->documento_identidad }} con domicilio para efectos del
            presente contrato en {{ $credito_cliente->clientes->direccion }} 
            {{-- distrito _________ provincia y departamento de____________  --}}
            a quien en
            adelante se le denominará EL MUTUANTE y de la otra parte la empresa GRUPO CREDIPALMO S.A.C. con R.U.C. N°
            20610069542 con domicilio en Jr. Juan Vargas N° 248 distrito de Tarapoto provincia y departamento de San
            Martin y debidamente representada por su gerente general la Sra. Bethzi López Díaz identificada con DNI N°
            44953385 cuyos poderes se encuentran inscritos en la Partida Electrónica N° 11412179 del Registro de
            Personas Jurídicas de Chiclayo a quien en adelante se le denominará LA MUTUARIA.</p>

        <p><b>CLÁUSULA PRIMERA: DE LAS REFERENCIAS</b></p>
        <p>LA MUTUATARIA es una empresa jurídica legalmente constituida dedicada a ofrecer alternativas de préstamos
            diferentes a la banca tradicional. Por su parte EL MUTUANTE se encuentra en capacidad de facilitar dicho
            Capital para que LA MUTUATARIA pueda desarrollar sus actividades empresariales.</p>

        <p><b>CLÁUSULA SEGUNDA: DEL OBJETO DEL MUTUO</b></p>
        <p>En virtud del presente contrato EL MUTUANTE se compromete a dar en calidad de préstamo a favor de LA
            MUTUATARIA hasta la suma de S/ {{ $prestamo->monto_total }} (…………con 00/100 soles) los mismos que serán entregados luego de la
            suscripción del presente contrato.</p>

        <p><b>CLÁUSULA TERCERA: DE LAS CARACTERISTICAS DEL MUTUO</b></p>
        <p>LA MUTUATARIA se compromete a pagar la deuda capital más los intereses y en el plazo máximo de {{ $prestamo->tiempo }}
            meses.</p>

        <p><b>CLÁUSULA CUARTA: DE LAS OBLIGACIONES DE LAS PARTES</b></p>
        <p>EL MUTUANTE entregará por partes la suma de dinero objeto del presente Mutuo luego de la suscripción del
            presente contrato declarando que estas operaciones se realizarán a través de las entidades del Sistema
            Financiero local y en la Cta. Bancaria de LA MUTUATARIA. Por este préstamo LA MUTUATARIA reconocerá a EL
            MUTUANTE intereses compensatorios a una tasa efectiva anual del {{ intval($prestamo->tasa) }}%.</p>

        <p><b>CLÁUSULA QUINTA: DE LA FORMA Y OPORTUNIDAD DEL PAGO</b></p>
        <p>En virtud del presente contrato LA MUTUATARIA devolverá la suma de dinero objeto del mutuo tal como lo señala
            la tercera cláusula en concordancia a lo establecido en la cuarta cláusula en la misma moneda y más los
            intereses debiéndose efectuar el pago mediante medios de sistema financiero. Las partes dejan constancia que
            el pago del Capital más los intereses señalados en la cuarta cláusula se realizará en el domicilio de EL
            MUTUANTE.</p>

        <p><b>CLÁUSULA SEXTA: DEL PLAZO</b></p>
        <p>El plazo máximo en el que se comenzará a cancelar la deuda por parte de LA MUTUATARIA es de {{ $prestamo->tiempo }} meses
            en la forma y lugar señalado en las cláusulas anteriores computado a partir de la suscripción de éste
            documento.</p>

        <p><b>CLÁUSULA SÉPTIMA: DE LA RESOLUCIÓN DEL CONTRATO</b></p>
        <p>Las partes acuerdan que el presente contrato podrá ser resuelto de pleno derecho ante el incumplimiento de
            las obligaciones asumidas por las partes intervinientes en el presente documento.</p>

        <p><b>CLÁUSULA OCTAVA: DE LA SOLUCIÓN DE CONTROVERSIAS</b></p>
        <p>Todo litigio o controversia derivados o relacionados con este acto jurídico será resuelto directamente por
            las partes para cuyo efecto éstas se comprometen a realizar sus mayores esfuerzos para la solución armónica
            de sus controversias con base en las reglas de la buena fe y atendiendo a la común intención expresada en el
            presente contrato en un plazo que no exceda los quince (15) días hábiles. En caso las diferencias subsistan,
            la controversia será sometida a la decisión inapelable de un árbitro designado de común acuerdo por las
            partes. Si no existiera acuerdo sobre la designación del árbitro, el nombramiento correspondiente se hará a
            petición de cualquiera de las partes por el Colegio de Abogados de Lambayeque cuyas normas administración y
            decisión se someten las partes en forma incondicional declarando conocerlas y aceptarlas en su integridad.
        </p>
        <p>El arbitraje se llevará a cabo por el Tribunal de Arbitraje del Colegio de Abogados de Lambayeque y no podrá
            exceder de sesenta (60) días desde el nombramiento del árbitro pudiendo éste prorrogar dicho plazo por
            causas justificadas. El arbitraje será de derecho.</p>

        <p><b>CLÁUSULA NOVENA: DE LA COMUNICACIÓN</b></p>
        <p>Las partes señalan que para la validez de todas las comunicaciones y notificaciones con motivo de la
            celebración o ejecución del presente contrato, los domicilios corresponderán a los señalados en los
            antecedentes del presente contrato. El cambio de domicilio de cualquiera de las partes surtirá efecto desde
            la fecha de comunicación a la otra parte por cualquier medio escrito.</p>

        <p><b>CLÁUSULA DÉCIMA: DE APLICACIÓN SUPLETORIA</b></p>
        <p>Las partes señalan que en todo aquello que no fue previsto en el presente contrato se aplicará
            supletoriamente lo dispuesto en el Código Civil en lo referente a la figura del mutuo artículos 1648º al
            1665º y demás del sistema jurídico que resulten aplicables.</p>

        <p><b>CLÁUSULA DECIMO PRIMERA: DE LA INTERPRETACIÓN DE LA CLAUSULA DÉCIMA</b></p>
        <p>Las Cláusulas que formen parte del presente contrato serán interpretadas por las partes teniendo en cuenta
            los principios de buena fe comercial, los principios generales de derecho y en forma que no contravenga la
            Constitución Política del país.</p>

        <p><b>CLÁUSULA DÉCIMA SEGUNDA: DE LA PENALIDAD</b></p>
        <p>Las partes están de acuerdo en que ante el retraso en el cumplimiento del pago señalado en la cláusula cuarta
            se constituirá la mora automática sin necesidad que se tenga que cursar carta notarial para hacer de su
            conocimiento que se están generando dichos intereses.</p>

            <p>Agregue usted señor notario las Cláusulas de Ley y en señal de conformidad las partes suscriben este
                documento en la ciudad de Tarapoto a los {{ $formattedDate }}.</p>
            

        <br><br>
        <table style="border-collapse: collapse; width: 100%;">
            <tr>
                <td style="border: none; text-align: center;">
                    <div style="width: 50%; margin: 0 auto;">
                        <hr>
                    </div>
                    EL MUTUANTE<br>
                    DNI N°: {{ $credito_cliente->clientes->documento_identidad }}
                </td>
                <td style="border: none; text-align: center;">
                    <div style="width: 50%; margin: 0 auto;">
                        <hr>
                    </div>
                    LA MUTUATARIA<br>
                    DNI N°: 44953385
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
