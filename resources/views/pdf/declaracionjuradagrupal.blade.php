<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Declaración Jurada y Fondo de Garantía</title>

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

        h2.seccion {
            font-size: 13px;
            font-weight: bold;
            margin: 16px 0 6px;
        }

        p {
            margin: 6px 0;
            font-size: 13px;
            text-align: justify;
        }

        ol.condiciones {
            font-size: 13px;
            margin: 10px 0;
            padding-left: 35px;
        }

        ol.condiciones li {
            margin-bottom: 10px;
            text-align: justify;
        }

        .content {
            margin: 0px 20px;
        }

        .firmas-bloque {
            margin-top: 30px;
            padding-top: 60px;
        }

        .firmas {
            width: 100%;
            border-collapse: collapse;
        }

        .firmas td {
            width: 50%;
            border: none;
            vertical-align: top;
            font-size: 13px;
            padding: 0 15px;
        }

        .firmas .linea {
            border-top: 1px solid #000;
            margin-bottom: 4px;
        }

        .firmas .cargo {
            text-align: center;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .firmas .dato {
            font-weight: bold;
            margin: 6px 0;
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
        <h1>DECLARACIÓN JURADA Y CONSTITUCIÓN DE FONDO DE GARANTÍA</h1>

        <p><b>Lugar y fecha:</b> Chiclayo, {{ $fechaDocumento }}</p>
        <p><b>NOMBRE DEL GRUPO:</b> <b>{{ $prestamo->nombre_prestamo }}</b></p>

        <p style="margin-top: 18px;">Presente. -</p>
        <p>De nuestra mayor consideración:</p>

        <p>Por medio del presente documento, el que suscribe <b>es la presidenta y tesorera</b> en representación del
            grupo denominado las {{ $prestamo->nombre_prestamo }}, en adelante <b>EL GRUPO</b>, declaro y manifiesto
            lo siguiente:</p>

        <h2 class="seccion">PRIMERO: Antecedentes del Contrato</h2>
        <p>Con fecha {{ $fechaContrato }}, EL GRUPO y <b>CREDIPALMO</b> (en adelante, <b>LA EMPRESA</b>) han suscrito
            el Contrato sobre un crédito grupal, conformado por integrantes mujeres .</p>

        <h2 class="seccion">Constitución del Fondo de Garantía</h2>
        <p>En cumplimiento de las condiciones establecidas en el Contrato, EL GRUPO deja constancia de la constitución
            de un <b>Fondo de Garantía</b> por la suma ascendente Al 10% del monto del crédito aprobado, el cual queda en
            poder o retenido por LA EMPRESA como respaldo y garantía de fiel, cabal y oportuno cumplimiento de todas las
            obligaciones asumidas por El GRUPO en virtud del Contrato mencionado.</p>

        <h2 class="seccion">TERCERO: Condiciones de Aplicación y Devolución</h2>
        <ol class="condiciones">
            <li><b>Aplicación del fondo de Garantia:</b> LA EMPRESA podrá hacer uso y retener de manera definitiva el
                monto del Fondo de Garantía, de forma total o parcial, únicamente en caso de que EL GRUPO incurra en
                incumplimiento de pago, penalidades, daños o cualquier otra obligación derivada del Contrato, previa
                notificación escrita detallando el incumplimiento.</li>
            <li><b>Devolución:</b> En caso de que EL GRUPO cumpla con la totalidad de las obligaciones pactadas a su
                cargo y a la conformidad de LA EMPRESA, el saldo remanente del Fondo de Garantía será devuelto a AL
                GRUPO .</li>
            <li><b>Naturaleza del fondo:</b> Este fondo no genera intereses obligatorios salvo acuerdo explícito en
                contrario entre las partes y no libera a EL CLIENTE de sus responsabilidades legales y contractuales que
                excedan el monto aquí garantizado.</li>
        </ol>

        <div class="avoid-page-break">
            <p>Sin otro particular, suscribimos el presente documento en señal de aceptación y conformidad.</p>
            <p>Atentamente,</p>

            <div class="firmas-bloque">
                <table class="firmas">
                    <tr>
                        <td>
                            <div class="linea"></div>
                            <div class="cargo">TESORERA DEL GRUPO</div>
                            <div class="dato">Nombre:</div>
                            <div class="dato">DNI</div>
                        </td>
                        <td>
                            <div class="linea"></div>
                            <div class="cargo">PRESIDENTA DEL GRUPO</div>
                            <div class="dato">Nombre:</div>
                            <div class="dato">DNI</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
