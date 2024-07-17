<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cronograma de Créditos</title>
    <style>
        body {
            margin: 0.2cm 0.4cm;
            /* Ajusta los márgenes del documento a 3 cm */
        }

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

        .individual {
            page-break-before: always;
            /* Fuerza un salto de página antes de cada tabla individual */
        }
    </style>
</head>

<body>

    <h2>CRONOGRAMA DE CRÉDITOS</h2>
    <table style="border: none !important; width: 100%; margin-bottom:30px">
        <tr>
            <td><b>Agencia:</b></td>
            <td>Tarapoto</td>
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
            <td><b>Desembolso (S/.):</b></td>
            <td>{{ number_format(round($prestamo->monto_total, 2), 2, '.', ',') }}</td>

        </tr>

        <tr>
            <td><b>Fecha de Desembolso:</b></td>
            <td>{{ $prestamo->fecha_desembolso }}</td>
            <td><b>Tasa (%):</b></td>
            <td>{{ $prestamo->tasa }}</td>
            <td><b>Periodo:</b></td>
            <td>{{ $prestamo->recurrencia }}</td>
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
                <th>Detalle</th>
                <th>Capital</th>
                <th>Interes</th>
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
                        <td>{{ $cuota->detalle }}</td>
                        <td>{{ number_format($cuota->capital, 2) }}</td>
                        <td>{{ number_format($cuota->interes, 2) }}</td>
                        <td>{{ number_format($cuota->amortizacion, 2) }}</td>
                        <td>{{ number_format($cuota->total, 2) }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"></td>
                <td style="text-align: right;"><b>Total</b></td>
                <td>{{ number_format($totalInteresGrupal, 2) }}</td>
                <td>{{ number_format($totalAmortizacionGrupal, 0) }}</td>
                <td>{{ number_format($totalMontoGrupal, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- 
    <br>
    <p><strong>Asesor de crédito:</strong> {{ $responsable->name }}</p><br> --}}

    <!-- Sección de firma del asesor de crédito -->
    <div style="width: 100%; margin-top: 50px; text-align: center;">
        <div style="display: inline-block; text-align: center;">
            <p>Auxiliar de Operaciones </p>
            <p>{{ Auth::user()->name }}</p>
            <p>{{ $responsable->name }}</p>
        </div>
    </div>

    <!-- Cronograma Individual -->
    @foreach ($prestamo->clientes as $cliente)
        <h4 class="card-title individual" style="text-align: center; margin: 20px 20px 40px 20px;">Cronograma individual
            de: {{ $cliente->nombre }}</h4>
        <table style="border: none !important; width: 100%; margin-bottom:30px">
            <tr>
                <td><b>Agencia:</b></td>
                <td>Tarapoto</td>
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
                <td><b>Desembolso (S/.):</b></td>
                @foreach ($credito_cliente as $credito)
                    @if ($credito->cliente_id == $cliente->id)
                        <td>{{ number_format(round($credito->monto_indivual, 2), 2, '.', ',') }}</td>
                    @endif
                @endforeach


            </tr>

            <tr>
                <td><b>Fecha de Desembolso:</b></td>
                <td>{{ $prestamo->fecha_desembolso }}</td>
                <td><b>Tasa (%):</b></td>
                <td>{{ $prestamo->tasa }}</td>
                <td><b>Periodo:</b></td>
                <td>{{ $prestamo->recurrencia }}</td>
            </tr>
        </table>

        <table class="principal contenido">
            <thead>
                <tr>
                    <th>N° CUOTA</th>
                    <th>Fecha de Vencimiento</th>

                    <th>Detalle</th>
                    <th>Capital</th>
                    <th>Interes</th>

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
                            <td>{{ $cuota->detalle }}</td>
                            <td>{{ number_format($cuota->capital, 2) }}</td>
                            <td>{{ number_format($cuota->interes, 2) }}</td>
                            <td>{{ number_format($cuota->amortizacion, 2) }}</td>
                            <td>{{ number_format($cuota->total, 2) }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                    <td style="text-align: right;"><b>Total</b></td>
                    <td>{{ number_format($totalInteresesIndividuales[$cliente->id], 2) }}</td>
                    <td>{{ number_format($totalAmortizacionIndividuales[$cliente->id], 0) }}</td>
                    <td>{{ number_format($totalMontoIndividuales[$cliente->id], 2) }}</td>
                </tr>
            </tfoot>
        </table>
        {{-- <br> --}}
        {{-- <p><strong>Asesor de crédito:</strong> {{ $responsable->name }}</p> --}}


        <!-- Sección de firmas -->
        <div style="width: 100%; margin-top: 50px; text-align: center;">
            <div style="width: 45%; float: left; text-align: center;">
                <p>__________________________</p>
                <p>Auxiliar de Operaciones </p>
                <p>{{ Auth::user()->name }}</p>
            </div>
            <div style="width: 45%; float: right; text-align: center;">
                <p>__________________________</p>
                <p>Cliente</p>
                <p>{{ $cliente->nombre }}</p>
            </div>
        </div>
        <div style="clear: both;"></div>
    @endforeach


</body>

</html>
