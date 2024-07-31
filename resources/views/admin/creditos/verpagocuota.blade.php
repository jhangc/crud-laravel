@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Cuotas del Crédito #{{ $credito->id }}</h1>
    @if ($credito->categoria == 'grupal')
        <h2>Cuotas Generales</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Cuota</th>
                    <th>Monto</th>
                    <th>Fecha Vencimiento</th>
                    <th>Días de Mora</th>
                    <th>Monto de Mora</th>
                    <th>Monto Total a Pagar</th>
                    <th>Estado</th>
                    <th>Pagadas</th>
                    <th>Pendientes</th>
                    <th>Vencidas</th>
                    <th>Monto Pagado</th>
                    <th>Monto Pendiente</th>
                    <th>Monto Vencido</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cuotasGenerales as $cuota)
                    <tr>
                        <td>{{ $cuota->numero }}</td>
                        <td>{{ number_format($cuota->monto, 2) }}</td>
                        <td>{{ $cuota->fecha }}</td>
                        <td>{{ $cuota->dias_mora }}</td>
                        <td>{{ number_format($cuota->monto_mora, 2) }}</td>
                        <td>{{ number_format($cuota->monto_total_pago_final, 2) }}</td>
                        <td>
                            @if ($cuota->estado == 'pagado')
                                <span class="badge badge-success">Pagado</span>
                            @elseif ($cuota->estado == 'vencida')
                                <span class="badge badge-danger">Vencida</span>
                            @elseif ($cuota->estado == 'pendiente')
                                <span class="badge badge-warning">Pendiente</span>
                            @else
                                <span class="badge badge-info">Parcial</span>
                            @endif
                        </td>
                        <td>{{ $cuota->pagadas }}</td>
                        <td>{{ $cuota->pendientes }}</td>
                        <td>{{ $cuota->vencidas }}</td>
                        <td>S/. {{ number_format($cuota->monto_pagado, 2) }}</td>
                        <td>S/. {{ number_format($cuota->monto_pendiente, 2) }}</td>
                        <td>S/. {{ number_format($cuota->monto_vencido, 2) }}</td>
                        <td>
                            @if ($cuota->estado == 'pagado')
                                {{ $cuota->fecha_pago }}
                                <a href="{{ route('generar.ticket.pagogrupal', ['array' => implode('-', $cuota->ingreso_ids)]) }}" target="_blank" class="btn btn-info">Ver Ticket</a>
                            @elseif ($cuota->estado == 'pendiente' || $cuota->estado == 'vencida' || $cuota->estado == 'parcial')
                                <button class="btn btn-{{ $cuota->estado == 'vencida' ? 'warning' : 'primary' }}" onclick="pagarCuotaGeneral({{ $credito->id }}, '{{ $cuota->fecha }}')">Pagar</button>
                            @elseif ($cuota->estado == 'vencida')
                                <button class="btn btn-{{ $cuota->estado == 'vencida' ? 'warning' : 'primary' }}" onclick="pagarCuotaGeneral({{ $credito->id }}, '{{ $cuota->fecha }}')">Ya venció</button>
                            @else
                                {{ $cuota->fecha_pago }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @foreach ($clientesCredito as $clienteCredito)
        <h3>Cliente: {{ $clienteCredito->clientes->nombre }}</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Cuota</th>
                    <th>Monto</th>
                    <th>Fecha Vencimiento</th>
                    <th>Días de Mora</th>
                    <th>Monto de Mora</th>
                    <th>Monto Total a Pagar</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cuotasPorCliente[$clienteCredito->cliente_id] as $cuota)
                    <tr>
                        <td>{{ $cuota->numero }}</td>
                        <td>{{ number_format($cuota->monto, 2) }}</td>
                        <td>{{ $cuota->fecha }}</td>
                        <td>{{ $cuota->dias_mora }}</td>
                        <td>{{ number_format($cuota->monto_mora, 2) }}</td>
                        <td>{{ number_format($cuota->monto_total_pago_final, 2) }}</td>
                        <td>
                            @if ($cuota->estado == 'pagado')
                                <span class="badge badge-success">Pagado</span>
                            @elseif ($cuota->estado == 'vencida')
                                <span class="badge badge-danger">Vencida</span>
                            @else
                                <span class="badge badge-warning">Pendiente</span>
                            @endif
                        </td>
                        <td>
                            @if ($cuota->estado == 'pagado')
                                {{ $cuota->fecha_pago }}
                                <a href="{{ route('generar.ticket.pago', ['id' => $cuota->ingreso_id]) }}" target="_blank" class="btn btn-info">Ver Ticket</a>
                            @elseif ($cuota->estado == 'pendiente' || $cuota->estado == 'vencida')
                                <button class="btn btn-{{ $cuota->estado == 'vencida' ? 'warning' : 'primary' }}" onclick="pagarCuota({{ $credito->id }}, {{ $clienteCredito->cliente_id }}, {{ $cuota->id }}, {{ $cuota->numero }}, {{ $cuota->monto_total_pago_final }}, {{ $cuota->monto }}, {{ $cuota->dias_mora }}, {{ $cuota->porcentaje_mora }})">Pagar</button>
                            @else
                                {{ $cuota->fecha_pago }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function pagarCuota(prestamo_id, cliente_id, cronograma_id, numero_cuota, monto_total_pago_final, monto, dias_mora, porcentaje_mora) {
    Swal.fire({
        title: '¿Está seguro?',
        text: `Está a punto de pagar la cuota #${numero_cuota} por un monto de S/. ${monto_total_pago_final.toFixed(2)}.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, pagar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route('creditos.pagocuota') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    prestamo_id: prestamo_id,
                    cliente_id: cliente_id,
                    cronograma_id: cronograma_id,
                    numero_cuota: numero_cuota,
                    monto: monto_total_pago_final,
                    monto_mora: (monto_total_pago_final - monto).toFixed(2),
                    dias_mora: dias_mora,
                    porcentaje_mora: porcentaje_mora
                },
                success: function(response) {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: response.success,
                        icon: 'success'
                }).then(() => {
                    window.open('/admin/generar-ticket-pago/' + response.ingreso_id, '_blank');
                    location.reload();
                    });
                },
                error: function(response) {
                    Swal.fire({
                        title: 'Error',
                        text: response.responseJSON.error,
                        icon: 'error'
                    });
                }
            });
        }
    });
}

function pagarCuotaGeneral(prestamo_id, fecha) {
    Swal.fire({
        title: '¿Está seguro?',
        text: 'Está a punto de pagar todas las cuotas vencidas en la fecha ' + fecha + '.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, pagar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route('creditos.pagogrupal') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    prestamo_id: prestamo_id,
                    fecha: fecha
                },
                success: function(response) {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: response.success,
                        icon: 'success'
                    }).then(() => {
                        var ids = response.ingreso_ids.join('-');
                        window.open('/admin/generar-ticket-pagogrupal/' + ids, '_blank');
                        location.reload();
                    });
                },
                error: function(response) {
                    Swal.fire({
                        title: 'Error',
                        text: response.responseJSON.error,
                        icon: 'error'
                    });
                }
            });
        }
    });
}
</script>
@endsection
