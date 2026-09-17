<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrato Crédito Grupal</title>

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
            font-style: italic
        }

        h1 {
            text-align: center;
            color: black;
            font-size: 16px;
            text-decoration: underline;
            margin: 10px 0 20px;
        }

        h2.clausula {
            font-size: 13px;
            font-weight: bold;
            text-decoration: underline;
            margin: 18px 0 6px;
        }

        p {
            margin: 6px 0;
            font-size: 13px;
            text-align: justify
        }

        ul.lista {
            list-style: none;
            margin: 4px 0 8px 0;
            padding-left: 10px;
            font-size: 13px;
        }

        ul.lista li {
            margin: 2px 0;
        }

        .tabla td {
            font-size: 13px;
            padding: 4px 6px;
        }

        .content {
            margin: 0px 20px;
        }

        .firmas {
            width: 100%;
            border-collapse: collapse;
            margin-top: 70px;
        }

        .firmas td {
            border: none;
            text-align: center;
            vertical-align: top;
            font-size: 13px;
            padding: 0 10px;
        }

        .firmas .linea {
            width: 75%;
            margin: 0 auto 4px;
            border-top: 1px solid #000;
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
        <h1>CONTRATO DE CRÉDITO GRUPAL/SOLIDARIO</h1>

        <p>Conste por el presente documento el Contrato de Crédito Grupal Solidario que celebran, de una parte,
            <b>GRUPO CREDIPALMO S.A.C.</b>, identificada con RUC N.° 20610069542, con domicilio fiscal en MZ I5 LOTE 9
            URB. MONTERRICO Distrito de Chiclayo Provincia de Chiclayo Departamento de Lambayeque, debidamente
            representado por su Gerente General cuyos poderes se encuentran inscritos en la Partida Electrónica
            N° 11412179 del Registro de Personas Jurídicas de Chiclayo, a quien en adelante se denominará
            <b>"LA EMPRESA"</b>; y de la otra parte, las integrantes del grupo denominado
            <b>{{ $prestamo->nombre_prestamo ?: '……………………………….' }}</b>, identificados en el Anexo N.° 1 del presente
            contrato, a quienes en adelante se denominará conjuntamente <b>"EL GRUPO"</b>.</p>

        <h2 class="clausula">CLÁUSULA PRIMERA: OBJETO</h2>
        <p>LA EMPRESA otorga a EL GRUPO un crédito grupal solidario por el monto, plazo, frecuencia de pago, tasa de
            interés y demás condiciones detalladas en la Hoja Resumen y Cronograma de Pagos, los cuales forman parte
            integrante del presente contrato.</p>

        <h2 class="clausula">CLÁUSULA SEGUNDA: RESPONSABILIDAD SOLIDARIA</h2>
        <p>Todos los integrantes de EL GRUPO se obligan solidariamente frente a LA EMPRESA al cumplimiento de todas las
            obligaciones derivadas del presente contrato.</p>
        <p>La falta de pago de uno o más integrantes no libera a los demás miembros de su obligación de responder por la
            totalidad de la deuda.</p>

        <h2 class="clausula">CLÁUSULA TERCERA: COMITÉ DE REPRESENTACIÓN</h2>
        <p>EL GRUPO designará un Comité integrado, como mínimo, por una Presidenta y una Tesorera.</p>
        <p>El Comité tendrá las siguientes facultades:</p>
        <ul class="lista">
            <li>a) Coordinar el desembolso del crédito.</li>
            <li>b) Recibir comunicaciones de LA EMPRESA.</li>
            <li>c) Solicitar información relacionada con el crédito.</li>
            <li>d) Gestionar solicitudes de reprogramación o refinanciamiento.</li>
            <li>e) Coordinar la entrega de documentación requerida.</li>
        </ul>
        <p>Las actuaciones del Comité no liberan de responsabilidad a los demás integrantes del grupo.</p>

        <h2 class="clausula">CLÁUSULA CUARTA: DESEMBOLSO</h2>
        <p>El desembolso podrá efectuarse mediante efectivo, transferencia bancaria, depósito en cuenta, billetera
            electrónica u otro medio acordado entre las partes.</p>
        <p>La constancia de desembolso emitida por LA EMPRESA constituirá prueba suficiente de la entrega del crédito.</p>

        <h2 class="clausula">CLÁUSULA QUINTA: GARANTÍA DE CUMPLIMIENTO</h2>
        <p>EL GRUPO constituirá un Fondo de Garantía equivalente al diez por ciento (10%) del monto total desembolsado.</p>
        <p>La garantía pasará a devolver al grupo siempre y cuando el grupo pague de manera puntual el total del
            crédito, caso contrario el grupo caiga en atrasos y no realicen el pago del total del crédito.</p>
        <p>La garantía podrá aplicarse a cuotas vencidas, intereses y gastos de cobranza pendientes.</p>

        <h2 class="clausula">CLÁUSULA SEXTA: CRONOGRAMA DE PAGOS</h2>
        <p>EL GRUPO se obliga a cumplir el Cronograma de Pagos entregado por LA EMPRESA.</p>
        <p>Todo pago efectuado será imputado en el siguiente orden:</p>
        <ul class="lista">
            <li>1. Gastos de cobranza.</li>
            <li>2. Intereses vencidos.</li>
            <li>3. Capital.</li>
        </ul>

        <h2 class="clausula">CLÁUSULA SÉTIMA: PAGO ANTICIPADO</h2>
        <p>EL GRUPO podrá realizar pagos anticipados sin penalidad.</p>
        <p>El pago anticipado podrá destinarse a:</p>
        <ul class="lista">
            <li>a) Reducir el número de cuotas.</li>
            <li>b) Reducir el importe de las cuotas.</li>
        </ul>
        <p>La elección deberá constar por escrito o mediante medio electrónico verificable.</p>

        <h2 class="clausula">CLÁUSULA OCTAVA: REPROGRAMACIÓN Y REFINANCIAMIENTO</h2>
        <p>Toda reprogramación o refinanciamiento requerirá la aceptación expresa de EL GRUPO. Dicha aceptación podrá
            acreditarse mediante:</p>
        <ul class="lista">
            <li>a) Firma manuscrita.</li>
            <li>b) Firma electrónica.</li>
            <li>c) Grabación telefónica.</li>
            <li>d) Correo electrónico.</li>
            <li>e) Mensaje de WhatsApp u otro medio electrónico que permita identificar al cliente.</li>
        </ul>

        <h2 class="clausula">CLÁUSULA NOVENA: INTERESES Y CARGOS</h2>
        <p>Las tasas de interés, gastos y demás cargos aplicables serán informadas previamente en la Hoja Resumen.</p>
        <p>LA EMPRESA no podrá exigir conceptos que no hayan sido previamente informados y aceptados por EL GRUPO.</p>

        <h2 class="clausula">CLÁUSULA DÉCIMA: MEDIOS DE COMUNICACIÓN</h2>
        <p>EL GRUPO autoriza a LA EMPRESA a utilizar correo electrónico, llamadas telefónicas, mensajes SMS, WhatsApp u
            otros medios electrónicos para comunicaciones relacionadas con el crédito.</p>

        <h2 class="clausula">CLÁUSULA DÉCIMA PRIMERA: GRABACIÓN DE LLAMADAS</h2>
        <p>EL GRUPO autoriza expresamente la grabación de llamadas telefónicas realizadas con LA EMPRESA.</p>
        <p>Las grabaciones podrán utilizarse como medio de prueba para acreditar solicitudes, conformidades,
            autorizaciones, reprogramaciones, refinanciamientos y demás actos relacionados con el crédito.</p>

        <h2 class="clausula">CLÁUSULA DÉCIMA SEGUNDA: GESTIÓN DE COBRANZA</h2>
        <p>En caso de incumplimiento, LA EMPRESA podrá realizar acciones de cobranza mediante llamadas, mensajes, correos
            electrónicos, cartas notariales o visitas domiciliarias, respetando la legislación vigente y la dignidad del
            cliente.</p>

        <h2 class="clausula">CLÁUSULA DÉCIMA TERCERA: INCUMPLIMIENTO</h2>
        <p>Constituyen causales de incumplimiento:</p>
        <ul class="lista">
            <li>a) Mora en el pago de una o más cuotas.</li>
            <li>b) Proporcionar información falsa.</li>
            <li>c) Ocultar información relevante para la evaluación del crédito.</li>
            <li>d) Incumplir obligaciones esenciales del presente contrato.</li>
        </ul>

        <h2 class="clausula">CLÁUSULA DÉCIMA CUARTA: VENCIMIENTO ANTICIPADO</h2>
        <p>LA EMPRESA podrá declarar el vencimiento anticipado de la deuda cuando exista incumplimiento grave de las
            obligaciones asumidas por EL GRUPO.</p>
        <p>En tal caso, podrá exigir el pago inmediato del saldo pendiente.</p>

        <h2 class="clausula">CLÁUSULA DÉCIMA QUINTA: PAGARÉ</h2>
        <p>Cuando corresponda, EL GRUPO podrá suscribir un pagaré en garantía de las obligaciones derivadas del presente
            contrato, de conformidad con la legislación vigente.</p>

        <h2 class="clausula">CLÁUSULA DÉCIMA SEXTA: PROTECCIÓN DE DATOS PERSONALES</h2>
        <p>EL GRUPO autoriza el tratamiento de sus datos personales para fines de evaluación crediticia, administración
            del crédito, cobranza y cumplimiento de obligaciones legales.</p>

        <h2 class="clausula">CLÁUSULA DÉCIMA SÉTIMA: RECLAMOS</h2>
        <p>Los reclamos podrán presentarse a través de los canales habilitados por LA EMPRESA, los cuales deberán
            encontrarse informados al público.</p>

        <h2 class="clausula">CLÁUSULA DÉCIMA OCTAVA: RENUNCIA DE DOMICILIO PARA SOLUCIÓN DE CONTROVERSIAS</h2>
        <p>LA EMPRESA podrá entablar acción judicial sobre las controversias derivadas del presente contrato donde lo
            tuviera por conveniente, a cuyo efecto EL GRUPO renuncia al fuero de su propio domicilio y a cuantos puedan
            favorecerlos en el proceso judicial o fuera de él.</p>

        <h2 class="clausula">CLÁUSULA DÉCIMA NOVENA: DECLARACIÓN DE DOMICILIO</h2>
        <p>Las partes señalan como domicilios los consignados en el presente contrato y en la Hoja Resumen.</p>

        <h2 class="clausula">CLÁUSULA VIGÉSIMA: ACEPTACIÓN</h2>
        <p>Las partes declaran haber leído, comprendido y aceptado todas las condiciones establecidas en el presente
            contrato.</p>

        <div class="avoid-page-break">
            <p style="margin-top: 25px;">Lugar y fecha: ______________________________</p>

            <table class="firmas">
                <tr>
                    <td style="width: 50%;">
                        <div class="linea"></div>
                        PRESIDENTA DEL GRUPO
                    </td>
                    <td style="width: 50%;">
                        <div class="linea"></div>
                        TESORERA DEL GRUPO
                    </td>
                </tr>
            </table>

            <table class="firmas">
                <tr>
                    <td>
                        <div class="linea" style="width: 40%;"></div>
                        LA EMPRESA
                    </td>
                </tr>
            </table>
        </div>

        <div class="page-break"></div>

        <h1>ANEXO N.° 1 – INTEGRANTES DE EL GRUPO</h1>
        <p><b>Grupo:</b> {{ $prestamo->nombre_prestamo }}</p>
        <br>
        @foreach ($prestamo->clientes as $cliente)
        <table style="border-collapse: collapse; width: 100%;" border="1" class="tabla avoid-page-break">
            <tr>
                <td colspan="2" style="text-align: center;"><b>Datos</b></td>
                <td style="text-align: center; width:30%;"><b>Firma</b></td>
            </tr>
            <tr>
                <td style="width:20%;"><b>Nombre:</b></td>
                <td style="width:50%;">{{ $cliente->nombre }}</td>
                <td rowspan="4"></td>
            </tr>
            <tr>
                <td><b>DNI:</b></td>
                <td>{{ $cliente->documento_identidad }}</td>
            </tr>
            <tr>
                <td><b>Dirección:</b></td>
                <td>{{ $cliente->direccion }}</td>
            </tr>
            <tr>
                <td><b>Celular:</b></td>
                <td>{{ $cliente->telefono }}</td>
            </tr>
        </table>
        <br><br>
        @endforeach

        <div class="page-break"></div>

        <h1>HOJA RESUMEN</h1>

        <p>El presente documento es parte integrante del Contrato de Crédito Grupal Solidario celebrado con GRUPO
            CREDIPALMO S.A.C. y en él constan todas las condiciones del crédito otorgado a EL GRUPO.</p>

        <div class="datos-credito">
            <h2 class="clausula">Datos del Crédito</h2>
            <table style="width: 100%; border-collapse: collapse; font-size:12px;">
                <tr>
                    <td style="border: 1px solid #000; padding: 8px; ">1. Nombre del Grupo</td>
                    <td style="border: 1px solid #000; padding: 8px; ">{{ $prestamo->nombre_prestamo }} </td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 8px; ">2. Código Crédito</td>
                    <td style="border: 1px solid #000; padding: 8px; ">{{$correlativosGenerales->correlativo}}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 8px; ">3. Monto del Crédito Prestado a EL GRUPO</td>
                    <td style="border: 1px solid #000; padding: 8px; ">S/. {{ $prestamo->monto_total }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 8px; ">4. Fecha de Desembolso</td>
                    <td style="border: 1px solid #000; padding: 8px; ">{{ Carbon\Carbon::parse($prestamo->fecha_desembolso)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 8px; ">5. Frecuencia de Pago</td>
                    <td style="border: 1px solid #000; padding: 8px; ">{{ $prestamo->recurrencia }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 8px; ">6. Plazo del Préstamo Desembolsado</td>
                    <td style="border: 1px solid #000; padding: 8px; ">{{ $prestamo->tiempo }} cuotas</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 8px; ">7. Tasa de Interés Compensatorio Fija Efectiva Anual (TEA 360 días)</td>
                    <td style="border: 1px solid #000; padding: 8px; ">{{ $prestamo->tasa }}%</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 8px; ">8. Tasa de Costo Efectiva Anual</td>
                    <td style="border: 1px solid #000; padding: 8px; ">{{ $prestamo->tasa }}%</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 8px; ">9. Tasa de Interés Moratorio Nominal Anual (*)</td>
                    <td style="border: 1px solid #000; padding: 8px; ">0.3% diario</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 8px; ">10. Fondo de Garantía (10% del monto desembolsado)</td>
                    <td style="border: 1px solid #000; padding: 8px; ">S/. {{ number_format((float) $prestamo->monto_total * 0.10, 2) }}</td>
                </tr>
            </table>
        </div>

        <p>En los casos en que el Comité solicite ampliación del monto desembolsado, y esta suponga la incorporación de nuevas integrantes en el grupo, dicha ampliación no afectará las tasas de costo efectiva anual, ni las condiciones pactadas anteriormente de las demás integrantes del grupo.</p>

        <p>(*) Tasa de interés moratorio Nominal Anual se cobrará a EL GRUPO en caso de incumplimiento de pago, se devengará de manera adicional a la tasa de interés compensatorio desde el día siguiente a la fecha de vencimiento de la obligación hasta el día de su pago total.</p>

        <p>Si EL GRUPO incumpliera el pago según las condiciones pactadas, se procederá a realizar el reporte correspondiente a la Central de Riesgos con la calificación que corresponda, de conformidad con el Reglamento para la Evaluación y Clasificación del Deudor y la Exigencia de Provisiones vigente.</p>
    </div>
</body>

</html>
