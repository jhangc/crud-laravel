<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrato Crédito Grupal</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        h1 {
            text-align: center;
            color:black;
            font-size: 18px
        }

        p {
            margin: 10px 0;
            font-size: 14px;
            text-align: justify
        }

        .signature {
            margin-top: 20px;
        }

        .signature div {
            display: inline-block;
            width: 45%;
            text-align: center;
        }
    </style>
</head>

<body>
    <h1>CONTRATO CRÉDITO GRUPAL</h1>
    <p>Conste por el presente documento el Contrato CREDITO GRUPAL (en adelante EL CRÉDITO)
    que celebra, de una parte, <b>Grupo CrediPalmo SAC,</b> con R.U.C. Nro. 20610069542, con domicilio para los efectos del
    presente contrato en Jirón Juan Vargas N° 248, Distrito Tarapoto, Provincia y Departamento de San Martin,
    debidamente representado por sus apoderados que lo suscriben, a quien en lo sucesivo se denominará Grupo CrediPalmo,
    y de la otra parte EL CLIENTE (en adelante se denominará EL GRUPO:"{{ $prestamo->nombre_prestamo }}" , cuyos datos de identificación se
    consignan al término del presente contrato, de forma física o por medio de diferentes medios electrónicos, que
    establezca la entidad financiera. Celebrando un contrato por medio del cual se regulan los términos y condiciones
    aplicables a la operatividad financiera denominada CREDITO GRUPAL (en adelante “EL CRÉDITO”). </p>
    
    <p>Para iniciar con el contrato de EL CRÉDITO, Grupo CrediPalmo, debe comprobar la identidad de los miembros de EL GRUPO, quienes
    dejaran constancia de la aceptación del presente contrato, así como también, de cualquier otra información que
    corresponda, lo que implica, además, el ingreso de nuevos integrantes. </p>
    <p>Los documentos que forman el presente contrato y del expediente del grupo son:</p>
    <ol style="list-style-type: lower-alpha; font-size:14px">
        <li>Documento de identidad.</li>
        <li>Hoja Resumen.</li>
        <li>Solicitud del crédito.</li>
        <li>Cronograma.</li>
        <li>Documentos de garantía y seguro (cuando correspondan).</li>
        <li>Evaluación crediticia.</li>
    </ol>
    
    <p><b>PRIMERO, SOBRE EL CRÉDITO:</b> La entidad financiera Grupo CrediPalmo, otorgará al GRUPO, previa
    revisión crediticia que realicen de esta, EL CRÉDITO por el importe que se detalla en la hoja de resumen, que Grupo
    CrediPalmo, brindará a EL GRUPO. Este crédito grupal solidario aprobado mediante Resolución SBS N° 4174-2015 que
    aprueba el Reglamento de Créditos Grupales Solidarios, siendo estás solidariamente responsables por las obligaciones
    crediticias adquiridas, siendo EL GRUPO, entendido como un todo, el sujeto pasivo del crédito.</p>
    <p>Los integrantes que conforman EL GRUPO, se encuentran detallados en la cartilla de identificación que forman parte del presente
    contrato.</p>
    <p>Se precisa que, para todos los efectos del presente contrato, tales como cancelación, ejecución de pago
    anticipados o cualquier otra ejecución contractual el cliente es EL GRUPO:{{ $prestamo->nombre_prestamo }} 
    <p><b>SEGUNDO, SOBRE EL GRUPO Y SU REPRESENTACIÓN:</b> El grupo tendrá la denominación consignada en el presente contrato y la elección de
    sus miembros será atribución de EL GRUPO: {{ $prestamo->nombre_prestamo }} </p>
    <p>EL GRUPO, por mayoría de votos ha designado a quienes conformaran el denominado “EL COMITÉ”, el cual estará integrado por el presidente, tesorero y secretario,
    quienes actuaran en representación de EL GRUPO. EL COMITÉ, tendrá la representación de EL GRUPO, durante la vigencia
    del presente contrato, cuyas responsabilidades obran en el acta de compromiso que forma parte del presente
    contrato.</p>
    <p>En ese sentido, EL COMITÉ, será quien represente al GRUPO, de acuerdo a lo dispuesto por el artículo N°
    155 del Código Civil, pudiendo efectuar las siguientes operaciones:</p>
    <ol style="list-style-type: lower-alpha; font-size:14px">
        <li>Suscribir los documentos contractuales de EL CRÉDITO con Grupo CREDIPALMO SAC, indicado en el presente documento, así como; suscribir la solicitud de
            refinanciamiento, reprogramación, congelamiento de EL CRÉDITO otorgado a EL GRUPO y/u otras operaciones que requieran.</li>
        <li>Realizar operaciones de pago de EL CRÉDITO, según el cronograma de pago correspondiente, ello no 
            siendo restrictivo para que el resto de integrantes puedan realizar dicha operación.</li>
        <li>Elección del mecanismo de desembolso de EL CRÉDITO</li>
        <li>Recibir toda clase de comunicaciones emitidas por Grupo CrediPalmo. Dicha facultad no
            enerva la responsabilidad de Grupo CrediPalmo sobre el deber de informar los cambios contractuales u otros aspectos
            a EL GRUPO, de acuerdo a la normativa aplicable</li>
        <li>Otras facultades que se le otorguen.</li>
    </ol>
        
    <p><b>TERCERO, SOBRE LA SOLIDARIDAD: </b>Conforme a lo indicado en la cláusula primera, los miembros de EL GRUPO, reconocen que se convierten en
    deudores solidarios entre sí, aval solidario a fin de comprometiéndose a pagar todas las obligaciones asumidas por
    EL GRUPO a favor de Grupo CrediPalmo, incluyendo los intereses compensatorios, intereses moratorios o concepto
    permitido por la normativa aplicable que, cumplan con el criterio señalado en la cláusula quinta siguiente,
    comisiones y gastos de toda clase que se deriven de este Contrato, sin reserva ni limitación alguna. </p>
    <p>Además, los avales solidarios por medio de la presente cláusula intervienen como avales solidarios las personas consignadas al
    final del presente contrato. Asimismo, conforme al inciso primero del artículo 1883º del Código Civil, queda
    establecido que el aval hace expresa renuncia al beneficio de excusión, por lo que para la ejecución de garantías no
    será necesario efectuar previamente la excusión de sus bienes. Los derechos y obligaciones de los avales solidarios
    se encuentran descritas en los anexos del presente contrato.</p>
    <p>La entidad financiera, trasladará el cobro de gastos y comisiones a EL GRUPO, según lo señalado en el Art. 16 del Reglamento de Gestión de Conducta de Mercado
    del Sistema Financiero (RGCM). Siendo en tal sentido, que el cobro de comisiones y/o gastos procede conforme a lo
    dispuesto en el precitado marco normativo.</p>
    <p><b>CUARTO, SOBRE EL DESEMBOLSO DE LOS CRÉDITOS:</b> Grupo CrediPalmo pondrá a disposición de EL GRUPO los siguientes mecanismos de desembolso de EL CRÉDITO a elección de los integrantes
    de EL GRUPO {{ $prestamo->nombre_prestamo }}:</p>
    <ol style="list-style-type: lower-alpha; font-size:14px">
        <li>Efectivo.</li>
        <li>Cheque girado a la orden de cada integrante de EL GRUPO.</li>
        <li>Dinero electrónico de contar con una Billetera Electrónica.</li>
    </ol>

    <p>Previo al desembolso del crédito grupal, EL GRUPO dejaría el
    10% cómo garantía del crédito, de haberse cancelado la totalidad del crédito de devolverá la garantía y en caso de
    incumpliendo de las cuotas del crédito, la garantía compensará al saldo vencido del crédito.</p>
    
    <p><b>QUINTO, CRONOGRAMA DE PAGOS:</b>Grupo CrediPalmo entregará a EL GRUPO, un CRONOGRAMA DE PAGO GRUPAL del crédito aprobado, el
    cual forma parte integrante del presente contrato como anexo, en el que se detalla el número de cuotas de pago
    grupal, el monto total de cada una de ellas y la fecha de vencimiento de las mismas, el cual se obligan
    estrictamente a cumplir. Asimismo, Grupo CrediPalmo entregará a cada uno de LOS CLIENTES un CRONOGRAMA DE PAGO
    INDIVIDUAL, los cuales también forman parte integrante del presente contrato, en el que se detalla el número de
    cuotas de pago, el monto que le corresponde aportar a cada uno de los integrantes de EL GRUPO a las cuotas de pago
    grupal, la fecha de vencimiento de las mismas, la tasa de interés compensatorio y moratorio o penalidad que Grupo
    CrediPalmo aplique durante la vigencia del contrato y que se encuentran detalladas en la hoja resumen que forma
    parte conjunta con el presente contrato.</p> 
    
    <p>El cronograma de pagos individual es la expresión de manera
    proporcional de cuanto le corresponde pagar al EL GRUPO, en tal sentido el cronograma de pagos individual y grupal
    se encuentran relacionados, por lo que el realizar un pago o dejar de hacerlo en la forma pactada en el cronograma
    de pagos individual afectará al cronograma grupal.</p>
    <p>El cronograma de pagos individual contendrá los intereses,
    comisiones y gastos, y se proporcionará una Hoja Resumen por cada cronograma individual emitido. EL GRUPO deberán
    efectuar el pago de las referidas cuotas, en efectivo, en la misma moneda del crédito; los días del mes señalados en
    el CRONOGRAMA DE PAGOS, y en caso de resultar día no hábil, podrá efectuar el pago el día hábil inmediato posterior
    a éste, por los importes que Grupo CrediPalmo indique conforme al CRONOGRAMA DE PAGOS a que se refiere esta
    cláusula.</p>
    <p>Asimismo, en caso de cuotas vencidas, Grupo CrediPalmo realizará la compensación con la cuenta de
    ahorros que LOS CLIENTES mantengan en Grupo CrediPalmo. Las partes acuerdan que el orden de imputación para pago de
    deudas será el siguiente: Comisiones, gastos, intereses y capital en ese orden, de conformidad con lo establecido en
    el artículo 1257 del Código Civil, pudiendo ser invertido a solicitud de EL GRUPO y aceptación de Grupo
    CrediPalmo.</p>
    <p>Los pagos que realice EL GRUPO en virtud del Contrato se aplicarán en el orden siguiente: a)
    intereses compensatorios; b) interés moratorio que cumpla con el criterio indicado en el párrafo precedente, en caso
    corresponda; c) gastos - comisiones y d) capital.</p>
    <p>La entidad financiera se encuentra prohibida de realizar
    cualquier acción que sea contraria al marco legal vigente aplicable a las operaciones de crédito; lo que involucra,
    sin que resulte limitativa la descripción, la capitalización de los intereses moratorios que se generen, otra
    comisión o gasto en caso de incumplimiento o atraso en el pago de EL CRÉDITO por parte de EL GRUPO; sin embargo, en
    caso de modificación a las prohibiciones legales antes señaladas, Grupo CrediPalmo podrá efectuar el cobro de
    interés moratorio o del concepto aplicable en caso de incumplimiento según lo permita la normativa
    vigente.</p>
    <p><b>SEXTO, SOBRE EL ADELANTO DE CUOTAS:</b> EL GRUPO podrán realizar pagos por encima de la cuota exigible
    en el periodo, en cualquiera de nuestras oficinas de atención al público, sin restricciones o limitaciones, libre de
    pago de comisiones, gastos, penalidades o cobros de naturaleza o efecto similar, tomando conocimiento, al realizar
    la operación, del monto que resulta aplicable por concepto de impuestos, para lo cual deberá tener en consideración
    las siguientes condiciones:</p>
    <p>Se considerará Pago Anticipado, cuando el pago es mayor de dos cuotas, que incluye
    aquella exigible en el periodo, y trae como consecuencia la aplicación del monto al capital del crédito, con la
    consiguiente reducción de los intereses, las comisiones y los gastos al día del pago.</p>
    <p>Los Pagos Anticipados disminuirán el monto de las cuotas o el plazo del pago, a elección de EL GRUPO, debiendo realizar dicha indicación
    al momento de comunicar su intención de realizar el Pago Anticipado, el cual constará en documento proporcionado por
    Grupo CrediPalmo. </p>
    <p>Si el Pago Anticipado fuere realizado por una tercera persona o existieran dificultades para
    efectuar la elección al momento de efectuarlo, EL GRUPO dentro de los 15 días calendarios siguientes, podrán enviar
    comunicación escrita a Grupo CrediPalmo indicando su elección; pasada esta fecha EL GRUPO autorizan, por esta
    cláusula, la reducción del número de cuotas, todo esto de acuerdo a lo establecido en el numeral 29.3 del <i>Reglamento
    de Gestión de Conducta de Mercado del Sistema Financiero (“Reglamento”), aprobado por la Superintendencia de Banca,
    Seguros y AFP (“SBS”) mediante Resolución N° 3274-2017 o las normas que las modifiquen o sustituyan de
    Transparencia.</i> </p>
    <p>Grupo CrediPalmo, a solicitud de EL GRUPO, procederá a emitir nuevo cronograma de pagos con el
    saldo remanente del crédito y/o nuevo número de cuotas, el cual será remitido vía correo electrónico a la dirección
    indicada por EL GRUPO, dentro de los siete (7) días de efectuada dicha solicitud. Sin perjuicio de lo expuesto, en
    estos casos, EL GRUPO podrán manifestar expresamente su voluntad para adelantar el pago de cuotas, la cual constará
    en documento proporcionado por Grupo CrediPalmo, procediéndose a aplicar el monto pagado en exceso sobre la cuota
    del periodo a las cuotas inmediatas siguientes.</p>
    <p>Se considera Adelanto de Cuotas a los pagos efectuados por EL
    GRUPO menores o iguales al equivalente de dos cuotas, que incluyen aquella exigible en el periodo. En estos casos,
    Grupo CrediPalmo procederá a aplicar el monto pagado en exceso sobre la cuota del periodo, a las cuotas inmediatas
    siguientes no vencidas. Sin perjuicio de ello, en estos casos, EL GRUPO podrán requerir, antes o al momento de
    efectuarse el pago, que deberá procederse a la aplicación del pago como anticipado, resultando aplicable lo indicado
    en el numeral 4.1 de la presente cláusula.</p>
    <p><b>SEPTIMO, INTERESES, COMISIONES Y GASTOS: </b>La tasa de interés compensatorio, moratorio o penalidad, comisiones y 
    gastos que Grupo CrediPalmo aplicará al saldo deudor de EL GRUPO,
    serán las expresadas en el CRONOGRAMA DE PAGOS y HOJA RESUMEN, que forman parte conjunta con el presente contrato y
    que han sido debidamente informadas a EL GRUPO para su conformidad. Asimismo, EL GRUPO convienen en cancelar los
    gastos y comisiones que le fueran aplicables, en concordancia con los criterios aplicables a las comisiones y gastos
    de la Resolución <i>SBS N° 8181-2012 (Reglamento de Transparencia de Información y Contratación con Usuarios del
    Sistema Financiero)</i>.</p>
    <p>Asimismo, las partes entenderán por TEA a la tasa de costo efectivo anual que permite
    igualar el valor actual de todas las cuotas con el monto que efectivamente haya sido recibido en préstamo. Para este
    cálculo se incluirán las cuotas que involucran el principal, intereses, comisiones y gastos, que de acuerdo a lo
    pactado serán trasladados al cliente, incluidos los seguros. No se incluirán en este cálculo aquellos pagos por
    servicios provistos por terceros que directamente sean pagados por el cliente, ni los tributos que resulten
    aplicables.</p>
    <p><b>OCTAVO, CESIÓN DE POSICIÓN CONTRACTUAL:</b> Grupo CrediPalmo podrá ceder sus derechos y/o su posición
    contractual en este Contrato a cualquier tercero, prestando los miembros de EL GRUPO de conformidad con lo indicado
    en el Código Civil, en este acto, su consentimiento anticipado a la referida cesión, lo cual será informado mediante
    los mecanismos previstos en la cláusula Décimo Quinta.</p>
    <p><b>NOVENO, MODIFICACIONES CONTRACTUALES:</b> Queda expresamente
    convenido entre las partes que Grupo CrediPalmo podrá variar unilateralmente las comisiones y gastos. En caso de
    modificaciones que requieran notificación previa, Grupo CrediPalmo procederá a notificar al cliente conforme a los
    medios de comunicación directos establecidos en la cláusula sexta, con 45 días de anticipación.</p>
    <p>Las tasas de interés podrán ser modificadas en los siguientes casos: </p>
    <p>1. Por novación de la obligación, conforme a las
    disposiciones del Código Civil. </p>
    <p>2. De mutuo acuerdo con EL GRUPO como resultado de una efectiva negociación
    entre las partes. </p>
    <p>3. Cuando la Superintendencia, previo informe favorable del Banco Central de Reserva del Perú,
    autorice al sistema financiero en general por circunstancias extraordinarias e imprevisibles que pongan en riesgo el
    propio sistema, de conformidad con lo señalado en el artículo 6° de la Ley Complementaria a la Ley de Protección al
    Consumidor en Materia de Servicios Financieros, Ley N° 28587 y sus normas modificatorias. </p>
    <p>4. En forma unilateral
    por Grupo CrediPalmo, en cualquier momento durante la vigencia del contrato, cuando se trate de modificaciones a la
    tasa de interés que impliquen condiciones más favorables para EL GRUPO, aplicándose las nuevas tasas de interés de
    manera inmediata y comunicado de este hecho de manera posterior, en tal sentido, no siendo exigible el envío de una
    comunicación previa. En el caso establecido en el numeral 4 de la presente cláusula, Grupo CrediPalmo SAC difundirá
    las nuevas tasas de interés aplicables al contrato a través de anuncios en sus oficinas de atención al público y, a
    solo criterio de Grupo CrediPalmo, a través de anuncios en medios de comunicación masivos. Grupo CrediPalmo
    comunicará a EL GRUPO la modificación de las cláusulas contractuales, comisiones y gastos, así como su fecha de
    entrada en vigencia; con una anticipación no menor a cuarenta y cinco días, cuando dichas modificaciones representen
    un incremento respecto de lo pactado o sea desfavorable para EL GRUPO. Grupo CrediPalmo comunicará las
    modificaciones contractuales, distintas a las tasas de interés, comisiones y gastos, descritos en la cláusula sexta,
    tales como: (i) la resolución del contrato por causal distinta al incumplimiento; (ii) la limitación o exoneración
    de responsabilidad por parte de Grupo CrediPalmo; y (iii) la incorporación de servicios que no se encuentren
    relacionados al préstamo. En dichos casos, las modificaciones contractuales deberán ser informadas a EL GRUPO,
    conjuntamente con la indicación de la fecha a partir de la cual entrarán en vigencia, con no menos de cuarenta y
    cinco días de anticipación y mediante los medios de comunicación directa descritos en la cláusula séptima, cuando
    así se precise o requiera por mandato de las normas legales vigentes. </p>
    
    <p>Cuando se trate de modificaciones
    contractuales que impliquen condiciones más favorables para EL GRUPO, se aplicarán de manera inmediata y se
    comunicaran posteriormente, a elección de Grupo CrediPalmo, por uno o más de cualquiera de los siguientes medios:
    página web, avisos en las agencias y locales, avisos en medios de comunicación masiva, cartas dirigidas al domicilio
    del cliente, vía correos electrónicos, redes sociales o anotaciones en comprobantes de pagos.</p>
    <p><b>DECIMO, RESOLUCIÓN DEL CONTRATO:</b> EL GRUPO podrá poner término a este Contrato cuando así lo decida, dando aviso a Grupo CrediPalmo, sin
    perjuicio de su obligación de pagar de manera inmediata el Saldo Deudor total de EL CRÉDITO que liquide Grupo
    CrediPalmo. Grupo CrediPalmo podrá mantener vigente EL CRÉDITO hasta la cancelación total del Saldo Deudor.</p>
    <p>El plazo del Contrato es a plazo fijo; sin embargo, Grupo CrediPalmo podrá dar por vencidos todos los plazos y/o
    resolver de pleno derecho el Contrato desde la fecha que señale en la comunicación que se realice a EL GRUPO
    previamente, sin necesidad de declaración judicial, en los siguientes casos:</p>
    <ol style="list-style-type: lower-alpha; font-size:14px">
        <li>Si EL GRUPO: (i) no paga en la
            forma, plazo y oportunidad convenidas el importe que figura en este Contrato y/o en el cronograma de pagos
            respectivo; (ii) no cumple cualquier otra obligación frente a Grupo CrediPalmo como deudor.<br>Se presentase
            cualquier otra situación mediante la cual se suspenden sus pagos.</li>
        <li>Si cualquier obligación crediticia de EL GRUPO
            para con Grupo CrediPalmo u otra empresa del Sistema Financiero es clasificada en la categoría de Dudosa o Pérdida o
            si la situación crediticia de EL GRUPO, bajo las políticas internas de la empresa, es considerada inconsistente y/o
            deficiente, en caso dicha nueva situación crediticia afecte el cumplimiento de las obligaciones de EL GRUPO respecto
            del presente Contrato.</li>
        <li>Si mantener vigente EL CRÉDITO implica el incumplimiento de las políticas corporativas de
            Grupo CrediPalmo o de alguna disposición legal, en especial aquellas referidas a políticas crediticias o de lavado
            de activos y financiamiento del terrorismo.</li>


        <li>Por cese o fallecimiento de miembros de EL GRUPO. </li>
        <li>Si EL GRUPO no cuenta con un seguro de desgravamen vigente en los términos previstos en el presente documento.</li>
        <li>Si EL GRUPO incumple cualquiera de las obligaciones que asumen en el presente Contrato y/o atenta contra las políticas internas
            que tiene Grupo Credipalmo en cada uno de sus canales o plataformas.</li>   
    
        <li>Si Grupo CrediPalmo considera que no resulta
            conveniente mantener relaciones comerciales con EL GRUPO por tener una conducta intolerable, deshonesta, agresiva u
            ofensiva por parte de EL GRUPO con el personal de Grupo CrediPalmo u otros Clientes de Grupo CrediPalmo, realizada
            en las oficinas de Grupo CrediPalmo o a través de los canales que Grupo CrediPalmo ponga a su
            disposición. </li>
        <li>Mantiene saldos menores en beneficio de Grupo CrediPalmo y éste sobre la base de sus políticas
            internas decide condonar dichos montos. </li>
    </ol>
    
    <p>Para los supuestos enunciados, Grupo CrediPalmo, dependiendo de la
    causal, podrá bloquear temporalmente la cuenta donde se soporta el crédito comunicando a través de los medios de
    comunicación establecidos en el presente Contrato, sin necesidad de aviso previo, hasta que EL GRUPO regularice la
    situación que originó el bloqueo y, en caso ello no ocurra, podrá proceder a comunicar la resolución del Contrato a
    través de los medios de información directos establecidos en el presente documento. Grupo CrediPalmo comunicará a
    través de los medios directos previamente a la resolución. </p>
    <p>En este caso, EL GRUPO deberá pagar de manera
    inmediata la totalidad de la deuda. De la misma manera, Grupo CrediPalmo, en aplicación de la normativa prudencial,
    podrá terminar de manera anticipada este Contrato cuando así lo decida, señalando en la comunicación
    correspondiente, el motivo de la misma. Dicho aviso se cursará en el plazo de siete (7) siete días posteriores a la
    aplicación de dicha resolución. En cualquier caso, de resolución o terminación anticipada del Contrato, EL GRUPO,
    bajo su responsabilidad, se obliga a cancelar dentro de las veinticuatro (24) horas siguientes o dentro del plazo
    adicional que le otorgue Grupo CrediPalmo expresamente y por escrito, el íntegro del Saldo Deudor pendiente de pago
    según la liquidación que realice Grupo CrediPalmo.</p>
    <p><b>DÉCIMO PRIMERO, DE LOS RETIROS DE ALGUN INTEGRANTE U OTRO SUPUESTO:</b>
    De conformidad con la Resolución S.B.S. N° 4174-2015, Reglamento de Créditos Grupales Solidarios y sus
    modificatorias, EL GRUPO cuenta con la capacidad de autogestión, en virtud del cual puede; determinar la
    conformación del mismo, pudiendo acordarse el retiro de alguna(s) de la(s) de la(s) integrante(s), por
    incumplimiento de las condiciones contractuales señaladas en el presente Contrato, para lo cual EL GRUPO, a través
    de EL COMITÉ, formulará la solicitud del retiro de la(s) integrante(s) a través del procedimiento que Grupo
    CrediPalmo establezca, tal operación no afectará la calidad de deudor de EL GRUPO. Para la formulación de la
    solicitud de retiro de la(s) integrante(s) deben intervenir, como mínimo, dos (2) de tres (3) miembros de EL COMITÉ,
    de manera conjunta. Dicha solicitud se encontrará sujeta a la aprobación de Grupo CrediPalmo, dentro del plazo
    establecido para ello, el mismo que se encontrará publicado en la página web de la empresa. La(s) integrante(s) que
    sean retiradas de EL GRUPO mantendrán, por la parte que proporcionalmente corresponda respecto del Saldo Deudor de
    EL GRUPO, el crédito individual que corresponda frente a Grupo CrediPalmo. En virtud a ello, Grupo CrediPalmo
    entregará a la(s) integrante(s) que sean retiradas de EL GRUPO, los documentos contractuales que correspondan. De la
    misma forma, Grupo CrediPalmo entregará a EL GRUPO, a través del COMITE, el cronograma de pagos actualizado, Hoja
    Resumen y Cartilla de Identificación.</p> 
    <p>En caso de fallecimiento o invalidez de sus miembros, EL GRUPO deberá
    comunicar de ello a Grupo CrediPalmo; debiendo tener en cuenta el procedimiento aplicable para dichos casos, que se
    encuentra más a detalle ara llegar toda la información por parte de Grupo CrediPalmo</p>
    <p><b>DÉCIMO SEGUNDO, INCAPACITAD PARA FIRMAR:</b>En caso algún integrante de EL GRUPO sea iletrado y/o tenga incapacidad para firmar y el monto otorgado
    de manera individual al interior de EL GRUPO es menor a S/ 3,000.00 (Tres Mil con 00/100 Soles), sólo deberá
    imprimir su huella digital en el presente Contrato y demás documentos. En caso el monto sea mayor o igual a S/
    3,000.00 (Tres Mil con 00/100 Soles), deberá imprimir su huella digital en el presente Contrato y demás documentos,
    con la firma de un testigo a ruego. En ningún caso los colaboradores de Grupo CrediPalmo firmarán a ruego y/o
    encargo.</p> 
    <p><b>DÉCIMO TERCERO, NORMAS:</b> De conformidad con lo establecido en el artículo 85 del Código de Protección y
    Defensa del Consumidor, Grupo CrediPalmo podrá modificar el presente contrato en aspectos distintos a las tasas de
    interés, comisiones o gastos sin necesidad de cursar el aviso previo a que se requiere la cláusula quinta; o incluso
    podrá resolverlo sin comunicación previa; en los siguientes casos:</p>

    <ol style="list-style-type: lower-alpha; font-size:14px">
        <li>Cuando en cumplimiento de las normas
            prudenciales emitidas por la Superintendencia de Banca, Seguros y Administradoras Privadas de Fondos de Pensiones en
            materia de sobreendeudamiento de deudores minoristas, y en aplicación de las políticas implementadas por Grupo
            CrediPalmo para la identificación de los niveles de endeudamiento de sus deudores minoristas, detecte que EL GRUPO
            han incurrido en riesgo de sobreendeudamiento haciendo presumir un potencial deterioro de su calidad crediticia y
            poniendo en riesgo el pago de las obligaciones derivadas del presente contrato.</li>
        <li>Por consideraciones del perfil
            de actividad de EL GRUPO vinculadas al Sistema de Prevención del Lavado de Activos y Financiamiento del Terrorismo.</li>
        <li>En los casos de falta de transparencia de EL GRUPO, cuando se compruebe, como resultado de la evaluación
            realizada a la información proporcionada por EL GRUPO, que dicha información es inexacta, incompleta, falsa o
            inconsistente con la información previamente declarada o entregada por EL GRUPO, sin perjuicio de la responsabilidad
            penal que pueda caber de conformidad con la normatividad aplicable. En caso que Grupo CrediPalmo decidiera modificar
            las condiciones contractuales o resolver el presente contrato por las causales indicadas en esta cláusula, deberá
            comunicarlo a EL GRUPO dentro de los siete días calendario posteriores a dicha modificación o resolución, empleando
            cualquiera de los siguientes medios directos: (i) comunicaciones al correo electrónico señalado por LOS EL GRUPO;
            (ii) comunicaciones escritas al domicilio de EL GRUPO y (iii) comunicaciones telefónicas al domicilio de EL GRUPO
            que puedan ser acreditadas fehacientemente. Grupo CrediPlamo deberá sustentar la(s) causal(es) que justifican las
            modificaciones contractuales o la resolución del contrato, conforme a lo indicado en esta cláusula.</li>

    </ol>


    <p><b>DÉCIMO CUARTO, PAGARÉ:</b>EL GRUPO declaran haber emitido pagarés incompletos, cuya copia les ha sido entregada a la
    suscripción del presente contrato. Asimismo, EL GRUPO, cuando hayan incurrido en una las causales de resolución del
    presente contrato, facultan a Grupo CrediPalmo para integrar los pagarés incompletos consignando como fecha de
    emisión aquella en la cual Grupo CrediPalmo haya desembolsado a EL GRUPO el crédito materia del presente contrato y
    como fecha de vencimiento la que corresponda al día en que se complete el título valor con la liquidación efectuada
    por Grupo CrediPalmo considerando el importe del saldo del préstamo más los intereses compensatorios, moratorios o
    penalidades, gastos y comisiones. También se consignarán la tasa de interés compensatoria y moratoria pactada en
    este contrato y descritas en la Hoja Resumen. Una vez completado el pagaré de acuerdo al párrafo anterior, Grupo
    CrediPalmo procederá de ser el caso a protestarlo y ejercitará las acciones de ley que correspondan; todo ello
    conforme al art. 10º de la Ley Nº 27287 – Ley de Títulos Valores y disposiciones de la Superintendencia de Banca,
    Seguros y AFP, que EL GRUPO declaran conocer y haber recibido explicación de parte de Grupo CrediPalmo en sus
    alcances y uso de este pagaré incompleto. EL GRUPO aceptan y dan por válidas todas las renovaciones y prorrogas
    totales o parciales que se anoten en el Pagaré, aun cuando no estén suscritas por ellos.</p> 

    <p>Se deja expresa constancia que de conformidad con lo dispuesto por el artículo 1279° del Código Civil, la emisión del pagare a que
    se refiere esta cláusula; así como la de cualquier otro título valor en que conste cualquiera de las deudas de EL
    GRUPO no constituirá novación o sustitución de la obligación primitiva o causal. La renovación del pagaré, o de
    darse el caso de cualquier cambio accesorio de la obligación, no constituye novación de la obligación principal. Si
    se perjudicara el/los pagaré/s mencionados, la obligación contenida en ellos no quedará extinguida, de conformidad
    con lo dispuesto en el artículo 1233° del Código Civil. EL GRUPO autorizan a Grupo CrediPalmo de manera irrevocable,
    para que en caso de que la obligación representada en el pagaré antes indicado sea pagada en su totalidad por EL
    GRUPO, destruyan por cuenta de estos últimos los pagarés, sin responsabilidad para Grupo CrediPalmo. A solicitud de
    EL GRUPO, se entregará constancia de esta destrucción y de no haber sucedido, se devolverá el Titulo
    Valor.</p>
    <p><b>DÉCIMO QUINTO, CAMBIO DE DOMICILIO:</b> Para los fines del presente contrato de préstamo, las partes
    establecen como domicilio de LOS CLIENTES el indicado al final del presente contrato y en la HOJA RESUMEN. El cambio
    de domicilio será comunicado por cualquier medio escrito. Para el caso que Grupo CrediPalmo recurra a la vía
    judicial con el objeto de hacer efectivo el importe del préstamo, las partes se someten a la competencia de los
    jueces y tribunales del lugar de celebración del presente contrato, a cuyo efecto EL GRUPO renuncian al fuero de su
    domicilio.</p>
    <p>Las partes acuerdan que en tanto no se comunique por escrito el cambio de domicilio de cualquiera de
    estas, las diversas diligencias, actuaciones, notificaciones, requerimientos o comunicaciones, surtirá efectos
    legales en el domicilio señalado en la CARTILLA DE IDENTIFICACIÓN, cuya dirección declarada en dicho documento será
    la acordada por EL GRUPO. En caso se realice el cambio de domicilio, esta entrará en vigencia a los quince (15) días
    de recibido el comunicado de variación de cualquiera de las partes.</p>
    <p><b>DÉCIMO SEXTO, SOLUCIÓN DE CONTROVERSIAS:</b>
    Las partes acuerdan que en caso surjan controversias sobre asuntos materia del presente Contrato, incluidas las
    referidas a rescisión, conclusión, ineficacia, nulidad, invalidez y/o resolución por incumplimiento del mismo, a
    elección de cualquiera de las partes, podrá optar por accionar y someterse ante:</p>
    <ol style="list-style-type: lower-alpha; font-size:14px">
        <li>El Poder Judicial de la ciudad donde firmen el Contrato.</li>
        <li>Sede Arbitral, siendo que EL GRUPO podrá elegir dicha modalidad de resolución de
            controversias, siendo obligatorio para Grupo CrediPalmo aceptar someterse a dicha vía cuando sea elegida por EL
            GRUPO.</li>

    </ol>
        
    
    <p>Por su parte, en los casos que Grupo CrediPalmo solicite la utilización de la vía arbitral, no será
    obligatorio para EL GRUPO someterse a dicha vía. Grupo CrediPalmo pone a disposición de EL GRUPO la atención de
    reclamos mediante los siguientes canales:</p>
    <br>De manera presencial a través de cualquiera de nuestras agencias a
    nivel nacional <br>A través de nuestra página web <br>Llamando a la central telefónica <br>Cualquiera de los
    integrantes de EL GRUPO podrá presentar el reclamo, para lo cual deberán presentar la información indicada en la
    página web o cualquiera de los canales antes indicados. Grupo CrediPalmo brindará la respuesta al cliente y/o
    usuario, de acuerdo a lo dispuesto en la normativa vigente en materia de atención de reclamos.<br>DÉCIMO SEPTIMO,
    DERECHO DE COMPENSACIÓN: De conformidad con lo establecido en el numeral 11 del artículo 132º de la Ley Nº 26702 –
    Ley General del Sistema Financiero y del Sistema de Seguros y Orgánica de la Superintendencia de Banca, Seguros y
    AFP, Grupo CrediPalmo podrá cobrar (compensar) en forma parcial o total el monto adeudado (obligaciones vencidas y
    exigibles) por EL GRUPO, quedando autorizado a debitar de cualquier cuenta de los miembros de este último o a
    liquidar cualquier bien que se encuentre en custodia de Grupo CrediPalmo. Con la finalidad de cancelar la deuda en
    la moneda en que ha sido contraída, Grupo CrediPalmo podrá proceder a la conversión de moneda de acuerdo al tipo de
    cambio vigente para Grupo CrediPalmo a la fecha en que se realice la operación. Asimismo, Grupo CrediPlamo podrá
    tomar las siguientes acciones: <br>Terminar de manera anticipada el Contrato, en cuyo caso, la consecuencia
    inmediata será que EL GRUPO se encuentre obligado a pagar el total del Saldo Deudor que tenga con Grupo CrediPalmo.
    <br>Bloquear temporal o definitivamente la cuenta. <br>Abrir una cuenta corriente (sin chequera) a nombre de EL
    GRUPO bajo los términos y condiciones generales que para la apertura de dicha clase de cuentas tenga aprobados en
    esa ocasión Grupo CrediPalmo, cargando en esa cuenta los saldos deudores o requerir el pago de su acreencia, según
    las liquidaciones que practique, de conformidad con la ley aplicable. <br><br>En cualquiera de los casos antes
    señalados, Grupo CrediPalmo comunicará en un plazo posterior no mayor a quince (15) días la aplicación del supuesto
    correspondiente mediante los mecanismos de comunicación directos establecidos en la cláusula Décimo Quinta. En el
    supuesto que, se hubiesen procesado Transacciones (abonos y/o cargos, aperturas, duplicidad de códigos, entre otros)
    con error operativo, EL GRUPO autoriza a Grupo CrediPalmo, sin previo aviso, a realizar cargos y/o extornos o
    regularizaciones que se hubiesen podido realizar en la cuenta o crédito, los mismos que, de ser el caso, serán
    informados con posterioridad a EL GRUPO, de conformidad con los mecanismos de información directos en la cláusula
    Décimo Quinta.<br><br>DÉCIMO OCTAVO: GESTIÓN DE COBRANZA Y GRABACIÓN DE LLAMADAS: EL GRUPO declara haber prestado su
    consentimiento sobre la posible gestión de cobranza que deba ser realizada por Grupo CrediPalmo o por terceros, en
    caso de incumplimiento de EL GRUPO, Asimismo, EL GRUPO: CHICAS ……………………………., autoriza en forma expresa a Grupo
    CrediPalmo a realizar, de considerarlo necesario, las gestiones de cobranza por vía telefónica, escrita u otro
    medio, de conformidad con la Ley Aplicable.<br>EL GRUPO autoriza a Grupo CrediPalmo a grabar cualquier tipo de
    conversación, solicitud, autorización, instrucción, adquisición y en general cualquier otro tipo de orden o
    manifestación de voluntad, relacionado con EL CRÉDITO, comprometiéndose a mantener absoluta reserva de las
    mismas.</p>
    <br><br>Firmado en ………………… el …… del mes de ……. de 202…<br><br><br><br><br><br><br><br><br><br>
</body>

</html>
