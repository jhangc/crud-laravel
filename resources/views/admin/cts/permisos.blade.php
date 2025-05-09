@extends('layouts.admin')

@section('title', 'Permisos CTS')
@section('page-title', 'Permisos CTS')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- Listado de movimientos -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white"
                            style="display: flex; justify-content: space-between;">
                            <h3 class="mb-0">Permisos CTS</h3>
                            <button id="btnAutorizarRetiro" type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                data-target="#modalPermiso">
                                Autorizar retiro
                            </button>

                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tablaMovimientos" class="table table-hover mb-0 nowrap" style="width:100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Documento</th>
                                            <th>Responsable</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($permisos as $permiso)
                                            <tr>
                                                <td>{{ $permiso->created_at }}</td>
                                                <td>
                                                    @if ($permiso->documento_autorizacion)
                                                        <a href="{{ asset('permisos_cts/' . $permiso->documento_autorizacion) }}"
                                                            target="_blank" class="btn btn-sm btn-info">
                                                            Ver documento
                                                        </a>
                                                    @else
                                                        <span class="text-muted">Sin documento</span>
                                                    @endif
                                                </td>


                                                <td>{{ $permiso->user->name }}</td>
                                                <td>
                                                    @if ($permiso->permiso_abierto === 1)
                                                        <button onclick="cerrar('{{ $permiso->id }}')" type="button"
                                                            class="btn bg-warning">Cerrar Permiso</button>
                                                    @else
                                                        <span class="badge badge-primary">Cerrado</span>
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
            </div>
        </div>
    </div>

    <!-- Modal Solicitar Retiro -->
    <div class="modal fade" id="modalPermiso" tabindex="-1" role="dialog" aria-labelledby="modalRetiroLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRetiroLabel">Solicitar Retiro</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Se añade enctype al formulario para subir archivos -->
                    <form id="formRetiro" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="archivoPermiso">Adjuntar documento de permiso</label>
                            <input type="file" class="form-control-file" id="archivoPermiso"
                                name="documento_autorizacion" accept=".pdf,.jpg,.png" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" id="btnGuardarRetiro" class="btn btn-primary">Autorizar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            $('#tablaMovimientos').DataTable({
                dom: 'Bfrtip', // Botones + tabla + info + paginación
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Exportar a Excel',
                    className: 'btn btn-success mb-3',
                    titleAttr: 'Descargar en formato Excel'
                }],
                paging: true,
                pageLength: 10,
                searching: false,
                info: true,
                ordering: false,
                lengthChange: false,
                responsive: true // Activa ajuste en móvil,
            });
        });

        

        $('#btnGuardarRetiro').on('click', function() {
            const form = $('#formRetiro')[0];
            const formData = new FormData(form);

            $.ajax({
                url: '{{ url('admin/cts/guardarpermiso') }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function() {
                    Swal.fire('Enviado', 'Permiso Abierto', 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    // Por defecto
                    let title = 'Error';
                    let text = 'No se pudo procesar la autorización';

                    // Si llega JSON con message, lo usamos
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        text = xhr.responseJSON.message;
                    }

                    Swal.fire(title, text, 'error');
                }
            });
        });


        function cerrar(permisoId) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Se cerrará el permiso CTS. ¿Deseas continuar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cerrar permiso',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('admin/cts/cerrarpermiso') }}/${permisoId}`,
                        method: 'GET',
                        data: {
                            _method: 'PATCH',
                            permiso_abierto: 2
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: () => {
                            Swal.fire('¡Listo!', 'El permiso ha sido cerrado.', 'success')
                                .then(() => location.reload());
                        },
                        error: () => {
                            Swal.fire('Error', 'No se pudo cerrar el permiso.', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
