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
      <tr>
        <td>{{ $cuota->numero }}</td>
        <td>{{ $cuota->fecha }}</td>
        <td>{{ $cuota->monto }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>