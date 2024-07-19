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

        @if ($modulo === 'aprobar')
            <h6><b>COMEMTARIO DEL ANALISTA: </b>{{ $comentarioasesor }}</h6>
        @endif

        @if ($estado === 'rechazado')
            <h6><b>MOTIVO DE RECHAZO: </b>{{ $comentarioadministrador }}</h6>
        @endif

        @if ($estado === 'observado')
            <h6><b>MOTIVO DE OBSERVACIÓN:</b>{{ $comentarioadministrador }}</h6>
        @endif
    </div>

    <div class="row">


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
                                <td>{{ number_format($totalVentas, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Egresos familiares</td>
                                <td>{{ number_format($totalCompras, 2) }}</td>
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
                                <td>{{ number_format($totalcuotadeuda, 2) }}</td>
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
                                <td class="{{ $saldo_final <= $cuotaprestamo ? 'text-danger' : '' }}">
                                    {{ number_format($saldo_final, 2) }}</td>

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
                                <th>resultado esperado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Total Garantia (S/.)</td>
                                <td class="{{ $totalgarantia < $totalprestamo ? 'text-danger' : '' }}">
                                    {{ number_format($totalgarantia, 2) }}</td>
                                <td>tiene que ser mayor o igual al total de crédito</td>
                            </tr>
                            <tr>
                                <td>Total Saldo de prestamos (S/.)</td>
                                <td>{{ number_format($totalgastosfinancieros, 2) }}</td>
                            </tr>
                            <tr>
                                {{-- PASIVO TOTAL / PATRIMONIO NETO --}}
                                <td>Solvencia</td>
                                <td class="{{ $solvencia > 1 ? 'text-danger' : '' }}">{{ $solvencia }}</td>
                                <td>tiene que ser (<=1)< /td>
                            </tr>
                            <tr>
                                <td>Cuota de endeudamiento</td>
                                <td class="{{ $saldo_final <= $cuotaprestamo ? 'text-danger' : '' }}">
                                    {{ number_format($saldo_final, 2) }}</td>
                                <td>tiene que ser mayor a la cuota del crédito</td>
                            </tr>
                            <tr>
                                {{-- Cuota de préstamo / saldo final --}}
                                <td>cuotaexcedente</td>
                                <td class="{{ $cuotaexcedente >= 1 ? 'text-danger' : '' }}">{{ $cuotaexcedente }}</td>
                                <td>tiene que ser <1 </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @if ($modulo === 'aprobar')
        <div class="row" style="text-align:center;">
            <div class="col-md-12 mb-5">

                <div class="form-group">
                    <input type="hidden" value="<?= $prestamo->id ?>" id="credito_id">
                    <label for="comentarioadministrador">Comentario:</label>
                    <textarea name="comentarioadministrador" id="comentarioadministrador" class="form-control" rows="3"
                        style="color: black;" required><?php if (isset($comentarioadministrador) && !empty($comentarioadministrador)) {
                            echo htmlspecialchars($comentarioadministrador, ENT_QUOTES, 'UTF-8');
                        } ?></textarea>
                </div>
                <button type="button" onclick="confirmarAccion('aprobar')"
                    class="btn btn-primary btnprestamo">Aprobar</button>
                <button type="button" onclick="confirmarAccion('observar')"
                    class="btn btn-warning btnprestamo">Observar</button>
                <button type="button" onclick="confirmarAccion('rechazar')"
                    class="btn btn-danger btnprestamo">Rechazar</button>
                <a href="{{ url('admin/creditos') }}" class="btn btn-secondary btnprestamo">Cancelar</a>
            </div>
        </div>
    @else
        <div class="row" style="text-align:center;">
            <div class="col-md-12 mb-5">
                <div class="form-group">
                    <input type="hidden" value="<?= $prestamo->id ?>" id="credito_id">
                    <label for="comentario">Comentario:</label>
                    <textarea name="comentario" id="comentario" class="form-control" rows="3" style="color: black;" required><?php if (isset($comentarioasesor) && !empty($comentarioasesor)) {
                        echo htmlspecialchars($comentarioasesor, ENT_QUOTES, 'UTF-8');
                    } ?></textarea>
                </div>
                <button type="button" onclick="confirmarAccion('guardar')" class="btn btn-primary btnprestamo"
                    {{ in_array($prestamo->estado, ['revisado', 'rechazado por sistema']) ? 'disabled' : '' }}>Guardar</button>
                <button type="button" class="btn btn-secondary btnprestamo" onclick="imprimirPDF()">Imprimir</button>

                <a href="{{ url('admin/creditos') }}" class="btn btn-secondary btnprestamo">Cancelar</a>
            </div>
        </div>
    @endif



    <script>
        function imprimirPDF() {
            var prestamoId = '{{ $prestamo->id }}';
            var url = '{{ url('/generar-pdf') }}' + '/' + prestamoId;

            // Abre la URL en una nueva pestaña
            window.open(url, '_blank');
        }

        function verificarCondiciones() {
            var totalgarantia = parseFloat('{{ $totalgarantia }}');
            var solvencia = parseFloat('{{ $solvencia }}');
            var indiceEndeudamiento = parseFloat('{{ $saldo_final }}');
            var cuotaExcedente = parseFloat('{{ $cuotaexcedente }}');
            var saldoFinal = parseFloat('{{ $saldo_final }}');
            var cuotaprestamo = parseFloat('{{ $cuotaprestamo }}');
            var totalprestamo = parseFloat('{{ $totalprestamo }}');

            if (solvencia > 1 ||
                indiceEndeudamiento <= cuotaprestamo ||
                totalgarantia <= totalprestamo ||
                cuotaExcedente >= 1) {
                return 'rechazado por sistema';
            }

            return 'revisado';
        }

        function confirmarAccion(accion) {
            var comentarioElement = document.getElementById('comentario');
            var comentarioadministradorElement = document.getElementById('comentarioadministrador');

            var comentario = comentarioElement ? comentarioElement.value : null;
            var comentarioadministrador = comentarioadministradorElement ? comentarioadministradorElement.value : null;


            var accionTexto;
            if (accion === 'aprobar') {
                accionTexto = 'aprobar';
            } else if (accion === 'rechazar') {
                accionTexto = 'rechazar';
            } else if (accion === 'observar') {
                accionTexto = 'observar';
            } else if (accion === 'guardar') {
                accionTexto = 'guardar';
            } else {
                return;
            }

            var confirmacion = confirm('¿Está seguro que desea ' + accionTexto + ' este crédito?');
            if (confirmacion) {
                var estado = verificarCondiciones();
                enviarSolicitud(accion, comentario, comentarioadministrador, estado);
            }
        }

        function enviarSolicitud(accion, comentario, comentarioadministrador, estado) {
            var creditoid = document.getElementById('credito_id').value;
            var data = {
                _token: '{{ csrf_token() }}',
                id: creditoid,
                comentario: comentario,
                comentarioadministrador: comentarioadministrador,
                accion: accion,
                estado: estado
            };

            $.ajax({
                url: '{{ url('/admin/credito') }}/' + accion,
                type: 'GET',
                data: data,
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
