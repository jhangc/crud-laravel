<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Cliente</title>

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

        .content {
            margin: 20px;
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
        <h1>Detalle del Cliente</h1>
        
        <p><b>Nombre del Cliente:</b> {{ $cliente->nombre }}</p>
        <p><b>Documento de Identidad:</b> {{ $cliente->documento_identidad }}</p>
        <p><b>Teléfono:</b> {{ $cliente->telefono }}</p>
        <p><b>Correo Electrónico:</b> {{ $cliente->email }}</p>
        <p><b>Actividad Económica:</b> {{ $cliente->actividad_economica }}</p>
        <p><b>Dirección:</b> {{ $cliente->direccion }}</p>
        <p><b>Dirección Laboral:</b> {{ $cliente->direccion_laboral }}</p>
        <p><b>Referencia:</b> {{ $cliente->referencia }}</p>
        <p><b>Lugar de Nacimiento:</b> {{ $cliente->lugar_nacimiento }}</p>
        <p><b>Fecha de Nacimiento:</b> {{ $cliente->fecha_nacimiento }}</p>
        <p><b>Sexo:</b> {{ $cliente->sexo }}</p>
        <p><b>Profesión:</b> {{ $cliente->profesion }}</p>
        <p><b>Estado Civil:</b> {{ $cliente->estado_civil }}</p>
        
        @if ($cliente->estado_civil == 'Casado' || $cliente->estado_civil == 'Conviviente')
            <h2>Información del Cónyuge</h2>
            <p><b>Cónyuge:</b> {{ $cliente->conyugue }}</p>
            <p><b>DNI del Cónyuge:</b> {{ $cliente->dni_conyugue }}</p>
            <p><b>Dirección del Cónyuge:</b> {{ $cliente->direccion_conyugue }}</p>
        @endif

        <h2>Información del Aval</h2>
        <p><b>Nombre del Aval:</b> {{ $cliente->aval }}</p>
        <p><b>Número de DNI del Aval:</b> {{ $cliente->numero_dni_aval }}</p>
        <p><b>Dirección del Aval:</b> {{ $cliente->direccion_aval }}</p>
    </div>

    <div class="page-break"></div>

    <div class="content">
        <h2>Foto</h2>
        @if ($cliente->foto)
            <img src="{{url('storage/foto/'.$cliente->id) }}" alt="Foto del cliente" style="width:100%;">
        @else
            <p>No se ha adjuntado una foto.</p>
        @endif
    </div>

    

</body>

</html>
