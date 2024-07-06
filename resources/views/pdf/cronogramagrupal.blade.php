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
    <table style="border: none !important; width: 100%; margin-bottom:30px">
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
        </tr>
    </table>

    <br><br>

    <!-- Cronograma Grupal -->
    <h4 class="card-title" style="text-align: center; margin: 0;">Cronograma Grupal</h4>
    <table class="principal contenido">
        <thead>
            <tr>
                <th>N° CUOTA</th>
                <th>Fecha de Vencimiento</th>
                <th>N° Días</th>
                <th>Detalle</th>
                <th>Capital</th>
                <th>Interes</th>
                <th>Deuda</th>
                <th>Amortización</th>
                <th>Total Soles</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cuotas as $cuota)
                @if (is_null($cuota->cliente_id))
                    <tr>
                        <td>{{ $cuota->numero }}</td>
                        <td>{{ $cuota->fecha }}</td>
                        <td>{{ $cuota->dias }}</td>
                        <td>{{ $cuota->detalle }}</td>
                        <td>{{ number_format($cuota->capital, 2) }}</td>
                        <td>{{ number_format($cuota->interes, 2) }}</td>
                        <td>{{ number_format($cuota->deuda, 2) }}</td>
                        <td>{{ number_format($cuota->amortizacion, 2) }}</td>
                        <td>{{ number_format($cuota->total, 2) }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <br><br>

    <!-- Cronograma Individual -->
    @foreach ($prestamo->clientes as $cliente)
        <h4 class="card-title" style="text-align: center; margin: 0;">Cronograma individual de: {{ $cliente->nombre }}</h4>
        <table class="principal contenido">
            <thead>
                <tr>
                    <th>N° CUOTA</th>
                    <th>Fecha de Vencimiento</th>
                    <th>N° Días</th>
                    <th>Detalle</th>
                    <th>Capital</th>
                    <th>Interes</th>
                    <th>Deuda</th>
                    <th>Amortización</th>
                    <th>Total Soles</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cuotas as $cuota)
                    @if ($cuota->cliente_id == $cliente->id)
                        <tr>
                            <td>{{ $cuota->numero }}</td>
                            <td>{{ $cuota->fecha }}</td>
                            <td>{{ $cuota->dias }}</td>
                            <td>{{ $cuota->detalle }}</td>
                            <td>{{ number_format($cuota->capital, 2) }}</td>
                            <td>{{ number_format($cuota->interes, 2) }}</td>
                            <td>{{ number_format($cuota->deuda, 2) }}</td>
                            <td>{{ number_format($cuota->amortizacion, 2) }}</td>
                            <td>{{ number_format($cuota->total, 2) }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        <br><br>
    @endforeach

    <p><strong>Funcionario:</strong> {{ $responsable->name }}</p>
</body>

</html>
