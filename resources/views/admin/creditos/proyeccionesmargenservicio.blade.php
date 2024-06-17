@extends('layouts.admin')

@section('content')
    <div class="row evaluacion">
        <h3 class="titulo">EVALUACION FINANCIERA</h3>
        <h6><b>TIPO DE CREDITO:</b> {{ $prestamo->tipo }}</h6>
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
    </div>

    {{-- <div class="row" id="proyeccionventas">
    <div class="col-md-12">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">Proyecciones de Ventas según inventario y producción</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Descripción</th>
                            <th>Venta mensual</th>
                            <th>Costo mensual</th>
                            <th>Utilidad (S/.)</th>
                            <th>Utilidad (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($proyecciones as $proyeccion)
                        <tr>
                            <td>{{ $proyeccion->descripcion_producto }}</td>
                            <td>{{ $proyeccion->unidades_vendidas * $proyeccion->precio_venta }}</td>
                            <td>{{ $proyeccion->unidades_compradas * $proyeccion->precio_compra }}</td>
                            <td>{{ $proyeccion->unidades_vendidas * $proyeccion->precio_venta - $proyeccion->unidades_compradas * $proyeccion->precio_compra }}</td>
                            <td>{{ ($proyeccion->precio_compra != 0) ? (($proyeccion->precio_venta - $proyeccion->precio_compra) / $proyeccion->precio_compra * 100) : 0 }}%</td>
                        </tr>
                        @endforeach

                        <tr class="finaltotal">
                            <td colspan="2"></td>
                            <td class="text-right"><b>Total</b></td>
                            <td>{{ $totalVentas - $totalCompras }}</td>
                            <td>
                                @if ($totalCompras != 0)
                                    {{ (($totalVentas - $totalCompras) / $totalCompras) * 100 }}%
                                @else
                                    0%
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> --}}

    {{-- <div class="row" id="proyeccionboletas">
    <div class="col-md-12">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">Proyección de ingresos por boletas</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre de boleta</th>
                            <th>Monto</th>
                            <th>Descuento</th>
                            <th>Total(S/.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($boletas as $boleta)
                        <tr>
                            <td>{{ $boleta->numero_boleta }}</td>
                            <td>{{ $boleta->monto_boleta }}</td>
                            <td>{{ $boleta->descuento_boleta }}</td>
                            <td>{{ $boleta->total_boleta }}</td>
                        </tr>
                        @endforeach

                        <tr class="finaltotal">
                            <td colspan="2"></td>
                            <td class="text-right"><b>Total</b></td>
                            <td>{{ $boletas->sum('total_boleta') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row" id="proyecciongastosproducir">
    <div class="col-md-12">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">Proyección de inversión y gastos a producir</h3>
            </div>
            <div class="card-body">
                <h5 class="card-title mb-3"><b>Total a producir:</b> </h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre de Insumo</th>
                            <th>cantidad</th>
                            <th>Precio unitario</th>
                            <th>Total (S/.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gastosProducir as $gasto)
                        <tr>
                            <td>{{ $gasto->descripcion_gasto }}</td>
                            <td>{{ $gasto->cantidad }}</td>
                            <td>{{ $gasto->precio_unitario }}</td>
                            <td>{{ $gasto->total_gasto }}</td>
                        </tr>
                        @endforeach

                        <tr class="finaltotal">
                            <td colspan="2"></td>
                            <td class="text-right"><b>Total</b></td>
                            <td>{{ $gastosProducir->sum('total_gasto') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> --}}

    <div class="row">
        <div class="col-md-6">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Resumen del negocio</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Indicador</th>
                                <th>Resultado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <!-- suma de total ventas al mes -->
                                <td>Total de Ventas</td>
                                <td>{{ $totalVentas }}</td>
                            </tr>
                            <tr>
                                <!-- suma de total compras al mes -->
                                <td>Costo de Ventas</td>
                                <td>{{ $totalCompras }}</td>
                            </tr>
                            <tr>
                                <!-- (Ventas - costo)/ventas -->
                                <td>Margen</td>
                                <td>
                                    {{ $margen }}
                                </td>
                            </tr>
                            <tr>
                                <!-- suma de todo el % participacion -->
                                <td>Participación</td>
                                <td>{{ $proporcion_ventas }}%</td>
                            </tr>
                            <tr>
                                <!-- Ventas - costo -->
                                <td>Utilidad</td>
                                <td>{{ $utilidadBruta }}</td>
                            </tr>
                            <tr>
                                <!-- total gastos operativos -->
                                <td>Gastos operativos</td>
                                <td>{{ $totalGastosOperativos }}</td>
                            </tr>
                            <tr>
                                <!-- Total sventa al credito -->
                                <td>Venta al credito</td>
                                <td>{{ $total_venta_credito }}</td>
                            </tr>
                            <tr>
                                <!-- Total de inventario -->
                                <td>Productos terminados</td>
                                <td>0</td>
                            </tr>
                            {{-- estos dos ultimos colocar 0.00 por el momento --}}
                            <tr>
                                <!-- Total de inventario en proceso -->
                                <td>Productos en proceso</td>
                                <td>0.0</td>
                            </tr>
                            <tr>
                                <!-- Total de materiales -->
                                <td>Materiales</td>
                                <td>0.0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
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
                                {{-- activo corriente mas fijo --}}
                                <td><b>Activo</b></td>
                                <td>{{ $activo}}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 20px;">Activo corriente (circulante)</td>
                                <td> {{ $activo_corriente}}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 40px;">Disponible (caja y bancos)</td>
                                <td>{{ $activos->saldo_en_caja_bancos}}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 40px;">Cuentas por cobrar</td>
                                <td>{{ $activos->cuentas_por_cobrar}}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 40px;">Adelanto a proveedores</td>
                                <td>{{ $activos->adelanto_a_proveedores}}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 40px;">Inventario</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 50px;">Productos terminados</td>
                                <td>0</td>
                            </tr>
                            {{-- <tr>
                            <td style="padding-left: 50px;">Productos terminados</td>
                            <td></td>
                        </tr> --}}
                            <tr>
                                <td style="padding-left: 50px;">En proceso productivo</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 50px;">Materiales</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 20px;">Activo fijo (circulante)</td>
                                <td>{{ $garantias->sum('valor_mercado') }}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 40px;">Garantia</td>
                                <td>{{ $garantias->sum('valor_mercado') }}</td>
                            </tr>
                            <tr>
                                <td><b>Pasivo</b></td>
                                <td>{{ $deudas->sum('saldo_capital') }}</td>
                            </tr>
                            <tr>
                                <td style="padding-left: 20px;">Deudas Financieras</td>
                                <td>{{ $deudas->sum('saldo_capital') }}</td>
                            </tr>
                            <tr>
                                <td><b>Patrimonio neto</b></td>
                                <td>{{ $patrimonio }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Gastos familiares</h3>
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
                            @php
                                $totalGastosFamiliares = 0;
                            @endphp
                            @foreach ($gastosfamiliares as $gasto)
                            @php
                                $subtotal = $gasto->precio_unitario * $gasto->cantidad;
                                $totalGastosFamiliares += $subtotal;
                            @endphp
                            <tr>
                                <td>{{ $gasto->descripcion }}</td>
                                <td>{{ number_format($subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><b><i>Total</i></b></td>
                                <td>{{ number_format($totalGastosFamiliares, 2) }}</td>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
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
                                <td>{{ $totalVentas }}</td>
                            </tr>
                            <tr>
                                <td>Costo de ventas</td>
                                <td>{{ $totalCompras }}</td>
                            </tr>
                            <tr>
                                <td>Utilidad</td>
                                <td>{{ $totalVentas - $totalCompras }}</td>
                            </tr>
                            <tr>
                                <td>Gastos operativos</td>
                                <td>{{ $totalGastosOperativos }}</td>
                            </tr>
                            <tr>
                                <td>Utilidad operativa</td>
                                <td>{{ $utilidadOperativa }}</td>
                            </tr>
                            <tr>
                                <td>Gastos financieros</td>
                                <td>{{ $deudas->sum('saldo_capital') }}</td>
                            </tr>
                            <tr>
                                <td>Saldo disponible del negocio</td>
                                <td>{{ $saldo_disponible_negocio }}</td>
                            </tr>
                            <tr>
                                <td>Gastos familiares</td>
                                <td>{{ number_format($totalGastosFamiliares, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Saldo final dsiponible</td>
                                <td>{{ $saldo_final }}</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>



        <div class="col-md-6">
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
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                {{-- colocar dle cuadro del excel --}}
                                <td>Rentabilidad del negocio (%)</td>
                                <td>12</td>
                            </tr>
                            <tr>
                                {{-- division entre saldo total disponible y ventas total --}}
                                <td>Rentabilidad de las ventas</td>
                                <td>{{ $rentabilidad_ventas }}</td>
                            </tr>
                            <tr>
                                {{-- total costo / total inventario --}}
                                <td>Rotación de inventario (en días)</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                {{-- activo corriente / pasivo corriente --}}
                                <td>Liquidez</td>
                                <td>{{ $liquidez}}</td>
                            </tr>
                            <tr>
                                {{-- UTILIDAD NETA / ACTIVOS TOTALES --}}
                                <td>ROA (%)</td>
                                <td>{{ $roa}}</td>
                            </tr>
                            <tr>
                                {{-- ACTIVO CORRIENTE - PASIVO CORRIENTE --}}
                                <td>Capital de trabajo (S/.)</td>
                                <td>{{ $capital_trabajo}}</td>
                            </tr>
                            <tr>
                                {{-- UTILIDAD NETA / PATRIMONIO NETO --}}
                                <td>ROE (%)</td>
                                <td>{{ $roe}}</td>
                            </tr>
                            <tr>
                                {{-- PASIVO TOTAL / PATRIMONIO NETO --}}
                                <td>Solvencia</td>
                                <td>{{ $solvencia}}</td>
                            </tr>
                            <tr>
                                {{-- PASIVO TOTAL / ACTIVO TOTAL --}}
                                <td>Indice de endeudamiento</td>
                                <td>{{ $indice_endeudamiento}}</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>




    <div class="row" style="text-align:center;">
        <div class="col-md-12 mb-5">
            <button type="button" class="btn btn-primary btnprestamo">Aprobar</button>
            <button type="button" class="btn btn-warning btnprestamo">Rechazar</button>
        </div>
    </div>
@endsection
