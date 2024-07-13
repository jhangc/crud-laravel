@extends('layouts.admin')

@section('content')
<div class="row evaluacion">
    <h3 class="titulo">EVALUACION FINANCIERA</h3>
    <h6><b>TIPO DE CREDITO:</b> {{ $prestamo->tipo }}</h6>
    <h6><b>PRODUCTO:</b> {{ $prestamo->producto }}</h6>
    <h6><b>DESTINO:</b> {{ $prestamo->destino }}</h6>
    <h6><b>CLIENTES:</b>
        @foreach ($prestamo->clientes as $cliente)
        {{ $cliente->nombre }}@if (!$loop->last),
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
            <h3 class="card-title">Resumen de cuentas</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Cuenta</th>
                        <th>Monto en S/.</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        {{-- activo corriente mas fijo  --}}
                        <td><b>Activo</b></td>
                        <td>{{ $totalactivo }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 20px;">Activo corriente (circulante)</td>
                        <td> {{ $activo_corriente}}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 40px;">Disponible (caja y bancos)</td>
                        <td>{{ $saldo_en_caja_bancos}}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 40px;">Cuentas por cobrar</td>
                        <td>{{ $cuenta_cobrar}}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 40px;">Adelanto a proveedores</td>
                        <td>{{ $adelanto_proveedores}}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 40px;">Inventario</td>
                        <td>{{$totalinventario}}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 20px;">Activo fijo</td>
                        <td>{{ $activofijo }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 40px;">Garantia</td>
                        <td>{{ $totalgarantia }}</td>
                    </tr>
                    <tr>
                        <td><b>Pasivo</b></td>
                        <td>{{ $pasivo }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left: 20px;">Deudas Financieras</td>
                        <td>{{ $totaldeudas }}</td>
                    </tr>

                    <tr>
                        <td><b>Patrimonio neto</b></td>
                        <td>{{ $patrimonioneto }}</td>
                    </tr>
                </tbody>
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
                        <td>Ingresos ventas</td>
                        <td>{{ number_format($totalVentas,2) }}</td>
                    </tr>
                    <tr>
                        <td>Costo de ventas</td>
                        <td>{{ number_format($totalCompras,2) }}</td>
                    </tr>
                    <tr>
                        <td>Utilidad</td>
                        <td>{{ number_format($margensoles,2) }}</td>
                    </tr>
                    <tr>
                        <td>Gastos financieros</td>
                        <td>{{ number_format($totalcuotadeuda,2) }}</td>
                    </tr>
                    <tr>
                        <td>Saldo disponible del negocio</td>
                        <td>{{ number_format($saldo_disponible_negocio,2) }}</td>
                    </tr>
                    <tr>
                        <td>Gastos familiares</td>
                        <td>{{ number_format($totalgastosfamiliares, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Saldo final disponible</td>
                        <td class="{{ $saldo_final <= $cuotaprestamo ? 'text-danger' : '' }}">{{ number_format($saldo_final, 2) }}</td>
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
                        {{-- colocar dle cuadro del excel  --}}
                        <td>Rentabilidad del negocio (%)</td>
                        <td>{{$margenventas}}%</td>
                        <td>Es el margen generico segun actividad</td>
                    </tr>
                    <tr>
                        {{-- División entre saldo total disponible y ventas total --}}
                        <td>Rentabilidad de las ventas (%)</td>
                        <td class="{{ abs($rentabilidad_ventas - $margenventas) > 5 ? 'text-danger' : '' }}">{{ $rentabilidad_ventas }}%</td>
                        <td>tiene que ser +- 5 de la rentabilidad</td>
                    </tr>
                    <tr>
                        {{-- Activo corriente / pasivo corriente --}}
                        <td>Liquidez</td>
                        <td class="{{ $liquidez <= 1 ? 'text-danger' : '' }}">{{ $liquidez }}</td>
                        <td>tiene que ser (>1)</td>
                    </tr>
                    <tr>
                        {{-- Utilidad neta / activos totales --}}
                        <td>ROA (%)</td>
                        <td class="{{ $roa <= 5 ? 'text-danger' : '' }}">{{ $roa }}%</td>
                        <td>tiene que ser (>5%)</td>
                    </tr>
                    <tr>
                        {{-- Activo corriente - pasivo corriente --}}
                        <td>Capital de trabajo (S/.)</td>
                        <td class="{{ $capital_trabajo <= $totalprestamo ? 'text-danger' : '' }}">{{ $capital_trabajo }}</td>
                        <td>tiene que ser mayor al préstamo</td>
                    </tr>
                    <tr>
                        {{-- Utilidad neta / patrimonio neto --}}
                        <td>ROE (%)</td>
                        <td class="{{ $roe <= 10 ? 'text-danger' : '' }}">{{ $roe }}%</td>
                        <td>tiene que ser (>10%)</td>
                    </tr>
                    <tr>
                        {{-- Pasivo total / patrimonio neto --}}
                        <td>Solvencia</td>
                        <td class="{{ $solvencia > 1 ? 'text-danger' : '' }}">{{ $solvencia }}</td>
                        <td>tiene que ser (<=1)</td>
                    </tr>
                    <tr>
                        {{-- Pasivo total / activo total --}}
                        <td>Índice de endeudamiento</td>
                        <td class="{{ $indice_endeudamiento > 40 ? 'text-danger' : '' }}">{{ $indice_endeudamiento }}%</td>
                        <td>tiene que ser (<=40%)</td>
                    </tr>
                    <tr>
                        {{-- Cuota de préstamo / saldo final --}}
                        <td>Cuota excedente</td>
                        <td class="{{ $cuotaexcedente >= 1 ? 'text-danger' : '' }}">{{ $cuotaexcedente }}</td>
                        <td>tiene que ser (<1)</td>
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
            <textarea name="comentarioadministrador" id="comentarioadministrador" class="form-control" rows="3" style="color: black;" required><?php if (isset($comentarioasesor) && !empty($comentarioasesor)) {
                                                                                                                                                    echo htmlspecialchars($comentarioasesor, ENT_QUOTES, 'UTF-8');
                                                                                                                                                } ?></textarea>
        </div>
        <button type="button" onclick="confirmarAccion('aprobar')" class="btn btn-primary btnprestamo">Aprobar</button>
        <button type="button" onclick="confirmarAccion('observar')" class="btn btn-warning btnprestamo">Observar</button>
        <button type="button" onclick="confirmarAccion('rechazar')" class="btn btn-danger btnprestamo">Rechazar</button>
        <a href="{{ url('admin/creditos') }}" class="btn btn-secondary btnprestamo">Cancelar</a>
    </div>
</div>
@else
<div class="row" style="text-align:center;">
    <div class="col-md-12 mb-5">
        <div class="form-group">
            <input type="hidden" value="<?= $prestamo->id ?>" id="credito_id">
            <label for="comentario">Comentario:</label>
            <textarea name="comentario" id="comentario" class="form-control" rows="3" style="color: black;" required><?php if (isset($comentarioadministrador) && !empty($comentarioadministrador)) {
                                                                                                                            echo htmlspecialchars($comentarioadministrador, ENT_QUOTES, 'UTF-8');
                                                                                                                        } ?></textarea>
        </div>
        <button type="button" onclick="confirmarAccion('guardar')" class="btn btn-primary btnprestamo" {{ in_array($prestamo->estado, ['revisado', 'rechazado por sistema']) ? 'disabled' : '' }}>Guardar</button>
        <button type="button" class="btn btn-secondary btnprestamo" onclick="imprimirPDF()">Imprimir</button>

        <a href="{{ url('admin/creditos') }}" class="btn btn-secondary btnprestamo">Cancelar</a>
    </div>
</div>
@endif



<script>
    function imprimirPDF() {
        var prestamoId = '{{$prestamo->id}}';
        var url = '{{ url('/generar-pdf')}}' + '/' + prestamoId;

        // Abre la URL en una nueva pestaña
        window.open(url, '_blank');
    }
    function verificarCondiciones() {
    var rentabilidadVentas = parseFloat('{{ $rentabilidad_ventas }}');
    var margenVentas = parseFloat('{{ $margenventas }}');
    var liquidez = parseFloat('{{ $liquidez }}');
    var roa = parseFloat('{{ $roa }}');
    var capitalTrabajo = parseFloat('{{ $capital_trabajo }}');
    var totalPrestamo = parseFloat('{{ $totalprestamo }}');
    var roe = parseFloat('{{ $roe }}');
    var solvencia = parseFloat('{{ $solvencia }}');
    var indiceEndeudamiento = parseFloat('{{ $indice_endeudamiento }}');
    var cuotaExcedente = parseFloat('{{ $cuotaexcedente }}');
    var saldoFinal = parseFloat('{{ $saldo_final }}');
    var cuotaprestamo = parseFloat('{{ $cuotaprestamo }}');

    if (Math.abs(rentabilidadVentas - margenVentas) > 5 ||
        liquidez <= 1 ||
        roa <= 5 ||
        capitalTrabajo <= totalPrestamo ||
        roe <= 10 ||
        solvencia > 1 ||
        indiceEndeudamiento > 40 ||
        cuotaExcedente >= 1 ||
        saldoFinal <= cuotaprestamo) {
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
        url: '{{ url('/admin/credito')}}/'+accion,
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