<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carta de Cobranza Crédito Grupal</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 2;
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
        <h1>CREDITO GRUPAL DEUDA VENCIDA</h1>

        <p><b>GRUPO:</b> {{$prestamo->nombre_prestamo}}</p>
        <p><b>Deuda Vencida: </b> S/ {{$deudaVencida}}</p>
        <p><b>Días de atraso: </b>{{$diasDeAtraso}}</p>
        <p><b>FECHA:</b> {{$formattedDate}}</p>

        <p>Estimada Clienta:</p>

        <p>Por intermedio de la presente, tenemos a bien dirigirnos a usted, y a la vez recordarle que el día {{$formattedfechadesembolso}} usted y demás integrantes suscribieron un contrato mutuo, del crédito CredimujerPalmo, otorgado por Grupo Credipalmo, donde todas en calidad de titulares y avales solidarios, en el cual implica una obligación solidaria por parte del grupo de acuerdo con lo establecido en la cláusula tercera del contrato del préstamo CredimujerPalmo, en la cual existe un atraso en el pago por el monto de {{$deudaVencida}} y que a la fecha cuenta con {{$diasDeAtraso}} días de atraso.</p>

        <p>Le solicitamos que en conjunto con su grupo, puedan regularizar esta situación en el menor plazo posible, y así evitar que la cuota se incremente, por efecto de los intereses moratorios. Recordemos que el cumplimiento puntual de los pagos del crédito hace posible estar bien calificadas en las centrales de riesgo y puedan acceder a más créditos.</p>

        <p>Para mayor información puede acercarse a nuestra oficina ubicada en la calle JR. Juan Vargas N°248 – Tarapoto o comunicarse con su asesor al telefono {{$responsable->telefono?? 'N/A'}}.</p>
    </div>
</body>

</html>
