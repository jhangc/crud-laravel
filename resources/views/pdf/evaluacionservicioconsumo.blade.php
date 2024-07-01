<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EVALUACION SERVICIO CONSUMO</title>

    <style>
        /* Ajusta el tamaño de la fuente para todo el documento */
        body {
            font-size: 10px;
            /* Puedes ajustar este tamaño según lo necesites */
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-size: 12px;
            /* Ajusta el tamaño de los encabezados */
            margin: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;

        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
        }

        .titulo {
            font-size: 14px;
            margin-bottom: 20px;
            text-align: center;
        }

        .contenido {
            page-break-inside: avoid;
            /* Evita que la tabla se divida entre dos páginas */
        }
    </style>
</head>

<body>
    <div class="row evaluacion">
        <h3 class="titulo">EVALUACION FINANCIERA</h3>
        <h6><b><span>TIPO DE CREDITO:</span></b> {{ $prestamo->tipo }}</h6>
        <h6><b>PRODUCTO:</b> {{ $prestamo->producto }}</h6>
        <h6><b>DESTINO:</b> {{ $prestamo->destino }}</h6>
        <h6><b>CLIENTES:</b>
            @foreach ($prestamo->clientes as $cliente)
            {{ $cliente->nombre }}@if (!$loop->last)
            ,
            @endif
            @endforeach
        </h6>
        <h6><b>ACTIVIDAD:</b> {{ $prestamo->descripcion_negocio }}</h6>
        <h6><b>RESPONSABLE:</b> {{ $responsable->name }}</h6>
        <h6><b>TOTAL PRESTAMO:</b> S/.{{ $totalprestamo }}</h6>
        <h6><b>CUOTA A EVALUAR:</b> S/.{{ $cuotaprestamo }}</h6>


    </div>


    <div class="row">


        <div class="col-md-6 contenido">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Saldo Total negocio</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Monto en S/.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Ingresos</td>
                                <td>{{ number_format($totalVentas,2) }}</td>
                            </tr>
                            <tr>
                                <td>Egresos familiares</td>
                                <td>{{ number_format($totalCompras,2) }}</td>
                            </tr>
                            {{-- <tr>
                                <td>Utilidad en soles</td>
                                <td>{{ number_format($margensoles,2) }}</td>
                            </tr> --}}
                            {{-- <tr>
                                <td>Utilidad (%)</td>
                                <td>{{$margenporcentaje }}</td>
                            </tr> --}}
                            <tr>
                                <td>Gastos financieros</td>
                                <td>{{ number_format($totalcuotadeuda,2) }}</td>
                            </tr>
                            {{-- <tr>
                                <td>Saldo disponible del negocio</td>
                                <td>{{ number_format($saldo_disponible_negocio,2) }}</td>
                            </tr> --}}
                            {{-- <tr>
                                <td>Gastos familiares</td>
                                <td>{{ number_format($totalgastosfamiliares, 2) }}</td>
                            </tr> --}}
                            <tr>
                                <td>Saldo final disponible</td>
                                <td class="{{ $saldo_final <= $cuotaprestamo ? 'text-danger' : '' }}">{{ number_format($saldo_final, 2) }}</td>

                            </tr>


                        </tbody>
                    </table>
                </div>
            </div>
        </div>



        <div class="col-md-6 contenido">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Ratios Financieros</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Valor</th>
                                <th>resultado esperado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Total Garantia (S/.)</td>
                                <td class="{{ $totalgarantia < $totalprestamo ? 'text-danger' : '' }}">{{ number_format($totalgarantia, 2) }}</td>
                                <td>tiene que ser mayor o igual al total de crédito</td>
                            </tr>
                            <tr>
                                <td>Total Saldo de prestamos (S/.)</td>
                                <td>{{ number_format($totalgastosfinancieros, 2) }}</td>
                            </tr>
                            <tr>
                                {{-- PASIVO TOTAL / PATRIMONIO NETO --}}
                                <td>Solvencia</td>
                                <td class="{{ $solvencia > 1 ? 'text-danger' : '' }}">{{ $solvencia }}</td>
                                <td>tiene que ser (<=1)</td>
                            </tr>
                            <tr>
                                <td>Cuota de endeudamiento</td>
                                <td class="{{ $saldo_final <= $cuotaprestamo ? 'text-danger' : '' }}">{{ number_format($saldo_final, 2) }}</td>
                                <td>tiene que ser mayor a la cuota del crédito</td>
                            </tr>
                            <tr>
                                {{-- Cuota de préstamo / saldo final --}}
                                <td>cuotaexcedente</td>
                                <td class="{{ $cuotaexcedente >= 1 ? 'text-danger' : '' }}">{{ $cuotaexcedente }}</td>
                                <td>tiene que ser <1 </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</body>

</html>