<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagaré</title>
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

        .content {
            margin: 0px 20px;
        }

        h1 {
            text-align: center;
            color: black;
            font-size: 16px;
            margin: 10px 0 15px;
        }

        h2.seccion {
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0 10px;
        }

        h2.centro {
            text-align: center;
        }

        p {
            margin: 6px 0;
            font-size: 13px;
            text-align: justify;
        }

        .datos p {
            margin: 0;
        }

        .tabla {
            width: 100%;
            border-collapse: collapse;
        }

        .tabla th,
        .tabla td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }

        .tabla th {
            font-weight: bold;
        }

        .firmas td {
            height: 90px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <header class="header">
        <h3>Grupo Credipalmo</h3>
    </header>

    @php($cliente = $credito_cliente->clientes)

    <div class="content">
        <h1>PAGARÉ</h1>

        <table class="tabla">
            <tr>
                <th>NÚMERO PAGARE</th>
                <th>LUGAR DE EMISION</th>
                <th>FECHA DE EMISION</th>
                <th>FECHA DE VENCIMIENTO</th>
                <th style="text-align: center;">MONEDA</th>
                <th>MONTO</th>
            </tr>
            <tr>
                <td><b>{{ $correlativo->correlativo }}</b></td>
                <td>Chiclayo</td>
                <td>{{ \Carbon\Carbon::parse($prestamo->fecha_desembolso)->format('d/m/Y') }}</td>
                <td>{{ $prestamo->fecha_fin ? \Carbon\Carbon::parse($prestamo->fecha_fin)->format('d/m/Y') : '' }}</td>
                <td style="text-align: center;">SOLES</td>
                <td>S/ {{ number_format((float) $prestamo->monto_total, 2) }}</td>
            </tr>
        </table>

        <p style="margin-top: 12px;">Por este título PAGARE / pagaremos solidariamente a la empresa Grupo Credipalmo
            S.A.C. su orden o quien este hubiera endosado el presente título en su oficina de esta ciudad o donde se
            presente este título para su cobro la suma de S/ {{ number_format((float) $prestamo->monto_total, 2) }}
            ({{ $montoEnLetras }})</p>

        <p>La suma materia de obligación contenida en el presente título será cancelada en la misma moneda antes
            expresada, según el cronograma de pago de las cuotas que se incluye en este Pagaré. Así también, nos
            obligamos a las clausulas especiales establecidas en el presente contrato.</p>

        <h2 class="seccion">DATOS DEL CLIENTE/DEUDOR(ES):</h2>
        <div class="datos">
            <p><b>NOMBRE(S)/RAZON SOCIAL:</b> {{ $cliente->nombre }}</p>
            <p><b>DNI/RUC N°:</b> {{ $cliente->documento_identidad }}</p>
            <p><b>DOMICILIO:</b> {{ $cliente->direccion }}</p>
        </div>

        <h2 class="seccion">DATOS DEL AVAL (ES)</h2>
        <div class="datos">
            <p><b>NOMBRE(S)/RAZON SOCIAL:</b> {{ $cliente->aval }}</p>
            <p><b>DNI/RUC:</b> {{ $cliente->numero_dni_aval }}</p>
        </div>

        <h2 class="seccion centro">CLAUSULAS ESPECIALES QUE RIGEN EL PRESENTE TITULO VALOR:</h2>

        <p><b>PRIMERO:</b> El vencimiento del pagaré podrá ser renovado y/o prorrogado por su tenedor al cumplimiento
            del plazo establecido en el cronograma por el plazo que éste señale mediante anotación al reverso del título
            por su importe total o por menor cantidad sin que sea necesaria intervención del emitente ni del(los)
            obligados(s) solidario(s). El tenedor queda obligado a informar de las prórrogas que conceda a simple
            requerimiento de cualquier obligado de este pagaré.</p>

        <p><b>SEGUNDO:</b> Este pagaré debe ser pagado solo en la misma moneda que expresa este título valor.</p>

        <p><b>TERCERO:</b> El importe de este pagaré y/o de las cuotas del crédito que representa generarán desde la
            fecha de emisión una tasa de interés compensatorio efectiva anual de {{ intval($prestamo->tasa) }}%.(360
            días calendarios) y en caso de incumplimiento de pago generará un interés moratorio del
            {{ intval($prestamo->tasa) }}% tasa efectiva anual de (360 días calendarios) la misma que se aplicará en su
            equivalente de factor diario de {{ $tasadiaria }}% sobre el saldo capital de cuota(s) sin perjuicio de los
            gastos y comisiones por los conceptos convenidos pactados e indicados en cronograma de pago que es anexo al
            presente pagaré que se hubiera generado o pudieran generarse de acuerdo al tarifario vigente de la empresa
            Grupo Credipalmo S.A.C. desde la fecha de emisión hasta la cancelación total de la presente obligación sin
            que sea necesario requerimiento alguno para constituirme / constituirnos en mora pues queda entendido que
            esta se producirá de modo automático por el solo hecho del vencimiento de este pagaré.</p>

        <p><b>CUARTA:</b> El importe de este pagaré y/o de sus intereses y comisiones que se indican será cancelado en
            cuotas conforme al cronograma de pagos que se inserta. La falta de pago de una o más cuotas en las fechas
            indicadas facultan a su tenedor a proceder a la ejecución del presente título procediendo su ejecución por
            el solo mérito de haber vencido su plazo y no haber sido prorrogado.</p>

        <p><b>QUINTA:</b> El presente pagaré no está sujeto a protesto por falta de pago, salvo que el tenedor opte por
            su protesto, en cuyo caso, los gastos respectivos serán de su cuenta, conforme lo dispuesto en el artículo
            81.2 de la ley N° 27287.</p>

        <p><b>SEXTA:</b> El importe de este pagaré podrá ser pactado en una o más cuotas según el/los importe(s) y
            vencimiento que indique el correspondiente cronograma de pagos que no requiera de suscripción adicional al
            presente documento.</p>

        <p><b>SETIMA: RENUNCIA DE DOMICILIO:</b> CREDIPALMO S.A.C. y/o su tenedor podrá entablar acción judicial para
            efectuar el cobro de este pagaré donde lo tuviera por conveniente a cuyo efecto el obligado principal y los
            avalistas solidarios renuncian al fuero de su propio domicilio y a cuantos puedan favorecerlos en el proceso
            judicial o fuera de él señalando como domicilio para todos los efectos y consecuencias que pudieran
            derivarse de la emisión del presente pagaré el indicado en este documento así como en el domicilio de su
            oficina principal y sus agencias sucursales de la empresa lugar donde se enviarán los avisos y se harán
            llegar todas las comunicaciones y/o notificaciones judiciales que resulten necesarias para lo cual se
            someten expresamente a las leyes de la República del Perú y a la competencia de los jueces y salas del
            distrito judicial de la ciudad donde se suscribe el presente pagaré y centro de conciliación que la empresa
            designe.</p>

        <h2 class="seccion centro" style="margin-top: 25px;">GARANTIAS</h2>
        <p style="text-align: center; margin: 15px 0 8px;"><b>AVALISTAS SOLIDARIAS PERMANENTES</b></p>

        <p>Me/nos constituyo/constituimos en aval(s) solidario(s) permanente(s) con ellos deudor(es) y entre nosotros
            mismos renunciando expresamente al beneficio de excusión a favor de la empresa garantizando el pago del
            presente pagaré y el cumplimiento de todas las obligaciones que representa este documento
            comprometiéndome/comprometiéndonos según las mismas disposiciones a las suscritas por el emitente y su
            cónyuge de ser el caso indicadas en los ítems 1,2,3,4,5,6 y 7 del presente pagaré.</p>

        <p style="margin-top: 15px;">Esta garantía es solidaria, incondicional, irrevocable y por plazo indefinido y
            estará vigente mientras no se encuentren totalmente pagadas las obligaciones que represente el presente
            pagaré aceptando desde ahora las prórrogas y/o renovaciones que la empresa conceda a mi(s) nuestro(s)
            avalado(s) para lo cual presto/prestamos mi/ nuestro expreso consentimiento sin que sea necesaria
            mi/nuestra intervención.</p>

        <p style="margin-top: 20px;">FECHA {{ $fechaDocumento }}</p>

        <div class="page-break"></div>

        <h1>FIRMAS</h1>

        <table class="tabla firmas">
            <tr>
                <th style="width: 30%;">CLIENTE/RPTE.LEGAL</th>
                <th style="width: 20%;">HUELLA DIGITAL</th>
                <th style="width: 30%;">FIRMA CONYUGUE</th>
                <th style="width: 20%;">HUELLA DIGITAL</th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <th>FIRMA GARANTE/ AVAL</th>
                <th>HUELLA DIGITAL</th>
                <th>FIRMA GARANTE/AVAL</th>
                <th>HUELLA DIGITAL</th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <table style="width: 100%; margin-top: 120px; border-collapse: collapse;">
            <tr>
                <td style="text-align: center; font-size: 13px;">
                    <div style="width: 35%; margin: 0 auto 6px; border-top: 1px solid #000;"></div>
                    GRUPO CREDIPALMO S.A.C.
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
