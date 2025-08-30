<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Hoja de Devolución de Joyas</title>
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
            font-size: 18px;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .content {
            margin: 0 25px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 12px;
        }

        th, td {
            border: 1px solid #bbb;
            padding: 6px;
            text-align: center;
        }

        th {
            background: #f1f1f1;
        }

        .acta {
            margin-top: 18px;
            text-align: justify;
        }
        .acta .tit {
            text-align: center;
            font-weight: bold;
            margin-bottom: 8px;
        }

        /* Firmas tipo contrato */
        .signature {
            margin-top: 50px;
            width: 100%;
        }
        .signature td {
            border: none;
            text-align: center;
            width: 50%;
            vertical-align: bottom;
            font-size: 12px;
        }
        .linea {
            width: 60%;
            margin: 0 auto 6px;
            border-top: 1px solid #000;
        }

       
    </style>
</head>
<body>

    {{-- Encabezado fijo --}}
    <header class="header">
        <h3>Grupo Credipalmo</h3>
    </header>

  <div class="content">
        {{-- Título --}}
        <h1>HOJA DE DEVOLUCIÓN DE JOYAS</h1>

        {{-- Datos básicos --}}
        <div class="row">
            <div><b>Cliente:</b> {{ $clienteNombre }}</div>
            <div><b>Fecha emisión:</b> {{ $emitido->format('Y-m-d H:i') }}</div>
        </div>

        {{-- Tabla joyas --}}
        <h3>Joyas</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>K</th>
                    <th>Peso Neto</th>
                    <th>Val. Tasación</th>
                    <th>Estado</th>
                    <th>Fecha Devolución</th>
                </tr>
            </thead>
            <tbody>
                @forelse($joyas as $i => $j)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $j->codigo }}</td>
                    <td style="text-align:left">{{ $j->descripcion }}</td>
                    <td>{{ $j->kilate }}</td>
                    <td>{{ $j->peso_neto }}</td>
                    <td>S/ {{ number_format($j->valor_tasacion,2,'.','') }}</td>
                    <td>
                        @if($j->devuelta == 0) En custodia
                        @elseif($j->devuelta == 1) Pendiente
                        @elseif($j->devuelta == 2) Devuelta
                        @endif
                    </td>
                    <td>
                        @if($j->devuelta == 2)
                            @if($j->fecha_devolucion)
                                {{ \Carbon\Carbon::parse($j->fecha_devolucion)->format('Y-m-d H:i') }}
                            @endif
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8">No hay joyas registradas.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- Tabla custodia --}}
        @if($custodias->count())
        <h3>Pagos de custodia</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($custodias as $k=>$c)
                <tr>
                    <td>{{ $k+1 }}</td>
                    <td>{{ optional($c->created_at)->format('Y-m-d H:i') }}</td>
                    <td>S/ {{ number_format($c->monto,2,'.','') }}</td>
                    <td style="text-align:left">{{ $c->observaciones }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- Acta --}}
        <div class="acta">
             <div class="tit">ACTA DE ENTREGA Y RECEPCIÓN</div>
    <p>
        En la fecha indicada, <b>Grupo Credipalmo</b>, en su calidad de <b>EL MUTUANTE</b>, hace entrega al(la) cliente
        <b>{{ $clienteNombre }}</b>, identificado(a) con DNI N° <b>{{ $clienteDni ?? '---' }}</b>, en calidad de
        <b>EL MUTUARIO</b>, de las joyas listadas en la presente hoja de devolución.
    </p>
    <p>
        EL MUTUARIO declara haber verificado el estado de las piezas recibidas y exonera a EL MUTUANTE
        de toda responsabilidad posterior sobre su custodia a partir de la fecha de devolución registrada.
    </p>
</div>

        {{-- Firmas --}}
        <table class="signature">
            <tr>
                <td>
                    <div class="linea"></div>
                    EL MUTUANTE<br>
                    GRUPO CREDIPALMO<br>
                    RUC N° 20610069542
                </td>
                <td>
                    <div class="linea"></div>
                    LA MUTUATARIA<br>
                    {{ $clienteNombre }}<br>
                    DNI N° {{ $clienteDni ?? '---' }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>