@extends('layouts.admin')

@section('content')
<div class="row evaluacion">
    <h3 class="titulo">EVALUACION FINANCIERA</h3>
    <h6><b>TIPO DE CREDITO:</b></h6>
    <h6><b>PRODUCTO:</b></h6>
    <h6><b>DESTINO:</b></h6>
    <h6><b>CLIENTE:</b></h6>
    <h6><b>ACTIVIDAD:</b></h6>
    <h6><b>RESPONSABLE:</b></h6>
</div>

<div class="row" id="proyeccionventas">
    <div class="col-md-12">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">Proyecciones de Ventas segun inventario y producción</h3>
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
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                        <tr class="finaltotal">
                            <td colspan="2"></td>
                            <td class="text-right"><b>Total</b></td>
                            <td></td>
                            <td></td>
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
                <h3 class="card-title">Proyeccion de ingresos por boletas</h3>
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
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                        <tr class="finaltotal">
                            <td colspan="2"></td>
                            <td class="text-right"><b>Total</b></td>
                            <td></td>
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
                <h3 class="card-title">Proyeccion de inversion y gastos a producir</h3>

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
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                        <tr class="finaltotal">
                            <td colspan="2"></td>
                            <td class="text-right"><b>Total</b></td>
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
                        <tr>
                            <td>Producto 1</td>
                            <td>500</td>
                        </tr>

                        <tr class="finaltotal">
                            <td class="text-right"><b>Total</b></td>
                            <td></td>
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
                        <tr>
                            <td>Producto 1</td>
                            <td>500</td>
                        </tr>

                        <tr class="finaltotal">
                            <td class="text-right"><b>Total</b></td>
                            <td></td>
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
                        <tr>
                            <td>Producto 1</td>
                            <td>500</td>
                        </tr>

                        <tr class="finaltotal">
                            <td class="text-right"><b>Total</b></td>
                            <td></td>
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
                        <tr>
                            <td>Producto 1</td>
                            <td>500</td>
                        </tr>
                        <tr class="finaltotal">
                            <td class="text-right"><b>Total</b></td>
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
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">Proyecciones de Ventas y Margen Bruto</h3>
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
                            <td>Utilidad Bruta</td>
                            <td>{{ number_format($utilidadBruta, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Utilidad Operativa</td>
                            <td>{{ number_format($utilidadOperativa, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Utilidad Neta</td>
                            <td>{{ number_format($utilidadNeta, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Cuota de Endeudamiento</td>
                            <td>{{ number_format($cuotaEndeudamiento, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
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
    </div>
</div>


<div class="row " style="text-align:center;">
    <div class="col-md-12 mb-5">
        <button type="button" class="btn btn-primary btnprestamo">Aprobar</button>
        <button type="button" class="btn btn-warning btnprestamo">Rechazar</button>
    </div>
</div>
@endsection