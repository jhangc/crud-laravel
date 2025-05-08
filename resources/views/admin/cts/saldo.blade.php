@extends('layouts.admin')

@section('title', 'Desembolso CTS')
@section('page-title', 'Desembolso CTS')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12 col-md-4">
                    <div class="card text-white shadow-lg rounded-lg"
                        style="background: linear-gradient(135deg, #007bff 0%, #00c6ff 100%);">
                        <div class="card-body text-center"
                            style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                            <h4 class="mb-1">{{ $cuenta->user->name }}</h4>
                            <h5 class="card-title"><b>Número de cuenta</b></h5>
                            <h4 class="font-weight-bold mb-1">{{ $cuenta->numero_cuenta }}</h4>
                            <h5 class="card-title"><b>Saldo Disponible</b></h5>
                            <h3 class="font-weight-bold mb-0">{{ $cuenta->saldo_disponible }}</h3>
                        </div>
                    </div>
                </div>


            </div>

            <!-- Listado de movimientos -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white"
                            style="display: flex; justify-content: space-between;">
                            <h3 class="mb-0">Movimientos CTS</h3>
                            <button id="btnSolicitarRetiro" type="button" class="btn btn-warning btn-sm"
                                data-toggle="modal" data-target="#modalRetiro">
                                Solicitar retiro
                            </button>

                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="tablaMovimientos" class="table table-hover mb-0 nowrap" style="width:100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Tipo</th>
                                            <th>Monto</th>
                                            <th>Responsable</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($depositos as $deposito)
                                            <tr>
                                                <td>{{ $deposito->fecha_deposito }}</td>
                                                <td>
                                                    @if ($deposito->tipo_transaccion === 1)
                                                        <span class="badge badge-success">Depósito</span>
                                                    @else
                                                        <span class="badge badge-danger">Pago</span>
                                                    @endif
                                                </td>
                                                <td>S/ {{ number_format($deposito->monto, 2) }}</td>
                                                <td>{{ $deposito->realizadoPor->name }}</td>
                                                <td>
                                                    @if ($deposito->estado === 1)
                                                        <span class="badge badge-primary">Pagado</span>
                                                    @else
                                                        <span class="badge badge-warning">Pendiente</span>
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
    <div class="modal fade" id="modalRetiro" tabindex="-1" role="dialog" aria-labelledby="modalRetiroLabel"
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
                    <form id="formRetiro">
                        @csrf
                        <div class="form-group">
                            <label for="montoRetiro">Monto a retirar</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="montoRetiro"
                                name="monto" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" id="btnGuardarRetiro" class="btn btn-primary">Enviar solicitud</button>
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
                lengthChange: false,
                responsive: true // Activa ajuste en móvil,
            });
        });

        // Abrir modal de retiro
        $('#btnSolicitarRetiro').on('click', function() {
            $('#montoRetiro').val('');
            $('#modalRetiro').modal('show');
        });

        $('#btnGuardarRetiro').on('click', function() {
            let monto = parseFloat($('#montoRetiro').val());
            let saldo = parseFloat('{{ $cuenta->saldo_disponible }}');
            if (isNaN(monto) || monto <= 0) {
                Swal.fire('Error', 'Ingresa un monto válido', 'error');
                return;
            }
            if (monto > saldo) {
                Swal.fire('Error', 'Monto superior al disponible', 'error');
                return;
            }
            $.ajax({
                url: '{{ url("admin/cts/solicitud-pago") }}',
                method: 'POST',
                data: {
                    cts_usuario_id: {{ $cuenta->id }},
                    monto: monto,
                    tipo_transaccion: 2,
                    estado: 2,
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    Swal.fire('Enviado', 'Pendiente de aprobación', 'success')
                        .then(() => location.reload());
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo procesar la solicitud', 'error');
                }
            });
        });
    </script>
@endsection
