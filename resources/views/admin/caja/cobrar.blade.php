

@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Cobrar credito</h1>
    </div> 
    <hr>
    <div class="row">
        <div class="col-md-12">
            
            <div class="card-body">
                <div class="table-responsive">
                    <table id="creditosTable" class="table table-bordered table-sm table-striped table-hover">
                        <thead>
                            <tr>
                                <th >Nro</th>
                                <th>ID</th>
                                <th >Nombres</th>
                                <th>Negocio</th>
                                <th>Tipo de credito</th>
                                <th>Producto</th>
                                <th>Monto (S/.)</th>
                                <th>Estado</th>
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
                                    <td>{{ $credito->tipo }}</td>
                                    <td>{{ $credito->producto }}</td>
                                    
                                    <td>{{ $credito->monto_total }}</td>
                                    <td>
                                        @if ($credito->estado == 'pendiente')
                                            <span style="background-color: yellow; padding: 3px 10px; border-radius: 5px;">Pendiente</span>
                                        @elseif($credito->estado == 'revisado')
                                            <span style="background-color: orange; padding: 3px 10px; border-radius: 5px;">Revisado</span>
                                        @elseif($credito->estado == 'rechazado')
                                            <span style="background-color: red; padding: 3px 10px; border-radius: 5px;">Rechazado</span>
                                        @elseif($credito->estado == 'aprobado')
                                            <span style="background-color: green; padding: 3px 10px; border-radius: 5px;">Aprobado</span>
                                        @elseif($credito->estado == 'rechazado por sistema')
                                            <span style="background-color: SkyBlue; padding: 3px 10px; border-radius: 5px;">Rechazado por sistema</span>
                                        @elseif($credito->estado == 'observado')
                                            <span style="background-color: purple; padding: 3px 10px; border-radius: 5px; color: white;">Observado</span>
                                        @elseif($credito->estado == 'pagado')
                                            <span style="background-color: blue; padding: 3px 10px; border-radius: 5px; color: white;">Activo</span>
                                        @elseif($credito->estado == 'terminado')
                                            <span style="background-color: grey; padding: 3px 10px; border-radius: 5px; color: white;">Terminado</span>
                                        @elseif($credito->estado == 'mora')
                                            <span style="background-color: darkred; padding: 3px 10px; border-radius: 5px; color: white;">Mora</span>
                                        @endif
                                    </td>
                                    <td style="display: flex; align-items: center; justify-content:center;">
                                        @if($credito->categoria == 'credijoya')
                                            <a href="{{ route('pagocredijoya.create', $credito->id) }}" 
                                            class="btn btn-warning" style="margin-left:10px">
                                                Cobrar
                                            </a>
                                        @else
                                            
                                            <a href="{{ route('creditos.verpagocuota', ['id' => $credito->id]) }}" 
                                            class="btn btn-primary" style="margin-left:10px">
                                                Cobrar
                                            </a>
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
