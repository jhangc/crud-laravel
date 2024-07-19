@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Cuotas del Crédito #{{ $credito->id }}</h1>
    @foreach ($clientesCredito as $clienteCredito)
        <h3>Cliente: {{ $clienteCredito->clientes->nombre }}</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Cuota</th>
                    <th>Monto</th>
                    <th>Fecha Vencimiento</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cuotasPorCliente[$clienteCredito->cliente_id] as $cuota)
                    <tr>
                        <td>{{ $cuota->numero }}</td>
                        <td>{{ $cuota->monto }}</td>
                        <td>{{ $cuota->fecha }}</td>
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
                            @if ($cuota->estado == 'pendiente')
                                <button class="btn btn-primary" onclick="pagarCuota({{ $credito->id }}, {{ $clienteCredito->cliente_id }}, {{ $cuota->id }}, {{ $cuota->numero }}, {{ $cuota->monto }})">Pagar</button>
                            @else
                                {{$cuota->fecha_pago}}
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
function pagarCuota(prestamo_id, cliente_id, cronograma_id, numero_cuota, monto) {
    Swal.fire({
        title: '¿Está seguro?',
        text: `Está a punto de pagar la cuota #${numero_cuota} por un monto de S/. ${monto}.`,
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
                    monto: monto
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
</script>
@endsection
