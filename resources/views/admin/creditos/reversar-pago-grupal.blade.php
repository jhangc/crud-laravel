@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h2 class="mb-0"><i class="fas fa-undo"></i> Reversar Pago - Crédito Grupal</h2>
            <a href="{{ route('pagos.historial-reversiones') }}" class="btn btn-outline-dark">
                <i class="fas fa-history"></i> Ver pagos dados de baja
            </a>
        </div>
        <div class="col-md-12"><hr></div>
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
                    @if($grupos->count() > 0)
                        <div class="table-responsive">
                            <table id="tablaPagosGrupal" class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th></th>
                                        <th>Crédito</th>
                                        <th>Grupo</th>
                                        <th>Cuota #</th>
                                        <th>Integrantes</th>
                                        <th>Total pagado</th>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($grupos as $g)
                                        <tr data-integrantes='@json($g['integrantes'])' data-dia="{{ \Carbon\Carbon::parse($g['fecha'])->format('d/m/Y') }}">
                                            <td class="details-control text-center" style="cursor:pointer; width:30px;">
                                                <i class="fas fa-plus-circle text-primary"></i>
                                            </td>
                                            <td>#{{ $g['prestamo_id'] }}</td>
                                            <td><strong>{{ $g['nombre'] }}</strong></td>
                                            <td><span class="badge badge-secondary">Cuota {{ $g['numero_cuota'] }}</span></td>
                                            <td class="text-center">{{ $g['num_integrantes'] }}</td>
                                            <td>S/. {{ number_format($g['total'], 2) }}</td>
                                            <td data-order="{{ $g['fecha'] }}">
                                                {{ \Carbon\Carbon::parse($g['fecha'])->format('d/m/Y') }}
                                            </td>
                                            <td>{{ $g['hora'] }}</td>
                                            <td>
                                                @if($g['es_grupo'])
                                                    <button class="btn btn-sm btn-danger"
                                                            onclick="abrirModalReversalGrupal({{ (int) $g['transaccion_id'] }}, {{ (int) $g['prestamo_id'] }}, {{ (int) $g['numero_cuota'] }}, '{{ addslashes($g['nombre']) }}', '{{ \Carbon\Carbon::parse($g['fecha'])->format('d/m/Y') }}')">
                                                        <i class="fas fa-trash"></i> Reversar grupo
                                                    </button>
                                                @else
                                                    <span class="badge badge-warning" title="Pago hecho por separado">Pago separado</span>
                                                    <small class="text-muted d-block">Reversar en el detalle ▸</small>
                                                @endif
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
                <p><strong>Grupo:</strong> <span id="creditoId"></span></p>
                <p><strong>Cuota #:</strong> <span id="numeroCuota"></span></p>
                <p><strong>Fecha de Pago:</strong> <span id="fechaPago"></span></p>
                <p class="text-danger"><strong>Advertencia:</strong> Se reversarán los ingresos de <strong>este pago grupal</strong> (todos los integrantes que pagaron juntos en esta transacción). Otros pagos no se verán afectados.</p>
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
let transaccionActual = null;
let prestamoActual = null;
let numeroCuotaActual = null;
let tablaGrupal = null;

function abrirModalReversalGrupal(transaccionId, prestamoId, numeroCuota, nombre, fecha) {
    transaccionActual = transaccionId;
    prestamoActual = prestamoId;
    numeroCuotaActual = numeroCuota;
    document.getElementById('creditoId').textContent = nombre + ' (Crédito #' + prestamoId + ')';
    document.getElementById('numeroCuota').textContent = numeroCuota;
    document.getElementById('fechaPago').textContent = fecha;
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
        text: 'Esta acción reversará los pagos de esta cuota para todos los integrantes del grupo. ¿Desea continuar?',
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
            transaccion_id: transaccionActual,
            prestamo_id: prestamoActual,
            numero_cuota: numeroCuotaActual,
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

    tablaGrupal.column(6).search(
        '^' + fechaInicio + '|' + fechaFin + '$',
        true,
        false,
        true
    ).draw();
}

function limpiarFiltros() {
    document.getElementById('filtroFechaInicio').value = '';
    document.getElementById('filtroFechaFin').value = '';
    tablaGrupal.column(6).search('').draw();
}

