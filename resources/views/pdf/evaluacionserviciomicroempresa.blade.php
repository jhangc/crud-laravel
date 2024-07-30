<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EVALUACION SERVICIO MICROEMPRESA</title>

    <!-- Theme style -->
    <!-- <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}"> -->
    <!-- <link rel="stylesheet" href="{{ asset('dist/css/style.css') }}"> -->
    <!-- <link rel="icon" href="{{ asset('dist/img/fdfds.ico') }}" type="image/x-icon"> -->

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
                    <h3 class="card-title">Resumen de cuentas</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Cuenta</th>
                                <th>Monto en S/.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                {{-- activo corriente mas fijo  --}}
                                <td><b>Activo</b></td>
                                <td>{{ $totalactivo }}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 20px;">Activo corriente (circulante)</td>
                                <td> {{ $activo_corriente}}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 40px;">Disponible (caja y bancos)</td>
                                <td>{{ $saldo_en_caja_bancos}}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 40px;">Cuentas por cobrar</td>
                                <td>{{ $cuenta_cobrar}}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 40px;">Adelanto a proveedores</td>
                                <td>{{ $adelanto_proveedores}}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 40px;">Inventario</td>
                                <td>{{$totalinventario}}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 20px;">Activo fijo</td>
                                <td>{{ $activofijo }}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 40px;">Garantia</td>
                                <td>{{ $totalgarantia }}</td>
                            </tr>
                            <tr>
                                <td><b>Pasivo</b></td>
                                <td>{{ $pasivo }}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 20px;">Deudas Financieras</td>
                                <td>{{ $totaldeudas }}</td>
                            </tr>

                            <tr>
                                <td><b>Patrimonio neto</b></td>
                                <td>{{ $patrimonioneto }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

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
                                <td>Ingresos ventas</td>
                                <td>{{ number_format($totalVentas,2) }}</td>
                            </tr>
                            <tr>
                                <td>Costo de ventas</td>
                                <td>{{ number_format($totalCompras,2) }}</td>
                            </tr>
                            <tr>
                                <td>Utilidad</td>
                                <td>{{ number_format($margensoles,2) }}</td>
                            </tr>
                            <tr>
                                <td>Gastos financieros</td>
                                <td>{{ number_format($totalcuotadeuda,2) }}</td>
                            </tr>
                            <tr>
                                <td>Saldo disponible del negocio</td>
                                <td>{{ number_format($saldo_disponible_negocio,2) }}</td>
                            </tr>
                            <tr>
                                <td>Gastos familiares</td>
                                <td>{{ number_format($totalgastosfamiliares, 2) }}</td>
                            </tr>
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
                                {{-- colocar dle cuadro del excel  --}}
                                <td>Rentabilidad del negocio (%)</td>
                                <td>{{$margenventas}}%</td>
                                <td>Es el margen generico segun actividad</td>
                            </tr>
                            <tr>
                                {{-- División entre saldo total disponible y ventas total --}}
                                <td>Rentabilidad de las ventas (%)</td>
                                <td class="{{ abs($rentabilidad_ventas - $margenventas) > 5 ? 'text-danger' : '' }}">{{ $rentabilidad_ventas }}%</td>
                                <td>tiene que ser +- 5 de la rentabilidad</td>
                            </tr>
                            <tr>
                                {{-- Activo corriente / pasivo corriente --}}
                                <td>Liquidez</td>
                                <td class="{{ $liquidez <= 1 ? 'text-danger' : '' }}">{{ $liquidez }}</td>
                                <td>tiene que ser (>1)</td>
                            </tr>
                            <tr>
                                {{-- Utilidad neta / activos totales --}}
                                <td>ROA (%)</td>
                                <td class="{{ $roa <= 5 ? 'text-danger' : '' }}">{{ $roa }}%</td>
                                <td>tiene que ser (>5%)</td>
                            </tr>
                            <tr>
                                {{-- Activo corriente - pasivo corriente --}}
                                <td>Capital de trabajo (S/.)</td>
                                <td class="{{ $capital_trabajo <= $totalprestamo ? 'text-danger' : '' }}">{{ $capital_trabajo }}</td>
                                <td>tiene que ser mayor al préstamo</td>
                            </tr>
                            <tr>
                                {{-- Utilidad neta / patrimonio neto --}}
                                <td>ROE (%)</td>
                                <td class="{{ $roe <= 10 ? 'text-danger' : '' }}">{{ $roe }}%</td>
                                <td>tiene que ser (>10%)</td>
                            </tr>
                            <tr>
                                {{-- Pasivo total / patrimonio neto --}}
                                <td>Solvencia</td>
                                <td class="{{ $solvencia > 1 ? 'text-danger' : '' }}">{{ $solvencia }}</td>
                                <td>tiene que ser menor o igual a 1</td>
                            </tr>
                            <tr>
                                {{-- Pasivo total / activo total --}}
                                <td>Índice de endeudamiento</td>
                                <td class="{{ $indice_endeudamiento > 40 ? 'text-danger' : '' }}">{{ $indice_endeudamiento }}%</td>
                                <td>tiene que ser menor o igual a 40% </td>
                            </tr>
                            <tr>
                                {{-- Cuota de préstamo / saldo final --}}
                                <td>Cuota excedente</td>
                                <td class="{{ $cuotaexcedente >= 1 ? 'text-danger' : '' }}">{{ $cuotaexcedente }}</td>
                                <td>tiene que ser menor a 1</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</body>

</html>