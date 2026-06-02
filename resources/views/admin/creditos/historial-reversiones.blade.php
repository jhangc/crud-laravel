@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-history"></i> Historial de Pagos Reversados (Dados de Baja)</h2>
            <hr>
        </div>
    </div>

    {{-- Filtro por crédito (roadmap de un crédito puntual) --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('pagos.historial-reversiones') }}" class="row">
                        <div class="col-md-4">
                            <label><strong>Filtrar por ID de Crédito:</strong></label>
                            <input type="number" name="prestamo_id" class="form-control"
                                   value="{{ $prestamoId }}" placeholder="Ej: 762">
                        </div>
                        <div class="col-md-3 align-self-end">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                        <div class="col-md-3 align-self-end">
                            <a href="{{ route('pagos.historial-reversiones') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-redo"></i> Ver todos
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        @if($prestamoId)
                            Reversiones del Crédito #{{ $prestamoId }}
                        @else
                            Todas las Reversiones
                        @endif
                    </h5>
                    <span>
                        <strong>{{ $reversiones->count() }}</strong> registro(s) |
                        Total reversado: <strong>S/. {{ number_format($totalReversado, 2) }}</strong>
                    </span>
                </div>
                <div class="card-body">
                    @if($reversiones->count() > 0)
                        <div class="table-responsive">
                            <table id="tablaReversiones" class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>ID Pago</th>
                                        <th>Fecha Reversión</th>
                                        <th>Crédito</th>
                                        <th>Tipo</th>
                                        <th>Cliente / Grupo</th>
                                        <th>Cuota #</th>
                                        <th>Monto</th>
                                        <th>Motivo</th>
                                        <th>Detalles</th>
                                        <th>Reversado por</th>
                                        <th>Estado</th>
                                        <th>Restablecimiento</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reversiones as $rev)
                                        <tr>
                                            <td>{{ $rev->id }}</td>
                                            <td><strong>#{{ $rev->ingreso_id }}</strong></td>
                                            <td data-order="{{ \Carbon\Carbon::parse($rev->created_at)->format('Y-m-d H:i:s') }}">
                                                {{ \Carbon\Carbon::parse($rev->created_at)->format('d/m/Y H:i') }}
                                            </td>
                                            <td>
                                                <a href="{{ route('pagos.historial-reversiones', ['prestamo_id' => $rev->prestamo_id]) }}">
                                                    #{{ $rev->prestamo_id }}
                                                </a>
                                            </td>
                                            <td>
                                                @if(optional($rev->prestamo)->categoria === 'grupal')
                                                    <span class="badge badge-primary">Grupal</span>
                                                @else
                                                    <span class="badge badge-info">Individual</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(optional($rev->ingreso)->cliente_id)
                                                    {{ optional(optional($rev->ingreso)->cliente)->nombre ?? 'Integrante' }}
                                                @else
                                                    {{ optional($rev->prestamo)->nombre_prestamo ?? 'Grupo / General' }}
                                                @endif
                                            </td>
                                            <td>{{ optional($rev->ingreso)->numero_cuota ?? '-' }}</td>
                                            <td>S/. {{ number_format($rev->monto, 2) }}</td>
                                            <td>{{ $rev->motivo }}</td>
                                            <td>{{ $rev->detalles }}</td>
                                            <td>{{ optional($rev->usuario)->name ?? 'N/D' }}</td>
                                            <td>
                                                @if($rev->restablecido_at)
                                                    <span class="badge badge-success">Restablecido</span>
                                                @else
                                                    <span class="badge badge-danger">Dado de baja</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($rev->restablecido_at)
                                                    <div><strong>Motivo:</strong> {{ $rev->motivo_restablecimiento }}</div>
                                                    <small class="text-muted">
                                                        Por {{ optional($rev->restablecidoPor)->name ?? 'N/D' }}
                                                        el {{ \Carbon\Carbon::parse($rev->restablecido_at)->format('d/m/Y H:i') }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($rev->restablecido_at)
                                                    <span class="text-success"><i class="fas fa-check"></i> Recuperado</span>
                                                @else
                                                    <button class="btn btn-sm btn-success"
                                                            onclick="abrirModalRestablecer({{ $rev->id }}, {{ $rev->ingreso_id }})">
                                                        <i class="fas fa-undo-alt"></i> Restablecer
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <strong>Sin registros.</strong>
                            @if($prestamoId)
                                Este crédito no tiene pagos reversados.
                            @else
                                Todavía no se ha reversado ningún pago.
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function abrirModalRestablecer(reversionId, ingresoId) {
    Swal.fire({
        title: '¿Restablecer el pago #' + ingresoId + '?',
        text: 'Se recuperará el ingreso y se volverá a sumar a la caja.',
        icon: 'question',
        input: 'textarea',
        inputLabel: 'Motivo del restablecimiento (obligatorio)',
        inputPlaceholder: 'Describa por qué se recupera este pago...',
        inputAttributes: { 'aria-required': true },
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, restablecer',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value || !value.trim()) {
                return 'El motivo es obligatorio';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/admin/pagos/restablecer/' + reversionId,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify({ motivo: result.value }),
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Listo!',
                        text: response.message,
                    }).then(() => location.reload());
                },
                error: function (xhr) {
                    const errorMsg = xhr.responseJSON?.error || 'Error desconocido';
                    Swal.fire({ icon: 'error', title: 'Error', text: errorMsg });
                }
            });
        }
    });
}

$(document).ready(function () {
    $('#tablaReversiones').DataTable({
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
        "order": [[2, "desc"]],
        "columnDefs": [
            { "orderable": false, "searchable": false, "targets": -1 }
        ]
    });
});
</script>
@endsection
