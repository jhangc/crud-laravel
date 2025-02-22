@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Reporte de Intereses - Año 2025</h1>
    </div>
    <div class="col-md-12">
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

    <script>
        $(document).ready(function() {
            var spanish = {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            };

            $('#reporte-table').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "language": spanish,
                "autoWidth": true,
                "pageLength": 10,
                dom: 'Bfrtip', // Agregar botones
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="bi bi-file-earmark-excel"></i> Exportar a Excel',
                        className: 'btn btn-success text-white', // Estilo mejorado
                        title: 'Reporte Intereses Mensual',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ]
            });

           
        });
    </script>


@endsection
