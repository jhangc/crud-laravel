@extends('layouts.admin')

@section('content')
    <div class="row evaluacion">
        <h3 class="titulo">EVALUACION FINANCIERA CONSUMO</h3>
        <h6><b>TIPO DE CREDITO:</b> {{ $prestamo->tipo }}</h6>
        <h6><b>PRODUCTO:</b> {{ $prestamo->producto }}</h6>
        <h6><b>DESTINO:</b> {{ $prestamo->destino }}</h6>
        <h6><b>CLIENTES:</b>
            @foreach ($prestamo->clientes as $cliente)
                {{ $cliente->nombre }}@if (!$loop->last)
                    ,
                @endif
            @endforeach
        </h6>
        <h6><b>ACTIVIDAD:</b> {{ $prestamo->descripcion_negocio }}</h6>
        <h6><b>RESPONSABLE:</b> {{ $responsable->name }}</h6>
        <h6><b>TOTAL PRESTAMO:</b> {{ $totalprestamo }}</h6>
        <h6><b>CUOTA PRESTAMO:</b> {{ $cuotaprestamo }}</h6>
    </div>

     <div class="row">

        <div class="col-md-6">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Gastos familiares</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Monto en S/.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalGastosFamiliares = 0;
                            @endphp
                            @foreach ($gastosfamiliares as $gasto)
                            @php
                                $subtotal = $gasto->precio_unitario * $gasto->cantidad;
                                $totalGastosFamiliares += $subtotal;
                            @endphp
                            <tr>
                                <td>{{ $gasto->descripcion }}</td>
                                <td>{{ number_format($subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><b><i>Total</i></b></td>
                                <td>{{ number_format($totalgastosfamiliares, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Saldo Total negocio</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Monto en S/.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Ingresos</td>
                                <td>{{ number_format($totalVentas,2) }}</td>
                            </tr>
                            <tr>
                                <td>Egresos familiares</td>
                                <td>{{ number_format($totalCompras,2) }}</td>
                            </tr>
                            {{-- <tr>
                                <td>Utilidad en soles</td>
                                <td>{{ number_format($margensoles,2) }}</td>
                            </tr> --}}
                            {{-- <tr>
                                <td>Utilidad (%)</td>
                                <td>{{$margenporcentaje }}</td>
                            </tr> --}}
                            <tr>
                                <td>Gastos financieros</td>
                                <td>{{ number_format($totalcuotadeuda,2) }}</td>
                            </tr>
                            {{-- <tr>
                                <td>Saldo disponible del negocio</td>
                                <td>{{ number_format($saldo_disponible_negocio,2) }}</td>
                            </tr> --}}
                            {{-- <tr>
                                <td>Gastos familiares</td>
                                <td>{{ number_format($totalgastosfamiliares, 2) }}</td>
                            </tr> --}}
                            <tr>
                                <td>Saldo final disponible</td>
                                <td>{{ number_format($saldo_final,2) }}</td>
                            </tr>


                        </tbody>
                    </table>
                </div>
            </div>
        </div>



        <div class="col-md-6">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Ratios Financieros</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Descripción</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Total Garantia</td>
                                <td>{{ number_format($totalgarantia,2) }}</td>
                            </tr>
                            <tr>
                                <td>Total Saldo de prestamos</td>
                                <td>{{ number_format($totalgastosfinancieros,2) }}</td>
                            </tr>
                            <tr>
                                {{-- PASIVO TOTAL / PATRIMONIO NETO  --}}
                                <td>Solvencia</td>
                                <td>{{ $solvencia}}</td>
                            </tr>
                            {{-- <tr>
                                {{-- PASIVO TOTAL / ACTIVO TOTAL 
                                <td>Indice de endeudamiento</td>
                                <td>{{ $indice_endeudamiento}}</td>
                            </tr> --}}
                            <tr>
                                <td>Cuota de endeudamiento</td>
                                <td>{{  number_format($saldo_final,2)}}</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="text-align:center;">
        <div class="col-md-12 mb-5">
            <div class="form-group">
            <input type="hidden" value="<?=$prestamo->id?>" id="credito_id">
                <label for="comentario">Comentario:</label>
                <textarea name="comentario" id="comentario" class="form-control" rows="3" required></textarea>
            </div>
            <button type="button" onclick="confirmarAccion('aprobar')" class="btn btn-primary btnprestamo">Aprobar</button>
            <button type="button" onclick="confirmarAccion('rechazar')" class="btn btn-warning btnprestamo">Rechazar</button>
        </div>
    </div>

    
    <script>
        function confirmarAccion(accion) {
            var comentario = document.getElementById('comentario').value;
            if (!comentario) {
                alert('El comentario es obligatorio.');
                return;
            }
            var confirmacion = confirm('¿Está seguro que desea ' + (accion === 'aprobar' ? 'aprobar' : 'rechazar') + ' este crédito?');
            if (confirmacion) {
                enviarSolicitud(accion, comentario);
            }
        }

        function enviarSolicitud(accion, comentario) {
            var creditoid = document.getElementById('credito_id').value;
            $.ajax({
                url: '{{ url("/admin/credito") }}/' + accion,
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: creditoid,
                    comentario: comentario
                },
                success: function(response) {
                    alert(response.mensaje);
                    window.location.href = response.redirect;
                },
                error: function(xhr) {
                    console.error(xhr);
                    alert('Ocurrió un error al realizar la acción.');
                }
            });
        }
    </script>
@endsection
