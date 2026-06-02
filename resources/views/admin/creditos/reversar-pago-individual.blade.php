@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h2 class="mb-0"><i class="fas fa-undo"></i> Reversar Pago - Crédito Individual</h2>
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
                    <h5 class="mb-0">⚠️ Últimos Pagos Registrados (Últimos 30 días)</h5>
                </div>
                <div class="card-body">
                    @if($grupos->count() > 0)
                        <div class="table-responsive">
                            <table id="tablaPagosIndividual" class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th></th>
                                        <th>Crédito</th>
                                        <th>Cliente</th>
                                        <th>Pagos del día</th>
                                        <th>Total pagado</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($grupos as $g)
                                        <tr data-pagos='@json($g['pagos'])' data-dia="{{ \Carbon\Carbon::parse($g['fecha'])->format('d/m/Y') }}">
                                            <td class="details-control text-center" style="cursor:pointer; width:30px;">
                                                <i class="fas fa-plus-circle text-primary"></i>
                                            </td>
                                            <td>#{{ $g['prestamo_id'] }}</td>
                                            <td><strong>{{ $g['cliente'] }}</strong></td>
                                            <td class="text-center">{{ $g['num_pagos'] }}</td>
                                            <td>S/. {{ number_format($g['total'], 2) }}</td>
                                            <td data-order="{{ $g['fecha'] }}">
                                                {{ \Carbon\Carbon::parse($g['fecha'])->format('d/m/Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <strong>Sin registros.</strong> No hay pagos individuales para reversar.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="modalReversalIndividual" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalLabel">⚠️ Confirmar Reversión de Pago</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Cliente:</strong> <span id="clienteNombre"></span></p>
                <p><strong>Monto:</strong> <span id="montoDisplay"></span></p>
                <div class="form-group">
                    <label for="motivoTextarea"><strong>Motivo de la reversión:</strong></label>
                    <textarea class="form-control" id="motivoTextarea" rows="4" placeholder="Describa el motivo de la reversión..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmarReversalIndividual()">
                    <i class="fas fa-check"></i> Confirmar Reversión
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let pagoIdActual = null;
let tablaIndividual = null;

function abrirModalReversal(pagoId, monto, cliente) {
    pagoIdActual = pagoId;
    document.getElementById('montoDisplay').textContent = monto;
    document.getElementById('clienteNombre').textContent = cliente;
    document.getElementById('motivoTextarea').value = '';
    $('#modalReversalIndividual').modal('show');
}

function confirmarReversalIndividual() {
    const motivo = document.getElementById('motivoTextarea').value.trim();

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
        text: 'Esta acción reversará el pago. ¿Desea continuar?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, reversar',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            realizarReversalIndividual(motivo);
        }
    });
}

function realizarReversalIndividual(motivo) {
    $.ajax({
        url: `/admin/pagos/pago/${pagoIdActual}/reversar-individual`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Content-Type': 'application/json'
        },
        data: JSON.stringify({ motivo: motivo }),
        success: function (response) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: response.message,
            }).then(() => {
                location.reload();
            });
            $('#modalReversalIndividual').modal('hide');
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

    tablaIndividual.column(5).search(
        '^' + fechaInicio + '|' + fechaFin + '$',
        true,
        false,
        true
    ).draw();
}

function limpiarFiltros() {
    document.getElementById('filtroFechaInicio').value = '';
    document.getElementById('filtroFechaFin').value = '';
    tablaIndividual.column(5).search('').draw();
}

// Escapa comillas para insertar texto en el onclick generado.
function escAttr(s) {
    return String(s == null ? '' : s).replace(/\\/g, '\\\\').replace(/'/g, "\\'");
}

// Construye la tabla de detalle (pagos del credito) con un boton Reversar por pago.
function formatoDetallePagos(pagos) {
    if (!pagos || !pagos.length) {
        return '<div class="p-2 text-muted">Sin pagos.</div>';
    }
    let filas = pagos.map(function (p) {
        let montoTxt = 'S/. ' + Number(p.monto).toFixed(2);
        let fecha = p.fecha ? p.fecha.split('-').reverse().join('/') : '';
        return '<tr>' +
            '<td>#' + p.id + '</td>' +
            '<td>Cuota ' + p.cuota + '</td>' +
            '<td>' + montoTxt + '</td>' +
            '<td>' + fecha + '</td>' +
            '<td>' + (p.hora || '') + '</td>' +
            '<td><button class="btn btn-sm btn-danger" onclick="abrirModalReversal(' +
                p.id + ", '" + escAttr(montoTxt) + "', '" + escAttr(p.cliente) + "')\">" +
                '<i class="fas fa-trash"></i> Reversar</button></td>' +
            '</tr>';
    }).join('');

    return '<div class="p-2 bg-light">' +
        '<table class="table table-sm table-bordered mb-0">' +
        '<thead><tr>' +
        '<th>ID Pago</th><th>Cuota</th><th>Monto</th><th>Fecha</th><th>Hora</th><th>Acción</th>' +
        '</tr></thead><tbody>' + filas + '</tbody></table></div>';
}

$(document).ready(function () {
    tablaIndividual = $('#tablaPagosIndividual').DataTable({
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
        // Sin orden inicial: respeta el orden del servidor (credito con pago mas reciente arriba).
        "order": [],
        "columnDefs": [
            { "orderable": false, "targets": [0] }
        ],
        // Cabecera separadora por dia (ultimo pago del credito).
        "drawCallback": function () {
            let api = this.api();
            let rows = api.rows({ page: 'current' }).nodes();
            let last = null;
            $(rows).each(function () {
                let dia = $(this).data('dia');
                if (last !== dia) {
                    $(this).before(
                        '<tr class="bg-secondary text-white"><td colspan="6">' +
                        '<i class="fas fa-calendar-day"></i> <strong>' + dia + '</strong></td></tr>'
                    );
                    last = dia;
                }
            });
        }
    });

    // Expandir / contraer el detalle de pagos de cada credito.
    $('#tablaPagosIndividual tbody').on('click', 'td.details-control', function () {
        let tr = $(this).closest('tr');
        let row = tablaIndividual.row(tr);
        let icono = $(this).find('i');

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            icono.removeClass('fa-minus-circle text-danger').addClass('fa-plus-circle text-primary');
        } else {
            let pagos = tr.data('pagos');
            row.child(formatoDetallePagos(pagos)).show();
            tr.addClass('shown');
            icono.removeClass('fa-plus-circle text-primary').addClass('fa-minus-circle text-danger');
        }
    });
});
</script>
@endsection
