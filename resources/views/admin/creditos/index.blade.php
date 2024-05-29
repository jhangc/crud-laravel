@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Listado de Creditos</h1>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-2 ">
                            <div class="card-tools ">
                                <a href="{{ url('/admin/creditos/createnuevo') }}" class="btn btn-primary"><i
                                        class="bi bi-person-fill-add"></i> Nuevo crédito</a>
                            </div>
                        </div>
                        <div class="col-md-2 ">
                            <div class="card-tools ">
                                <a href="{{ url('/admin/creditos/create') }}" class="btn btn-primary"><i
                                        class="bi bi-person-fill-add"></i> Crédito recurrente</a>
                            </div>
                        </div>
                        <div class="col-md-2 ">
                            <div class="card-tools ">
                                <a href="{{ url('/admin/creditos/create') }}" class="btn btn-primary"><i
                                        class="bi bi-person-fill-add"></i> Crédito refinanciado</a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <select name="tipo_ingreso" id="tipo_ingreso" class="form-control" required
                                    onchange="filtrarPorTipo()">
                                    <option value="">Tipo de crédito...</option>
                                    <option value="Individual">Individual</option>
                                    <option value="Grupal">Grupal</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table class="table table-bordered table-sm table-striped table-hover">
                    <thead>
                        <tr>
                            <th>
                                <center>Nro</center>
                            </th>
                            <th>
                                <center>ID</center>
                            </th>
                            <th>
                                <center>Nombres</center>
                            </th>
                            <th>
                                <center>Negocio</center>
                            </th>
                            <th>
                                <center>Tipo de credito</center>
                            </th>
                            <th>
                                <center>Producto</center>
                            </th>
                            <th>
                                <center>SubProducto</center>
                            </th>
                            <th>
                                <center>Destino de credito</center>
                            </th>
                            {{-- <th><center>DNI</center></th> --}}
                            {{-- <th><center>Solicitante</center></th> --}}
                            <th>
                                <center>Intervalo</center>
                            </th>
                            <th>
                                <center>Tasa (%)</center>
                            </th>
                            <th>
                                <center>Tiempo</center>
                            </th>
                            <th>
                                <center>Monto</center>
                            </th>
                            <th>
                                <center>Estado</center>
                            </th>
                            <th>
                                <center>Acciones</center>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $contador = 0; @endphp
                        @foreach ($creditos as $credito)
                            @php
                                $contador = $contador + 1;
                                $id = $credito->id;
                            @endphp
                            <tr>
                                <td><center>{{ $contador }}</center></td>
                                <td><center>{{ $credito->id }}</center></td>
                                <td>
                                    @foreach($credito->clientes as $cliente)
                                        {{ $cliente->nombre }}<br>
                                    @endforeach
                                </td>
                                <td><center>{{ $credito->descripcion_negocio}}</center></td>
                                    <td>
                                    <center>{{ $credito->tipo }}</center>
                                </td>
                                <td>
                                    <center>{{ $credito->producto }}</center>
                                </td>
                                <td>
                                    <center>{{ $credito->subproducto }}</center>
                                </td>
                                <td>
                                    <center>{{ $credito->destino }}</center>
                                </td>
                                {{-- <td><center>{{ $credito->documento_identidad }}</center></td> --}}
                                {{-- <td><center>{{ $credito->solicitante }}</center></td> --}}
                                <td>
                                    <center>{{ $credito->recurrencia }}</center>
                                </td>
                                <td>
                                    <center>{{ $credito->tasa }}</center>
                                </td>
                                <td>
                                    <center>{{ $credito->tiempo }}</center>
                                </td>
                                <td>
                                    <center>{{ $credito->monto_total }}</center>
                                </td>
                                <td>
                                    <center>
                                        @if ($credito->estado == 'pendiente')
                                            <span
                                                style="background-color: yellow; padding: 3px 10px; border-radius: 5px;">Pendiente</span>
                                        @elseif($credito->estado == 'rechazado')
                                            <span
                                                style="background-color: red; padding: 3px 10px; border-radius: 5px;">Rechazado</span>
                                        @elseif($credito->estado == 'aprobado')
                                            <span
                                                style="background-color: green; padding: 3px 10px; border-radius: 5px;">Aprobado</span>
                                        @endif
                                    </center>
                                </td>
                                <td style="display: flex; align-items: center; justify-content:center;">

                                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#cuotasModal"
                                        data-url="{{ route('credito.cuotas', $credito->id) }}">Cuotas</a>


                                    <a href="{{ route('creditos.edit', $credito->id) }}"
                                        class="btn btn-secondary">Resultado</a>

                                    <a href="{{ route('creditos.edit', $credito->id) }}" type="button"
                                        class="btn btn-success"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('creditos.destroy', $credito->id) }}"
                                        onclick="preguntar<?= $id ?>(event)" method="post" id="miFormulario<?= $id ?>">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger"
                                            style="border-radius: 0px 5px 5px 0px"><i class="bi bi-trash"></i></button>
                                    </form>

                                </td> <!-- Puedes agregar aquí tus acciones -->
                            </tr>
                        @endforeach

                    </tbody>
                </table>
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
        function preguntar(id) {
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
                    // Agrega la lógica para eliminar el registro, por ejemplo, mediante una petición POST
                    console.log("Eliminar registro con ID:", id);
                    // Aquí podrías hacer una petición AJAX para eliminar el registro
                }
            });
        }
    </script>

    <script>
        function filtrarPorTipo() {
            var seleccion = document.getElementById('tipo_ingreso').value;
            var filas = document.querySelectorAll("table tbody tr"); // Selecciona todas las filas de la tabla

            filas.forEach(fila => {
                var tipoCredito = fila.cells[1]
                    .textContent; // Asume que el tipo de crédito está en la segunda columna
                if (seleccion === "" || tipoCredito.includes(seleccion)) {
                    fila.style.display =
                        ""; // Muestra la fila si coincide con el filtro o si el filtro está en blanco
                } else {
                    fila.style.display = "none"; // Oculta la fila si no coincide con el filtro
                }
            });
        }
    </script>
    <script>
        function preguntar<?= $id ?>(event) {
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
                    var form = $('#miFormulario<?= $id ?>');
                    form.submit();
                }
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            $('#cuotasModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Botón que activó el modal
                var url = button.data('url'); // Extrae la URL del atributo data-url

                var modal = $(this);
                modal.find('.modal-body').html(''); // Limpiar el contenido anterior

                // Hacer una solicitud AJAX para cargar el contenido del modal
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(data) {
                        modal.find('.modal-body').html(data);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log('Error al cargar el contenido del modal:', textStatus,
                            errorThrown);
                        modal.find('.modal-body').html('<p>Error al cargar el contenido.</p>');
                    }
                });
            });
        });
    </script>
@endsection
