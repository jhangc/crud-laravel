<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrato Crédito Individual</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
            color: #222;
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
            font-size: 16px;
            margin: 10px 0 20px;
        }

        h2.clausula {
            font-size: 13px;
            font-weight: bold;
            margin: 16px 0 6px;
        }

        p {
            margin: 6px 0;
            font-size: 13px;
            text-align: justify;
        }

        .content {
            margin: 0px 20px;
        }

        .firmas {
            width: 100%;
            border-collapse: collapse;
            margin-top: 80px;
        }

        .firmas td {
            width: 50%;
            border: none;
            text-align: center;
            vertical-align: top;
            font-size: 13px;
            padding: 0 10px;
        }

        .firmas .linea {
            width: 60%;
            margin: 0 auto 4px;
            border-top: 1px solid #000;
        }

        .firmas .doc {
            font-size: 12px;
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

    @php($cliente = $credito_cliente->clientes)

    <div class="content">
        <h1>CONTRATO PRIVADO DE MUTUO DINERARIO</h1>

        <p>Conste por el presente documento el contrato de mutuo dinerario que celebran de una parte el Sr.(A)
            <b>{{ $cliente->nombre }}</b>, peruano identificado con DNI. N° <b>{{ $cliente->documento_identidad }}</b>
            con domicilio para efectos del presente contrato en {{ $cliente->direccion }}, a quien en adelante se le
            denominará <b>EL MUTUANTE</b> y de la otra parte la empresa <b>GRUPO CREDIPALMO S.A.C.</b> con R.U.C. N°
            20610069542 con domicilio Fiscal en MZ I5 LOTE 9 URB. MONTERRICO Distrito de Chiclayo Provincia de
            Chiclayo Departamento de Lambayeque y debidamente representado por su Gerente General, cuyos poderes se
            encuentran inscritos en la Partida Electrónica N° 11412179 del Registro de Personas Jurídicas de Chiclayo a
            quien en adelante se le denominará <b>LA MUTUARIA</b>.</p>

        <h2 class="clausula">CLÁUSULA PRIMERA: DE LAS REFERENCIAS</h2>
        <p>LA MUTUARIA es una empresa jurídica legalmente constituida dedicada a ofrecer alternativas de préstamos
            diferentes a la banca tradicional. Por su parte se encuentra en capacidad de facilitar dicho Capital para
            que EL MUTUANTE pueda desarrollar sus actividades empresariales.</p>

        <h2 class="clausula">CLÁUSULA SEGUNDA: DEL OBJETO DEL MUTUO</h2>
        <p>En virtud del presente contrato LA MUTUARIA se compromete a dar en calidad de préstamo a favor de EL
            MUTUANTE hasta la suma de S/ {{ number_format((float) $prestamo->monto_total, 2) }}
            ({{ ucfirst(mb_strtolower($montoEnLetras)) }}) los mismos que serán entregados luego de la suscripción del
            presente contrato. EL MUTUANTE se compromete a pagar la deuda capital más los intereses y en el plazo
            máximo de {{ $plazoTexto }}.</p>

        <h2 class="clausula">CLÁUSULA TERCERA: GARANTÍA DE CUMPLIMIENTO</h2>
        <p>EL MUTUANTE constituirá un Fondo de Garantía equivalente al diez por ciento (10%) del monto total
            desembolsado, ascendente a S/ {{ number_format((float) $prestamo->monto_total * 0.10, 2) }}.</p>
        <p>La garantía pasará a devolver A EL MUTUANTE siempre y cuando pague de manera puntual el total del
            crédito, caso contrario EL MUTUANTE caiga en atrasos y no realicen el pago del total del crédito.</p>
        <p>La garantía podrá aplicarse a cuotas vencidas, intereses y gastos de cobranza pendientes.</p>

        <h2 class="clausula">CLÁUSULA CUARTA: DE LAS OBLIGACIONES DE LAS PARTES</h2>
        <p>EL MUTUANTE entregará por partes la suma de dinero objeto del presente Mutuo luego de la suscripción del
            presente contrato declarando que estas operaciones se realizarán a través de las entidades del Sistema
            Financiero local y en la Cta. Bancaria de LA MUTUATARIA. Por este préstamo LA MUTUATARIA reconocerá a EL
            MUTUANTE intereses compensatorios a una tasa efectiva anual del {{ rtrim(rtrim(number_format((float) $prestamo->tasa, 2), '0'), '.') }}%.</p>

        <h2 class="clausula">CLÁUSULA QUINTA: DE LA FORMA Y OPORTUNIDAD DEL PAGO</h2>
        <p>En virtud del presente contrato EL MUTUANTE devolverá la suma de dinero objeto del mutuo tal como lo señala
            la tercera cláusula en concordancia a lo establecido en la cuarta cláusula en la misma moneda y más los
            intereses debiéndose efectuar el pago mediante medios de sistema financiero. Las partes dejan constancia que
            el pago del Capital más los intereses señalados en la cuarta cláusula se realizará en el domicilio de LA
            MUTUARIA.</p>

        <h2 class="clausula">CLÁUSULA SEXTA: DEL PLAZO</h2>
        <p>El plazo máximo en el que se comenzará a cancelar la deuda por parte de EL MUTUANTE es de
            {{ $plazoTexto }} en la forma y lugar señalado en las cláusulas anteriores computado a partir de la
            suscripción de éste documento.</p>

        <h2 class="clausula">CLÁUSULA SÉPTIMA: DE LA RESOLUCIÓN DEL CONTRATO</h2>
        <p>Las partes acuerdan que el presente contrato podrá ser resuelto de pleno derecho ante el incumplimiento de
            las obligaciones asumidas por las partes intervinientes en el presente documento.</p>

        <h2 class="clausula">CLÁUSULA OCTAVA: DE LA SOLUCIÓN DE CONTROVERSIAS</h2>
        <p>Todo litigio o controversia derivados o relacionados con este acto jurídico será resuelto directamente por
            las partes para cuyo efecto éstas se comprometen a realizar sus mayores esfuerzos para la solución armónica
            de sus controversias con base en las reglas de la buena fe y atendiendo a la común intención expresada en
            el presente contrato en un plazo que no exceda los quince (15) días hábiles. En caso las diferencias
            subsistan, la controversia será sometida a la decisión inapelable de un árbitro designado de común acuerdo
            por las partes. Si no existiera acuerdo sobre la designación del árbitro, el nombramiento correspondiente
            se hará a petición de cualquiera de las partes por el Colegio de Abogados de Lambayeque cuyas normas,
            administración y decisión se someten las partes en forma incondicional declarando conocerlas y aceptarlas
            en su integridad.</p>
        <p>El arbitraje se llevará a cabo por el Tribunal de Arbitraje del Colegio de Abogados de Lambayeque y no podrá
            exceder de sesenta (60) días desde el nombramiento del árbitro pudiendo éste prorrogar dicho plazo por
            causas justificadas. El arbitraje será de derecho.</p>

        <h2 class="clausula">CLÁUSULA NOVENA: DE LA COMUNICACIÓN</h2>
        <p>Las partes señalan que para la validez de todas las comunicaciones y notificaciones con motivo de la
            celebración o ejecución del presente contrato, los domicilios corresponderán a los señalados en los
            antecedentes del presente contrato. El cambio de domicilio de cualquiera de las partes surtirá efecto desde
            la fecha de comunicación a la otra parte por cualquier medio escrito.</p>

        <h2 class="clausula">CLÁUSULA DÉCIMA: DE APLICACIÓN SUPLETORIA</h2>
        <p>Las partes señalan que en todo aquello que no fue previsto en el presente contrato se aplicará
            supletoriamente lo dispuesto en el Código Civil en lo referente a la figura del mutuo artículos 1648º al
            1665º y demás del sistema jurídico que resulten aplicables.</p>

        <h2 class="clausula">CLÁUSULA DÉCIMO PRIMERA: DE LA INTERPRETACIÓN DE LA CLÁUSULA DÉCIMA</h2>
        <p>Las Cláusulas que formen parte del presente contrato serán interpretadas por las partes teniendo en cuenta
            los principios de buena fe comercial, los principios generales de derecho y en forma que no contravenga la
            Constitución Política del país.</p>

        <h2 class="clausula">CLÁUSULA DÉCIMA SEGUNDA: DE LA PENALIDAD</h2>
        <p>Las partes están de acuerdo en que ante el retraso en el cumplimiento del pago señalado en la cláusula
            cuarta se constituirá la mora automática sin necesidad que se tenga que cursar carta notarial para hacer de
            su conocimiento que se están generando dichos intereses.</p>

        <div class="avoid-page-break">
            <p>Agregue usted señor notario las Cláusulas de Ley y en señal de conformidad las partes suscriben este
                documento en la ciudad de Chiclayo a los {{ trim($formattedDate) }}.</p>

            <table class="firmas">
                <tr>
                    <td>
                        <div class="linea"></div>
                        EL MUTUANTE<br>
                        <span class="doc">DNI: {{ $cliente->documento_identidad }}</span>
                    </td>
                    <td>
                        <div class="linea"></div>
                        LA MUTUARIA<br>
                        <span class="doc">RUC: 20610069542</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
