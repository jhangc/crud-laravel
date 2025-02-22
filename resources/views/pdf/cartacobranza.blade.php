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
        <h1>CARTA DE COBRANZA DE DEUDA VENCIDA</h1>

        <p><i>Tarapoto {{ $formattedDate }}</i></p>

        @php
            $cliente = $prestamo->creditoClientes->first()->clientes;
        @endphp

        <p><b>SEÑOR(A):</b> {{ $cliente->nombre }}</p>
        <p><b>DIRECCIÓN:</b> {{ $prestamo->clientes->first()->direccion }}</p>


        <p><b>DISTRITO:</b> {{ $cliente->distrito->dis_nombre }}
            <b>PROVINCIA:</b> {{ $cliente->distrito->provincia->pro_nombre }}
            <b>DEPARTAMENTO:</b> {{ $cliente->distrito->provincia->departamento->dep_nombre }}
        </p>
        <p><b>DEUDA VENCIDA (*) </b> S/ {{ $deudaVencida }}</p>
        <p><b>DIAS DE ATRASO:</b> {{ $diasDeAtraso }}</p>

        <p>De nuestra consideración:</p>
        <p>Por intermedio de la presente tenemos a bien dirigirnos a usted y a la vez recordarle que el día
            {{ $formattedfechadesembolso }} usted suscribió un pagaré en su calidad de titular del crédito a favor de
            nuestra representada, el mismo que a la fecha cuenta con {{ $diasDeAtraso }} días de atraso por lo que le
            solicitamos acercarse a nuestra oficina para que realice el pago de la deuda vencida. Se ejerce este derecho
            con base en el inciso primero del artículo 12 del Código Civil.</p>

        <p>Es importante manifestarle que la puntualidad en los pagos evitará que la deuda se incremente por efecto de
            los intereses moratorios y también lo califique como buen cliente, evitando mala calificación por
            incumplimiento de pago en las centrales de riesgos. Además, lo ayudará a mantener una buena relación
            comercial con Grupo CrediPalmo.</p>

        <p>Asimismo, le indicamos que de hacer caso omiso a la presente comunicación, el expediente de crédito será
            derivado al área legal, donde nuestro abogado iniciará las acciones judiciales que correspondan ante el
            órgano jurisdiccional competente o, en todo caso, impulsará cualquier acción legal que ya se haya trabajado
            en su contra.</p>

        <p>En caso tuviera alguna duda o consulta sobre la comunicación, puede comunicarse con su asesor de negocios al
            celular: {{ $prestamo->user->telefono }} o también Para mayor información, puede acercarse a nuestra
            oficina ubicada en la calle Jr. Tahuantinsuyo N° 164 - Tarapoto.</p>
    </div>
</body>

</html>
