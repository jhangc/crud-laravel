<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagaré Crédito Individual</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
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
            margin: 20px;
        }

        h1 {
            text-align: center;
            color: black;
            font-size: 18px
        }

        p {
            margin: 10px 0;
            font-size: 14px;
            text-align: justify
        }


        .tabla {
            width: 100%;
            border-collapse: collapse;
        }

        .tabla,
        .tabla th,
        .tabla td {
            border: 1px solid black;
        }

        .tabla th,
        .tabla td {
            padding: 8px;
            text-align: left;
        }

        .tabla td,
        th {
            font-size: 14px
        }
    </style>
</head>

<body>
    <header class="header">
        <h3>Grupo Credipalmo</h3>
    </header>

    <div class="content">
        <h1>PAGARÉ</h1>

        <table class="tabla">
            <tr>
                <th>NÚMERO PAGARE</th>
                <th>LUGAR DE EMISION</th>
                <th>FECHA DE EMISION</th>
                <th>FECHA DE VENCIMIENTO</th>
                <th>MONEDA</th>
                <th>MONTO</th>
            </tr>
            <tr>
                <td>01</td>
                <td>Tarapoto</td>
                <td>{{ \Carbon\Carbon::parse($prestamo->fecha_desembolso)->translatedFormat('d-F-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($prestamo->fecha_fin)->translatedFormat('d-F-Y') }}</td>
                <td>SOLES</td>
                <td>S/ {{ number_format($prestamo->monto_total, 2) }}</td>
            </tr>
        </table>

        <p>Por este título PAGARE / pagaremos solidariamente a la empresa Grupo Credipalmo S.A.C. su orden o quien este
            hubiera endosado el presente título en su oficina de esta ciudad o donde se presente este título para su
            cobro la suma de S/ {{ number_format($prestamo->monto_total, 2) }} ({{ $montoEnLetras }}).</p>

        <p>La suma materia de obligación contenida en el presente título será cancelada en la misma moneda antes
            expresada, según el cronograma de pago de las cuotas que se incluye en este Pagaré. Así también, nos
            obligamos a las clausulas especiales establecidas en el presente contrato.</p>

        <h3>DATOS DEL CLIENTE/DEUDOR(ES):</h3>
        <p><b>NOMBRE(S)/RAZON SOCIAL:</b> {{ $credito_cliente->clientes->nombre }} <br>
            <b>DNI/RUC N°:</b> {{ $credito_cliente->clientes->documento_identidad }} <br>
            <b>DOMICILIO:</b> {{ $credito_cliente->clientes->direccion }} <br>
            @if ($credito_cliente->clientes->conyuge)
                <b>NOMBRE DEL CODEUDOR/CONYUGUE/RPTE LEGAL:</b> {{ $credito_cliente->clientes->conyuge }} <br>
                <b>DNI N°:</b> {{ $credito_cliente->clientes->dni_conyuge }} <br>
                <b>DOMICILIO:</b> {{ $credito_cliente->clientes->direccion }} <br>
            @endif

        <h3>DATOS DEL AVAL (ES)</h3>
        <p><b>NOMBRE(S)/RAZON SOCIAL:</b> {{ $credito_cliente->clientes->aval }} <br>
            {{-- DNI/RUC N°: {{ $credito_cliente->clientes->dni_aval }} <br> --}}
            {{-- NOMBRE DEL RPTE. LEGAL: {{ $credito_cliente->clientes->nombre_representante_aval }} <br>
            DNI N°: {{ $credito_cliente->clientes->documento_identidad_representante_aval }} <br> --}}
            {{-- DOMICILIO: {{ $credito_cliente->clientes->direccion_aval }} <br>
            NOMBRE DEL CODEUDOR/CONYUGUE/RPTE LEGAL: {{ $credito_cliente->clientes->nombre_conyuge_aval }} <br>
            DNI N°: {{ $credito_cliente->clientes->documento_identidad_conyuge_aval }} <br>
            DOMICILIO: {{ $credito_cliente->clientes->direccion_conyuge_aval }}</p> --}}

        <h3 style="text-align: center">CLAUSULAS ESPECIALES QUE RIGEN EL PRESENTE TITULO VALOR:</h3>
        <p><b>PRIMERO:</b> El vencimiento del pagaré podrá ser renovado y/o prorrogado por su tenedor al cumplimiento
            del plazo establecido en el cronograma por el plazo que éste señale mediante anotación al reverso del título
            por su importe total o por menor cantidad sin que sea necesaria intervención del emitente ni del(los)
            obligados(s) solidario(s). El tenedor queda obligado a informar de las prórrogas que conceda a simple
            requerimiento de cualquier obligado de este pagaré.</p>
        <p><b>SEGUNDO:</b> Este pagaré debe ser pagado solo en la misma moneda que expresa este título valor.</p>
        <p><b>TERCERO:</b> El importe de este pagaré y/o de las cuotas del crédito que representa generarán desde la
            fecha de emisión una tasa de interés compensatorio efectiva anual de {{ intval($prestamo->tasa) }}%.(360 días calendarios) y en caso de
            incumplimiento de pago generará un interés moratorio del {{ intval($prestamo->tasa) }}% tasa efectiva anual de (360 días calendarios)
            la misma que se aplicará en su equivalente de factor diario de {{$tasadiaria }}% sobre el saldo capital de cuota(s) sin
            perjuicio de los gastos y comisiones por los conceptos convenidos pactados e indicados en cronograma de pago
            que es anexo al presente pagaré que se hubiera generado o pudieran generarse de acuerdo al tarifario vigente
            de la empresa Grupo Credipalmo S.A.C. desde la fecha de emisión hasta la cancelación total de la presente
            obligación sin que sea necesario requerimiento alguno para constituirme / constituirnos en mora pues queda
            entendido que esta se producirá de modo automático por el solo hecho del vencimiento de este pagaré.</p>
        <p><b>CUARTO:</b> El obligado principal y los solidarios aceptan igualmente que las tasas de interés
            compensatoria y/o moratoria puedan ser variadas por la Empresa Grupo Credipalmo S.A.C. y/o su último tenedor
            sin necesidad de aviso previo de acuerdo a las tasas que este tenga vigentes.</p>
        <p><b>QUINTO:</b> El importe de este pagaré y/o de sus intereses y comisiones que se indican será cancelado en
            cuotas conforme al cronograma de pagos que se inserta. La falta de pago de una o más cuotas en las fechas
            indicadas facultan a su tenedor a proceder a la ejecución del presente título procediendo su ejecución por
            el solo mérito de haber vencido su plazo y no haber sido prorrogado.</p>
        <p><b>SEXTO:</b> El presente pagaré no requiere ser protestado; salvo el protesto por falta de pago de
            cualquiera de las cuotas con la finalidad de precluir sus plazos a que queda facultado el tenedor conforme a
            lo establecido para tal efecto por la ley de títulos valores.</p>
        <p><b>SÉPTIMO:</b> El importe de este pagaré podrá ser pactado en una o más cuotas según el/los importe(s) y
            vencimiento que indique el correspondiente cronograma de pagos que no requiera de suscripción adicional al
            presente documento.</p>
        <p><b>OCTAVO: RENUNCIA DE DOMICILIO:</b> CREDIPALMO S.A.C. y/o su tenedor podrá entablar acción judicial para
            efectuar el cobro de este pagaré donde lo tuviera por conveniente a cuyo efecto el obligado principal y los
            avalistas solidarios renuncian al fuero de su propio domicilio y a cuantos puedan favorecerlos en el proceso
            judicial o fuera de él señalando como domicilio para todos los efectos y consecuencias que pudieran
            derivarse de la emisión del presente pagaré el indicado en este documento así como en el domicilio de su
            oficina principal y sus agencias sucursales de la empresa lugar donde se enviarán los avisos y se harán
            llegar todas las comunicaciones y/o notificaciones judiciales que resulten necesarias para lo cual se
            someten expresamente a las leyes de la República del Perú y a la competencia de los jueces y salas del
            distrito judicial de la ciudad donde se suscribe el presente pagaré y centro de conciliación que la empresa
            designe.</p>
        <p><b>NOVENO:</b> El emisor del título valor se somete expresamente a la competencia de los jueces y tribunales
            de esta ciudad y señalando como domicilio aquél que aparece indicado en el pagaré donde se efectuarán las
            diligencias notariales judiciales y demás que fuesen necesarias para los efectos del pago.</p>

        <h3 style="text-align: center">GARANTIAS</h3>
        <p style="text-align: center"><b>AVALISTAS SOLIDARIAS PERMANENTES</b></p>
        <p>Me/nos constituyo/constituimos en aval(s) solidario(s) permanente(s) con ellos deudor(es) y entre nosotros
            mismos renunciando expresamente al beneficio de excusión a favor de la empresa garantizando el pago del
            presente pagaré y el cumplimiento de todas las obligaciones que representa este documento
            comprometiéndome/comprometiéndonos según las mismas disposiciones a las suscritas por el emitente y su
            cónyuge de ser el caso indicadas en los items 1234567891011 del presente contrato.</p>
        <p>Esta garantía es solidaria, incondicional, irrevocable y por plazo indefinido y estará vigente mientras no se
            encuentren totalmente pagadas las obligaciones que represente el presente pagaré aceptando desde ahora las
            prórrogas y/o renovaciones que la empresa conceda a mi(s) nuestro(s) avalado(s) para lo cual
            presto/prestamos mi/ nuestro expreso consentimiento sin que sea necesaria mi/nuestra intervención.</p>

        <p>{{ $formattedDate }}</p>
<br>
        <h3 style="text-align: center">FIRMAS</h3>
        @if ($credito_cliente->clientes->conyuge)
        <table class="tabla" style="width: 100%; border-collapse: collapse; border: 1px solid black;">
            <tr>
                <th style="border: 1px solid black;">CLIENTE/RPTE.LEGAL</th>
                <th style="border: 1px solid black;">HUELLA DIGITAL</th>
                <th style="border: 1px solid black;">FIRMA CONYUGUE</th>
                <th style="border: 1px solid black;">HUELLA DIGITAL</th>
            </tr>
            <tr>
                <td style="border: 1px solid black;">{{ $credito_cliente->clientes->nombre }}</td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;">{{ $credito_cliente->clientes->conyuge }}</td>
                <td style="border: 1px solid black;"></td>
            </tr>
            <tr>
                <th style="border: 1px solid black;">AVAL1/RPTE.LEGAL</th>
                <th style="border: 1px solid black;">HUELLA DIGITAL</th>
                <th style="border: 1px solid black;"></th>
                <th style="border: 1px solid black;"></th>
            </tr>
            <tr>
                <td style="border: 1px solid black;">{{ $credito_cliente->clientes->aval }}</td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
                <td style="border: 1px solid black;"></td>
            </tr>
        </table>
        
        @else
        <table class="tabla">
            <tr>
                <th>CLIENTE/RPTE.LEGAL</th>
                <th>HUELLA DIGITAL</th>
                <th>FIRMA AVAL</th>
                <th>HUELLA DIGITAL</th>
            </tr>
            <tr>
                <td>{{ $credito_cliente->clientes->nombre }}</td>
                <td></td>
                <td>{{ $credito_cliente->clientes->aval }}</td>
                <td></td>
            </tr>
        </table>
        @endif

        <br><br><br><br>

        <p style="text-align: center">________________________</p>
        <p style="text-align: center">GRUPO CREDIPALMO S.A.C.</p>
    </div>
</body>

</html>
