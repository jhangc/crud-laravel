@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-undo"></i> Reversar Pago - Crédito Grupal</h2>
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
                    <h5 class="mb-0">⚠️ Últimos Pagos Grupales Registrados (Últimos 30 días)</h5>
                </div>
                <div class="card-body">
                    @if($pagos->count() > 0)
                        <div class="table-responsive">
                            <table id="tablaPagosGrupal" class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID Pago</th>
                                        <th>ID Crédito</th>
                                        <th>Nombre/Grupo</th>
                                        <th>Monto</th>
                                        <th>Cuota #</th>
                                        <th>Fecha Pago</th>
                                        <th>Tipo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pagos as $pago)
                                        <tr>
                                            <td>{{ $pago->id }}</td>
                                            <td>{{ $pago->prestamo_id }}</td>
                                            <td>
                                                @if($pago->cliente_id)
                                                    {{ $pago->cliente->nombre ?? 'Sin nombre' }}
                                                @else
                                                    {{ $pago->prestamo->nombre_prestamo ?? 'Grupo sin nombre' }}
                                                @endif
                                            </td>
                                            <td>S/. {{ number_format($pago->monto, 2) }}</td>
                                            <td>{{ $pago->numero_cuota }}</td>
                                            <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                                            <td>
                                                @if($pago->cliente_id)
                                                    <span class="badge badge-info">Integrante</span>
                                                @else
                                                    <span class="badge badge-primary">Grupo</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-danger" onclick="abrirModalReversalGrupal({{ $pago->prestamo_id }}, '{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('Y-m-d') }}')">
                                                    <i class="fas fa-trash"></i> Reversar
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <strong>Sin registros.</strong> No hay pagos grupales para reversar.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="modalReversalGrupal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalLabel">⚠️ Confirmar Reversión de Pago Grupal</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>ID Crédito Grupal:</strong> <span id="creditoId"></span></p>
                <p><strong>Fecha de Pago:</strong> <span id="fechaPago"></span></p>
                <p class="text-danger"><strong>Advertencia:</strong> Se reversarán TODOS los ingresos de esta fecha para este crédito.</p>
                <div class="form-group">
                    <label for="motivoTextareaGrupal"><strong>Motivo de la reversión:</strong></label>
                    <textarea class="form-control" id="motivoTextareaGrupal" rows="4" placeholder="Describa el motivo de la reversión..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmarReversalGrupal()">
                    <i class="fas fa-check"></i> Confirmar Reversión
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let creditoActualId = null;
let fechaActual = null;
let tablaGrupal = null;

function abrirModalReversalGrupal(creditoId, fecha) {
    creditoActualId = creditoId;
    fechaActual = fecha;
    document.getElementById('creditoId').textContent = creditoId;
    document.getElementById('fechaPago').textContent = new Date(fecha).toLocaleDateString('es-ES');
    document.getElementById('motivoTextareaGrupal').value = '';
    $('#modalReversalGrupal').modal('show');
}

function confirmarReversalGrupal() {
    const motivo = document.getElementById('motivoTextareaGrupal').value.trim();

    if (!motivo) {
        Swal.fire({
            icon: 'warning',
            title: 'Campo requerido',
            text: 'Por favor ingrese el motivo de la reversión',
        });
        return;
    }

    Swal.fire({
        title: '¿Está seguro?',
        text: 'Esta acción reversará TODOS los pagos de esta fecha para este crédito grupal. ¿Desea continuar?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, reversar',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            realizarReversalGrupal(motivo);
        }
    });
}

function realizarReversalGrupal(motivo) {
    $.ajax({
        url: `/admin/pagos/reversar-grupal`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Content-Type': 'application/json'
        },
        data: JSON.stringify({
            prestamo_id: creditoActualId,
            fecha: fechaActual,
            motivo: motivo
        }),
        success: function (response) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: response.message,
            }).then(() => {
                location.reload();
            });
            $('#modalReversalGrupal').modal('hide');
        },
        error: function (xhr) {
            const errorMsg = xhr.responseJSON?.error || 'Error desconocido';
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMsg,
            });
        }
    });
}

function aplicarFiltros() {
    const fechaInicio = document.getElementById('filtroFechaInicio').value;
    const fechaFin = document.getElementById('filtroFechaFin').value;

    if (!fechaInicio || !fechaFin) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos requeridos',
            text: 'Por favor seleccione ambas fechas',
        });
        return;
    }

    tablaGrupal.column(4).search(
        '^' + fechaInicio + '|' + fechaFin + '$',
        true,
        false,
        true
    ).draw();
}

function limpiarFiltros() {
    document.getElementById('filtroFechaInicio').value = '';
    document.getElementById('filtroFechaFin').value = '';
    tablaGrupal.column(4).search('').draw();
}

$(document).ready(function () {
    tablaGrupal = $('#tablaPagosGrupal').DataTable({
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
            "sInfoFiltered": "(filtrado de _MAX_ registros en total)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero",
                "sPrevious": "Anterior",
                "sNext": "Siguiente",
                "sLast": "Último"
            }
        },
        "paging": true,
        "pageLength": 10,
        "searching": true,
        "ordering": true,
        "order": [[4, "desc"]]
    });
});
</script>
@endsection
