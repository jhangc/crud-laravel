@extends('layouts.admin')

@section('content')
<div class="row">
    <h1>Resultados</h1>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">Proyecciones de Ventas  y Margen Bruto</h3>
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
@endsection