// Escapa comillas para insertar texto en el onclick generado.
function escAttr(s) {
    return String(s == null ? '' : s).replace(/\\/g, '\\\\').replace(/'/g, "\\'");
}

// Construye la tabla de detalle (integrantes) de una cuota grupal,
// con un boton Reversar por integrante.
function formatoDetalleIntegrantes(integrantes) {
    if (!integrantes || !integrantes.length) {
        return '<div class="p-2 text-muted">Sin integrantes.</div>';
    }
    let filas = integrantes.map(function (i) {
        return '<tr>' +
            '<td>#' + i.id + '</td>' +
            '<td>' + i.nombre + '</td>' +
            '<td>Cuota ' + i.cuota + '</td>' +
            '<td>S/. ' + Number(i.monto).toFixed(2) + '</td>' +
            '<td>' + (i.hora || '') + '</td>' +
            '<td><button class="btn btn-sm btn-outline-danger" onclick="reversarIntegrante(' +
                i.id + ", '" + escAttr(i.nombre) + "')\">" +
                '<i class="fas fa-trash"></i> Reversar</button></td>' +
            '</tr>';
    }).join('');

    return '<div class="p-2 bg-light">' +
        '<table class="table table-sm table-bordered mb-0">' +
        '<thead><tr>' +
        '<th>ID Pago</th><th>Integrante</th><th>Cuota</th><th>Monto</th><th>Hora</th><th>Acción</th>' +
        '</tr></thead><tbody>' + filas + '</tbody></table></div>';
}

// Reversa el pago de un solo integrante (pide motivo obligatorio).
function reversarIntegrante(ingresoId, nombre) {
    Swal.fire({
        title: 'Reversar pago de ' + nombre,
        text: 'Se reversará solo el pago de este integrante (y el cierre del grupo de esa cuota, si existe).',
        icon: 'warning',
        input: 'textarea',
        inputLabel: 'Motivo de la reversión (obligatorio)',
        inputPlaceholder: 'Describa el motivo...',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, reversar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value || !value.trim()) { return 'El motivo es obligatorio'; }
        }
    }).then((result) => {
        if (!result.isConfirmed) return;
        $.ajax({
            url: '/admin/pagos/reversar-integrante/' + ingresoId,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({ motivo: result.value }),
            success: function (response) {
                Swal.fire({ icon: 'success', title: '¡Éxito!', text: response.message })
                    .then(() => location.reload());
            },
            error: function (xhr) {
                const errorMsg = xhr.responseJSON?.error || 'Error desconocido';
                Swal.fire({ icon: 'error', title: 'Error', text: errorMsg });
            }
        });
    });
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
        "pageLength": 25,
        "searching": true,
        "ordering": true,
        // Sin orden inicial: respeta el orden del servidor (agrupado por evento de pago).
        "order": [],
        // La primera columna (control de expandir) y la de acciones no se ordenan.
        "columnDefs": [
            { "orderable": false, "targets": [0, -1] }
        ],
        // Cabecera separadora por dia de pago.
        "drawCallback": function () {
            let api = this.api();
            let rows = api.rows({ page: 'current' }).nodes();
            let last = null;
            $(rows).each(function () {
                let dia = $(this).data('dia');
                if (last !== dia) {
                    $(this).before(
                        '<tr class="bg-secondary text-white"><td colspan="9">' +
                        '<i class="fas fa-calendar-day"></i> <strong>' + dia + '</strong></td></tr>'
                    );
                    last = dia;
                }
            });
        }
    });

    // Expandir / contraer el detalle de integrantes de cada cuota grupal.
    $('#tablaPagosGrupal tbody').on('click', 'td.details-control', function () {
        let tr = $(this).closest('tr');
        let row = tablaGrupal.row(tr);
        let icono = $(this).find('i');

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            icono.removeClass('fa-minus-circle text-danger').addClass('fa-plus-circle text-primary');
        } else {
            let integrantes = tr.data('integrantes');
            row.child(formatoDetalleIntegrantes(integrantes)).show();
            tr.addClass('shown');
            icono.removeClass('fa-plus-circle text-primary').addClass('fa-minus-circle text-danger');
        }
    });
});
</script>
@endsection
