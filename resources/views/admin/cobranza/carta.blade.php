@extends('layouts.admin')

@section('content')
<div class="row">
    <h1>Generar Carta Cobranza</h1>
</div>
<hr>
<div class="row">
    <div class="col-md-12">

        <div class="card-body">
            <div class="table-responsive">
                <table id="creditosTable" class="table table-bordered table-sm table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>ID</th>
                            <th>Nombres</th>
                            <th>Negocio</th>
                            <th>Producto</th>
                            <th>Monto (S/.)</th>
                            <th>Fecha de vencimiento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $contador = 0; @endphp
                        @foreach ($creditos as $credito)
                        @php
                        $contador++;
                        $id = $credito->id;
                        @endphp
                        <tr>
                            <td>{{ $contador }}</td>
                            <td>{{ $credito->id }}</td>
                            <td>
                                @foreach($credito->clientes as $cliente)
                                {{ $cliente->nombre }}<br>
                                @endforeach
                            </td>
                            <td>{{ $credito->descripcion_negocio}}</td>
                            <td>{{ $credito->producto }}</td>

                            <td>{{ $credito->monto_total }}</td>
                            <td>{{ \Carbon\Carbon::parse($credito->fecha_fin)->format('Y-m-d') }}</td>
                            <td style="display: flex; align-items: center; justify-content:center;">
                                @if ($credito->producto != "grupal")
                                    <a href="{{ route('carta-cobranza-pdf', $credito->id) }}" target="_blank" class="btn btn-primary" style="margin-left:10px">Generar Carta</a>
                                @else
                                    <a href="{{ route('carta-cobranza-grupal-pdf', $credito->id) }}" target="_blank" class="btn btn-primary" style="margin-left:10px">Generar Carta</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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

        $('#creditosTable').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "language": spanish,
            "autoWidth": true,
            "pageLength": 10
        });
    });

    function filtrarPorTipo() {
        var seleccion = document.getElementById('tipo_ingreso').value;
        var tabla = $('#creditosTable').DataTable();
        tabla.columns(4).search(seleccion).draw();
    }
</script>
@endsection