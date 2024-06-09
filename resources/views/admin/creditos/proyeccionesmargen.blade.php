@extends('layouts.admin')

@section('content')
<div class="row evaluacion">
    <h3 class="titulo">EVALUACION FINANCIERA</h3>
    <h6><b>TIPO DE CREDITO:</b> {{ $prestamo->tipo }}</h6>
    <h6><b>PRODUCTO:</b> {{ $prestamo->producto }}</h6>
    <h6><b>DESTINO:</b> {{ $prestamo->destino }}</h6>
    <h6><b>CLIENTES:</b>
        @foreach ($prestamo->clientes as $cliente)
            {{ $cliente->nombre }}@if(!$loop->last), @endif
        @endforeach
    </h6>
    <h6><b>ACTIVIDAD:</b> {{ $prestamo->descripcion_negocio }}</h6>
    <h6><b>RESPONSABLE:</b> {{ $responsable->name }}</h6>
</div>

<div class="row" id="proyeccionventas">
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
                        @foreach($proyecciones as $proyeccion)
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
                                @if($totalCompras != 0)
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
</div>

<div class="row" id="proyeccionboletas">
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
                        @foreach($boletas as $boleta)
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
                        @foreach($gastosProducir as $gasto)
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
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Inventario</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Descripción</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventario as $item)
                        <tr>
                            <td>{{ $item->descripcion }}</td>
                            <td>{{ $item->precio_unitario * $item->cantidad }}</td>
                        </tr>
                        @endforeach

                        <tr class="finaltotal">
                            <td class="text-right"><b>Total</b></td>
                            <td>{{ $inventario->sum(fn($item) => $item->precio_unitario * $item->cantidad) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">GASTOS OPERATIVOS</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Descripción</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gastosOperativos as $gasto)
                        <tr>
                            <td>{{ $gasto->descripcion }}</td>
                            <td>{{ $gasto->precio_unitario * $gasto->cantidad }}</td>
                        </tr>
                        @endforeach

                        <tr class="finaltotal">
                            <td class="text-right"><b>Total</b></td>
                            <td>{{ $totalGastosOperativos }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">DEUDAS FINANCIERAS</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ENTIDAD</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deudas as $deuda)
                        <tr>
                            <td>{{ $deuda->nombre_entidad }}</td>
                            <td>{{ $deuda->saldo_capital }}</td>
                        </tr>
                        @endforeach

                        <tr class="finaltotal">
                            <td class="text-right"><b>Total</b></td>
                            <td>{{ $deudas->sum('saldo_capital') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">GARANTIA</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>DESCRIPCION</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($garantias as $garantia)
                        <tr>
                            <td>{{ $garantia->descripcion }}</td>
                            <td>{{ $garantia->valor_mercado }}</td>
                        </tr>
                        @endforeach
                        <tr class="finaltotal">
                            <td class="text-right"><b>Total</b></td>
                            <td>{{ $garantias->sum('valor_mercado') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Ventas diarías</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Dia</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Lunes</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Martes</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Miercoles</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Jueves</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Viernes</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Sábado</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Domingo</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><b>Total quincena</b></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><b>Total al mes</b></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">Cuadro de Capacidad de Pago</h3>
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
                            <td></td>
                        </tr>
                        <tr>
                            <!-- suma de total compras al mes -->
                            <td>Costo de Ventas</td>
                            <td></td>
                        </tr>
                        <tr>
                            <!-- Ventas - costo -->
                            <td><b>Utilidad Bruta</b></td>
                            <td></td>
                        </tr>
                        <tr>
                            <!-- total gastos operativos -->
                            <td>Gastos operativos</td>
                            <td></td>
                        </tr>
                        <tr>
                            <!-- utlidad bruta - gastos operativos -->
                            <td><b>Utilidad operativa</b></td>
                            <td></td>
                        </tr>
                        <tr>
                            <!-- total gastos familiares-->
                            <td>Gastos Familiares</td>
                            <td></td>
                        </tr>
                        <tr>
                            <!-- suma de cuotas de deudas en otras entidades-->
                            <td>Cuotas otras entidades</td>
                            <td></td>
                        </tr>
                        <tr>
                            <!-- utilidad operativa - gastos familaires - cuotas otras entidades-->
                            <td><b>Utilidad Neta</b></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Evaluación de Viabilidad Financiera</h3>
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
                            <!-- total de inventario -->
                            <td>Total patrimonial</td>
                            <td></td>
                        </tr>
                        <tr>
                            <!-- total arantia-->
                            <td>Total Garantia</td>
                            <td></td>
                        </tr>
                        <tr>
                            <!-- total prestamo -->
                            <td>Total Prestamo</td>
                            <td></td>
                        </tr>
                        <tr>
                            <!-- division de patrimonio/prestamos en %-->
                            <td><b>Viabilidad por patrimonio</b></td>
                            <td></td>
                        </tr>
                        <tr>
                            <!-- division de patrimonio/garantia en %-->
                            <td><b>Viabilidad por garantia</b></td>
                            <td></td>
                        </tr>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- <div class="col-md-6">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Indicadores Finales</h3>
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
                            <td>Solvencia</td>
                            <td>{{ number_format($solvencia, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Rentabilidad</td>
                            <td>{{ number_format($rentabilidad, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Indicador Inventario</td>
                            <td>{{ number_format($indicadorInventario, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Indicador Capital de Trabajo</td>
                            <td>{{ number_format($indicadorCapitalTrabajo, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div> -->

</div>



<div class="row" style="text-align:center;">
    <div class="col-md-12 mb-5">
        <button type="button" class="btn btn-primary btnprestamo">Aprobar</button>
        <button type="button" class="btn btn-warning btnprestamo">Rechazar</button>
    </div>
</div>
@endsection
