@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-undo"></i> Reversar Pago de Credijoya</h2>
            <hr>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label><strong>Filtrar por Fecha Inicio:</strong></label>
                            <input type="date" id="filtroFechaInicio" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label><strong>Filtrar por Fecha Fin:</strong></label>
                            <input type="date" id="filtroFechaFin" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <button class="btn btn-primary btn-block" onclick="aplicarFiltros()">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <button class="btn btn-secondary btn-block" onclick="limpiarFiltros()">
                                <i class="fas fa-redo"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">⚠️ Últimos Pagos Registrados (Últimos 30 días)</h5>
                </div>
                <div class="card-body">
                    @if($pagos->count() > 0)
                        <div class="table-responsive">
                            <table id="tablaReversiones" class="table table-sm table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID Pago</th>
                                        <th>ID Crédito</th>
                                        <th>Cliente</th>
                                        <th>Monto</th>
                                        <th>Tipo</th>
                                        <th>Modo</th>
                                        <th>Fecha Pago</th>
                                        <th>¿Renovación?</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pagos as $pago)
                                        <tr>
                                            <td><strong>#{{ $pago->id }}</strong></td>
                                            <td>{{ $pago->prestamo_id }}</td>
                                            <td>
                                                {{ $pago->cliente->nombre ?? 'N/A' }}
                                            </td>
                                            <td class="text-right">
                                                <strong>S/ {{ number_format($pago->monto, 2) }}</strong>
                                            </td>
                                            <td>
                                                @if($pago->tipo === 'total')
                                                    <span class="badge badge-danger">{{ $pago->tipo }}</span>
                                                @elseif($pago->tipo === 'interes')
                                                    <span class="badge badge-info">{{ $pago->tipo }}</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $pago->tipo }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-primary">{{ $pago->modo }}</span>
                                            </td>
                                            <td>
                                                {{ $pago->fecha_pago ?? $pago->created_at->format('d/m/Y') }}
                                                <br>
                                                <small class="text-muted">{{ $pago->hora_pago ?? $pago->created_at->format('H:i:s') }}</small>
                                            </td>
                                            <td class="text-center">
                                                @if($pago->nuevo_id)
                                                    <span class="badge badge-success">SÍ (#{{ $pago->nuevo_id }})</span>
                                                @else
                                                    <span class="badge badge-secondary">NO</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="confirmReversarPago({{ $pago->id }}, '{{ $pago->cliente->nombre ?? 'Cliente' }}', {{ $pago->monto }})">
                                                    <i class="fas fa-undo"></i> Reversar
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No hay pagos registrados para reversar.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="modalConfirmacion" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Confirmar Reversión de Pago
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <strong>⚠️ OPERACIÓN IRREVERSIBLE</strong>
                    <p>Está a punto de reversar un pago. Esta acción:</p>
                    <ul>
                        <li>Eliminará el ingreso registrado</li>
                        <li>Restaurará el estado anterior del crédito</li>
                        <li>Si hay renovación, eliminará el nuevo crédito y sus cuotas</li>
                        <li>Descontará el monto de la caja</li>
                        <li>Quedará registrado en los logs del sistema</li>
                    </ul>
                </div>

                <div class="form-group">
                    <label><strong>Cliente:</strong></label>
                    <div id="display-cliente" class="form-control-plaintext">-</div>
                </div>

                <div class="form-group">
                    <label><strong>Monto a Revertir:</strong></label>
                    <div id="display-monto" class="form-control-plaintext text-danger">-</div>
                </div>

                <div class="form-group">
                    <label for="motivo-reversión"><strong>Motivo de Reversión (opcional):</strong></label>
                    <textarea class="form-control" id="motivo-reversión" rows="3" placeholder="Ej: Pago registrado por error, cliente cancela..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btn-confirmar-reversión">
                    <i class="fas fa-check"></i> SÍ, Reversar Pago
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .badge {
        padding: 5px 10px;
        border-radius: 3px;
    }
</style>

<script>
    let pagoIdActual = null;

    $(document).ready(function() {
        // Configuración de idioma en español
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

        // Inicializar DataTable
        var tabla = $('#tablaReversiones').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "language": spanish,
            "autoWidth": true,
            "pageLength": 10,
            "order": [[6, "desc"]]  // Ordenar por columna Fecha Pago (índice 6) descendente
        });
    });

    function aplicarFiltros() {
        const tabla = $('#tablaReversiones').DataTable();
        const fechaInicio = document.getElementById('filtroFechaInicio').value;
        const fechaFin = document.getElementById('filtroFechaFin').value;
        
        if (!fechaInicio || !fechaFin) {
            Swal.fire('Error', 'Por favor selecciona ambas fechas', 'warning');
            return;
        }
        
        tabla.column(6).search('^' + fechaInicio + '|' + fechaFin + '$', true, false, true).draw();
    }

    function limpiarFiltros() {
        const tabla = $('#tablaReversiones').DataTable();
        document.getElementById('filtroFechaInicio').value = '';
        document.getElementById('filtroFechaFin').value = '';
        tabla.column(6).search('').draw();
    }

    function confirmReversarPago(pagoId, cliente, monto) {
        pagoIdActual = pagoId;
        $('#display-cliente').text(cliente);
        $('#display-monto').html('S/ ' + parseFloat(monto).toLocaleString('es-PE', {minimumFractionDigits: 2}));
        $('#motivo-reversión').val('');
        $('#modalConfirmacion').modal('show');
    }

    $('#btn-confirmar-reversión').on('click', function() {
        if (!pagoIdActual) return;

        const btnSpinner = $(this);
        const textOriginal = btnSpinner.html();
        
        btnSpinner.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');

        const motivo = $('#motivo-reversión').val();

        $.ajax({
            url: `/admin/credijoya/pago/${pagoIdActual}/reversar`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({
                motivo: motivo
            }),
            success: function(response) {
                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: '✓ Éxito',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                    $('#modalConfirmacion').modal('hide');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.error || 'Ocurrió un error desconocido',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON || {};
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.error || 'Error al procesar la solicitud',
                    confirmButtonText: 'OK'
                });
            },
            complete: function() {
                btnSpinner.prop('disabled', false).html(textOriginal);
            }
        });
    });
</script>
@endsection
