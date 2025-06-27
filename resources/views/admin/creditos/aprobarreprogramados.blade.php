@extends('layouts.admin')

@section('content')
    <div class="row mb-3">
        <h1>Solicitudes de Reprogramación Pendientes</h1>
    </div>
    <hr>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table id="reprogramacionesTable" class="table table-bordered table-sm table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID Solicitud</th>
                            <th>Crédito ID</th>
                            <th>Cliente(s)</th>
                            <th>Asesor</th>
                            <th>Fecha Solicitud</th>
                            <th>Cuotas Pendientes</th>
                            <th>Nuevas Cuotas</th>
                            <th>Tasa (i)</th>
                            <th>Nuevo Monto</th>
                            <th>Fecha Reprogramar</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reprogramaciones as $idx => $r)
                            <tr>
                                <td>{{ $r->id }}</td>
                                <td>{{ $r->credito_id }}</td>
                                <td>
                                    @foreach ($r->credito->clientes as $cliente)
                                        {{ $cliente->nombre }}<br>
                                    @endforeach
                                </td>
                                <td>{{ optional($r->solicitante)->name ?? '—' }}</td>
                                <td>{{ $r->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $r->cuotas_pendientes }}</td>
                                <td>{{ $r->nuevo_numero_cuotas }}</td>
                                <td>{{ $r->tasa_interes }}</td>
                                <td>{{ $r->capital_restante + $r->interes_restante }}</td>
                                <td>{{ $r->fecha_reprogramar }}</td>
                                <td>
                                    @switch($r->estado)
                                        @case('pendiente')
                                            <span class="badge badge-warning">Pendiente</span>
                                        @break

                                        @case('aprobada')
                                            <span class="badge badge-success">Aprobada</span>
                                        @break

                                        @case('rechazada')
                                            <span class="badge badge-danger">Rechazada</span>
                                        @break
                                    @endswitch
                                </td>
                                <td class="text-center">
                                    @if ($r->estado === 'pendiente')
                                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal"
                                            data-target="#aprobarModal"
                                            onclick="setAprobacionData({{ $r->id }}, `{{ addslashes($r->observaciones) }}`)">
                                            Evaluar
                                        </button>
                                    @else
                                        <em>Sin acciones</em>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal único -->
    <div class="modal fade" id="aprobarModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="aprobarForm">
                @csrf
                <input type="hidden" name="id" id="modal_reprog_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Procesar Reprogramación</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Comentario del solicitante:</strong></p>
                        <textarea id="modal_reprog_observaciones" class="form-control mb-3" readonly></textarea>
                        <div class="form-group">
                            <label>Tu comentario</label>
                            <textarea name="comentario_admin" id="comentario_admin" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="procesarReprogramacion('aprobada')">
                            Aprobar
                        </button>
                        <button type="button" class="btn btn-danger" onclick="procesarReprogramacion('rechazada')">
                            Rechazar
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
        $(function() {
            $('#reprogramacionesTable').DataTable({
                paging: true,
                lengthChange: false,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: true,
                pageLength: 10,
                language: {
                    sProcessing: "Procesando...",
                    sLengthMenu: "Mostrar _MENU_ registros",
                    sZeroRecords: "No se encontraron resultados",
                    sEmptyTable: "Ningún dato disponible en esta tabla",
                    sInfo: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    sInfoEmpty: "Mostrando 0 a 0 de 0 registros",
                    sInfoFiltered: "(filtrado de _MAX_ totales)",
                    sSearch: "Buscar:",
                    oPaginate: {
                        sFirst: "Primero",
                        sLast: "Último",
                        sNext: "Siguiente",
                        sPrevious: "Anterior"
                    },
                }
            });
        });
    </script>

    <script>
        function setAprobacionData(id, observaciones) {
            $('#modal_reprog_id').val(id);
            $('#modal_reprog_observaciones').val(observaciones);
            // no llamamos .modal('show'), Bootstrap lo hará automáticamente
        }

        function procesarReprogramacion(accion) {
            // 1) Recoger datos
            const id = $('#modal_reprog_id').val();
            const comentario = $('#comentario_admin').val().trim();
            const _token = '{{ csrf_token() }}';

            // 2) Construir payload
            const payload = {
                _token: _token,
                id: id,
                estado: accion,
                comentario_admin: comentario
            };

            // 3) Llamada AJAX
            $.ajax({
                url: '/reprogramaciones/process', // ajusta tu ruta
                method: 'POST',
                data: payload,
                success: function(res) {
                    Swal.fire('¡Listo!', res.message, 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'No se pudo procesar.', 'error');
                }
            });
        }
    </script>
@endsection
