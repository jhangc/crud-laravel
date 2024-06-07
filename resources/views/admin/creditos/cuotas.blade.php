<div class="container">
    @foreach($clientes as $cliente)
    <h3>Cliente: {{ $cliente->nombre }}</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Número de Cuota</th>
                <th>Fecha de Pago</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cuotas as $cuota)
                @if($cuota->cliente_id == $cliente->id)
                <tr>
                    <td>{{ $cuota->numero }}</td>
                    <td>{{ $cuota->fecha }}</td>
                    <td>{{ $cuota->monto }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    @endforeach
</div>