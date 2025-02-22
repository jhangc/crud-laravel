@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Reporte de Intereses - Año {{ \Carbon\Carbon::parse($fecha)->year }}</h1>
    </div>

    <div class="col-md-12">
        <div class="row mb-3">
            <div class="col-md-4">
                <form id="dateFilterForm" method="GET">
                    <div class="input-group">
                        <input type="date" id="fechaInput" name="fecha" class="form-control"
                            value="{{ request('fecha', date('Y-m-d')) }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>


        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="reporte-table">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Préstamo</th>
                            <th>Nombre del Préstamo</th>
                            <th>Tipo</th>
                            <th>Enero</th>
                            <th>Febrero</th>
                            <th>Marzo</th>
                            <th>Abril</th>
                            <th>Mayo</th>
                            <th>Junio</th>
                            <th>Julio</th>
                            <th>Agosto</th>
                            <th>Septiembre</th>
                            <th>Octubre</th>
                            <th>Noviembre</th>
                            <th>Diciembre</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reporte as $fila)
                            <tr>
                                <td>{{ $fila->id_prestamo }}</td>
                                <td>{{ $fila->nombre_credito }}</td>
                                <td>{{ $fila->tipo_credito }}</td>
                                <td>{{ number_format($fila->enero, 2) }}</td>
                                <td>{{ number_format($fila->febrero, 2) }}</td>
                                <td>{{ number_format($fila->marzo, 2) }}</td>
                                <td>{{ number_format($fila->abril, 2) }}</td>
                                <td>{{ number_format($fila->mayo, 2) }}</td>
                                <td>{{ number_format($fila->junio, 2) }}</td>
                                <td>{{ number_format($fila->julio, 2) }}</td>
                                <td>{{ number_format($fila->agosto, 2) }}</td>
                                <td>{{ number_format($fila->septiembre, 2) }}</td>
                                <td>{{ number_format($fila->octubre, 2) }}</td>
                                <td>{{ number_format($fila->noviembre, 2) }}</td>
                                <td>{{ number_format($fila->diciembre, 2) }}</td>
                                <td>{{ number_format($fila->total_interes, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="table-info">
                            <td colspan="3"><strong>Total</strong></td>
                            <td>{{ number_format($totalesMeses['enero'], 2) }}</td>
                            <td>{{ number_format($totalesMeses['febrero'], 2) }}</td>
                            <td>{{ number_format($totalesMeses['marzo'], 2) }}</td>
                            <td>{{ number_format($totalesMeses['abril'], 2) }}</td>
                            <td>{{ number_format($totalesMeses['mayo'], 2) }}</td>
                            <td>{{ number_format($totalesMeses['junio'], 2) }}</td>
                            <td>{{ number_format($totalesMeses['julio'], 2) }}</td>
                            <td>{{ number_format($totalesMeses['agosto'], 2) }}</td>
                            <td>{{ number_format($totalesMeses['septiembre'], 2) }}</td>
                            <td>{{ number_format($totalesMeses['octubre'], 2) }}</td>
                            <td>{{ number_format($totalesMeses['noviembre'], 2) }}</td>
                            <td>{{ number_format($totalesMeses['diciembre'], 2) }}</td>
                            <td>{{ number_format($totalesMeses['total_interes'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


@endsection
