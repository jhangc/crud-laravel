<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cronograma de Créditos</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        .principal th,
        .principal td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h1,
        h2 {
            text-align: center;
            line-height: 0.5;
        }

        .contenido {
            page-break-inside: avoid;
            /* Evita que la tabla se divida entre dos páginas */
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <h2>CRONOGRAMA DE CRÉDITOS</h2>
    <table style="border: none !important; width: 100%; margin-buttom:30px">
        <tr>
            <td><b>Agencia:</b></td>
            <td>CHICLAYO</td>
            <td><b>Tipo de Crédito:</b></td>
            <td>{{ $prestamo->tipo }}</td>
            <td><b>Producto:</b></td>
            <td>{{ $prestamo->producto }}</td>
        </tr>
        <tr>
            <td><b>Nombre del grupo:</b></td>
            <td>{{ $prestamo->nombre_prestamo }}</td>
            <td><b>Moneda:</b></td>
            <td>Soles</td>
            <td><b>Desembolso:</b></td>
            <td>{{ number_format(round($prestamo->monto_total, 2), 2, '.', ',') }}</td>

        </tr>

        <tr>
            <td><b>Fecha de Desembolso:</b></td>
            <td>{{ $prestamo->fecha_desembolso }}</td>
            <td><b>Tasa (%):</b></td>
            <td>{{ $prestamo->tasa }}</td>
            {{-- <td>Cuotas:</td>
            <td>{{ $prestamo->fecha_desembolso }}</td> --}}
        </tr>
    </table>

    <br><br>
    @foreach ($prestamo->clientes as $cliente)
        <table class="principal contenido">
            <thead>
                <tr>
                    <th colspan="8" style="text-align: center;">
                        <h4 class="card-title" style="margin: 0;">Cronograma individual de: {{ $cliente->nombre }}</h4>
                    </th>
                </tr>
                <tr>
                    <th>N°</th>
                    <th>Vencimiento</th>
                    <th>Pagado</th>
                    <th>Atraso</th>
                    {{-- <th>Capital</th>
                    <th>Intereses</th> --}}
                    <th>Mora</th>
                    <th>Mto Cuota</th>
                    <th>Saldo Capital</th>
                    <th>Situación</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cuotas as $cuota)
                    @if ($cuota->cliente_id == $cliente->id)
                        <tr>
                            <td>{{ $cuota->numero }}</td>
                            <td>{{ $cuota->fecha }}</td>
                            <td>-</td>
                            <td>0</td>
                            {{-- <td></td>
                            <td>-</td> --}}
                            <td>0.00</td>
                            <td>{{ $cuota->monto }}</td>

                            <td>
                                @foreach ($credito_cliente as $creditocliente)
                                    @if ($creditocliente->cliente_id == $cliente->id)
                                        {{ $creditocliente->monto_indivual }}
                                    @endif
                                @endforeach
                                    
                            </td>
                            <td>pendiente</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @endforeach

    <p><strong>Funcionario:</strong> {{ $responsable->name }}</p>
</body>

</html>
