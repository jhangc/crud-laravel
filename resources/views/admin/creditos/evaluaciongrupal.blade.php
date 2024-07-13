@extends('layouts.admin')

@section('content')
<div class="row evaluacion">
    <h3 class="titulo">EVALUACION FINANCIERA</h3>
    <h6><b><span>TIPO DE CREDITO:</span></b> {{ $prestamo->tipo }}</h6>
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
    <h6><b>TOTAL PRESTAMO:</b> S/.{{ $totalprestamo }}</h6>
    <h6><b>CUOTA A EVALUAR:</b> S/.{{ $cuotaprestamo }}</h6>

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


@foreach ($prestamo->clientes as $cliente)
    <div class="col-md-6">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h4 class="card-title">Montos individuales de: {{ $cliente->nombre }} </h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Numero</th>
                            <th>Fecha</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                          @foreach($cuotastodas as $cuota)
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
            </div>
        </div>
    </div>
@endforeach
</div>


@if ($modulo === 'aprobar')
<div class="row" style="text-align:center;">
    <div class="col-md-12 mb-5">

        <div class="form-group">
            <input type="hidden" value="<?= $prestamo->id ?>" id="credito_id">
            <label for="comentarioadministrador">Comentario:</label>
            <textarea name="comentarioadministrador" id="comentarioadministrador" class="form-control" rows="3" style="color: black;" required><?php if (isset($comentarioadministrador) && !empty($comentarioadministrador)) {
                                                                                                                                                    echo htmlspecialchars($comentarioadministrador, ENT_QUOTES, 'UTF-8');
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
            <textarea name="comentario" id="comentario" class="form-control" rows="3" style="color: black;" required><?php if (isset($comentarioasesor) && !empty($comentarioasesor)) {
                                                                                                                            echo htmlspecialchars($comentarioasesor, ENT_QUOTES, 'UTF-8');
                                                                                                                        } ?></textarea>
        </div>
        <button type="button" onclick="confirmarAccion('guardar')" class="btn btn-primary btnprestamo" {{ in_array($prestamo->estado, ['revisado', 'rechazado por sistema']) ? 'disabled' : '' }}>Guardar</button>
        <button type="button" class="btn btn-secondary btnprestamo" onclick="imprimirPDF()">Imprimir</button>


        <a href="{{ url('admin/creditos') }}" class="btn btn-secondary btnprestamo">Cancelar</a>
    </div>
</div>
@endif



<script>

    function verificarCondiciones() {
        var rentabilidadVentas =0;
        var margenVentas = 0;
        

        // if (Math.abs(rentabilidadVentas - margenVentas) > 5 ||
        //     liquidez <= 1 ) {
        //     return 'rechazado por sistema';
        // }

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
            url: '{{ url('/admin/credito')}}/' + accion,
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






{{-- <script>
        function confirmarAccion(accion) {
            const comentario = document.getElementById('comentario').value;
            const creditoid = document.getElementById('credito_id').value;

            $.ajax({
                url: '{{ url('/admin/credito') }}/' + accion,
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
</script> --}}
@endsection