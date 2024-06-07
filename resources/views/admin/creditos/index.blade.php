@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Listado de Creditos </h1>
    </div> 
    <hr>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline">
                <div class="card-header">
                    <div class="row" style="text-align: center;">
                        <div class="col-md-4">
                            <div class="card-tools">
                                <a href="{{ url('/admin/creditos/createnuevo') }}" class="btn btn-primary"><i class="bi bi-person-fill-add"></i> Nuevo crédito</a>
                            </div>
                            
                        </div>
                        <div class="col-md-4">
                            <div class="card-tools">
                                <a href="{{ url('/admin/creditos/create') }}" class="btn btn-primary"><i class="bi bi-person-fill-add"></i> Crédito recurrente</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card-tools">
                                <a href="{{ url('/admin/creditos/create') }}" class="btn btn-primary"><i class="bi bi-person-fill-add"></i> Crédito refinanciado</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


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
                                <!-- <th>SubProducto</th>
                                <th>Destino de credito</th> -->
                                <th>Intervalo</th>
                                <th>Tasa (%)</th>
                                <th>Tiempo</th>
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
                                    <!-- <td>{{ $credito->subproducto }}</td>
                                    <td>{{ $credito->destino }}</td> -->
                                    <td>{{ $credito->recurrencia }}</td>
                                    <td>{{ $credito->tasa }}</td>
                                    <td>{{ $credito->tiempo }}</td>
                                    <td>{{ $credito->monto_total }}</td>
                                    <td>
                                        @if ($credito->estado == 'pendiente')
                                            <span style="background-color: yellow; padding: 3px 10px; border-radius: 5px;">Pendiente</span>
                                        @elseif($credito->estado == 'rechazado')
                                            <span style="background-color: red; padding: 3px 10px; border-radius: 5px;">Rechazado</span>
                                        @elseif($credito->estado == 'aprobado')
                                            <span style="background-color: green; padding: 3px 10px; border-radius: 5px;">Aprobado</span>
                                        @endif
                                    </td>
                                    <td style="display: flex; align-items: center; justify-content:center;">
                                        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#cuotasModal" onclick="loadCuotas('{{ route('credito.cuotas', $credito->id) }}')">Cuotas</a>
                                        <a href="{{  route('creditos.proyecciones', ['id' => $credito->id]) }}" class="btn btn-secondary">Resultado</a>
                                        <a href="{{ route('creditos.edit', $credito->id) }}" type="button" class="btn btn-success"><i class="bi bi-pencil"></i></a>
                                        <form action="{{ route('creditos.destroy', $credito->id) }}" onclick="preguntar(event, '{{ $id }}')" method="post" id="miFormulario{{ $id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" style="border-radius: 0px 5px 5px 0px"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cuotasModal" tabindex="-1" role="dialog" aria-labelledby="cuotasModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cuotasModalLabel">Cuotas del Crédito</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- El contenido del modal se cargará aquí -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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

        function loadCuotas(url) {
            var modal = $('#cuotasModal');
            modal.find('.modal-body').html(''); // Limpiar el contenido anterior

            // Hacer una solicitud AJAX para cargar el contenido del modal
            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    modal.find('.modal-body').html(data);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Error al cargar el contenido del modal:', textStatus, errorThrown);
                    modal.find('.modal-body').html('<p>Error al cargar el contenido.</p>');
                }
            });
        }

        function preguntar(event, id) {
            event.preventDefault();
            Swal.fire({
                title: 'Eliminar registro',
                text: '¿Desea eliminar este registro?',
                icon: 'question',
                showDenyButton: true,
                confirmButtonText: 'Eliminar',
                confirmButtonColor: '#a5161d',
                denyButtonColor: '#270a0a',
                denyButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    var form = $('#miFormulario' + id);
                    form.submit();
                }
            });
        }

        function filtrarPorTipo() {
            var seleccion = document.getElementById('tipo_ingreso').value;
            var tabla = $('#creditosTable').DataTable();
            tabla.columns(4).search(seleccion).draw();
        }
    </script>
@endsection
