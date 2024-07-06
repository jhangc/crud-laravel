<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartilla de Identificación</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        h1 {
            text-align: center;
            color: #333;
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
    <h1>CARTILLA DE IDENTIFICACIÓN</h1>
    <p><b>Denominación de EL GRUPO:</b> {{ $prestamo->nombre_prestamo }}<br>
        <b>FECHA:</b>{{ $formattedDate }}

        
        <br><br>
        <b>PRIMERO:</b> EL GRUPO enterado del contenido y alcance jurídico de las obligaciones que contraen con la
        celebración de este Contrato, suscriben manifestando que tienen conocimiento y comprende plenamente los términos
        y
        condiciones pactadas, habiendo sido absueltas y aclaradas a satisfacción sus consultas y/o dudas, por lo que
        firman
        de conformidad en el lugar y fecha que se menciona en el presente documento. Asimismo, declaran haber recibido
        el
        contrato de Préstamo Grupal aprobado mediante resolución SBS N°02912-2022, la Hoja Resumen y el Cronograma de
        Pagos.<br><br>
        <b>SEGUNDO:</b> Los integrantes de EL GRUPO conforme a lo establecido en la Ley N° 29733, Ley de Protección
        de Datos Personales y su Reglamento, autorizaran a la empresa GRUPO CREDIPALMO SAC marcando con un &apos;X&apos;
        las
        opciones mencionadas en el cuadro, para la utilización de la información proporcionada a efectos de: (i)
        Contactarlo
        a través de cualquier mecanismo (telefónico mensaje de texto, correo electrónico, directamente u otros) para
        ofrecerle cualquiera de sus productos o servicios que brinda GRUPO CREDIPALMO SAC, incluyendo la medición y
        monitoreo de la calidad del contacto y/o encuesta. (ii) La permanente evaluación de la calidad crediticia y
        capacidad de pago de EL GRUPO. (iii) Transferir la información que sea necesaria a sus socios comerciales o
        integrantes de su grupo económico, a efectos que se puedan ofrecer los productos señalados en los puntos
        anteriores.
        Esta transferencia de la información, incluye todos los datos, operaciones o referencias de EL GRUPO, que la
        Empresa
        GRUPO CREDIPALMO SAC pudiera acceder en el curso normal de sus operaciones, ya sea por haber sido proporcionada
        directamente por EL GRUPO o por terceros, en forma física, oral o electrónica. Asimismo, los integrantes de EL
        GRUPO
        quedan informadas de la posibilidad de ejercer sus derechos de oposición, cancelación, supresión, rectificación,
        inclusión y revocación del consentimiento para el tratamiento de sus datos personales. <br>Los integrantes del
        GRUPO
        manifiestan en forma de declaración jurada que los datos que a continuación se llenaran son verdaderos: haber
        sido
        instruidos sobre lo estipulado en el mismo.
    </p><br><br><br><br><br><br>
    <table style="border-collapse: collapse; width: 100%;">
        <tr>
            <td style="border: none; text-align: center;">
                <div style="width: 50%; margin: 0 auto;">
                    <hr>
                </div>
                Grupo CrediPalmo<br>
                Gerente/representante
            </td>
            <td style="border: none; text-align: center;">
                <div style="width: 50%; margin: 0 auto;">
                    <hr>
                </div>
                Grupo CrediPalmo<br>
                Representante
            </td>
        </tr>
    </table>
    <br><br>

    <style>
        .page-break {
            page-break-before: always;
        }
    </style>
    <div class="page-break"></div>
    @foreach ($prestamo->clientes as $cliente)
    <table style="border-collapse: collapse; width: 100%;" border="1" >
        <tr>
            <td colspan="2" style="text-align: center;"><b>Datos</b></td>
            <td  style="text-align: center; width:30%;"><b>Firma</b></td>
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
    


</body>

</html>
