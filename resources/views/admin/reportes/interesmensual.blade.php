@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Reporte de Intereses - Año 2025</h1>
    </div>
    <div class="col-md-12">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Préstamo</th>
                            <th>Nombre del Préstamo</th>
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
                                <td>{{ $fila->nombre_prestamo }}</td>
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
                            <td colspan="2"><strong>Total</strong></td>
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
