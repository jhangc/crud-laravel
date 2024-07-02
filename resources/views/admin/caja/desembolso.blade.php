@extends('layouts.admin')

@section('content')
<div class="row evaluacion justify-content-center ">
    <h3 class="titulo" style="margin-bottom: 20px;">DATOS GENERALES DEL CREDITO</h3>
    <div class="col-md-4">
        <h6><b><span>TIPO DE CREDITO:</span></b> {{ $prestamo->tipo }}</h6>
        <h6><b>PRODUCTO:</b> {{ $prestamo->producto }}</h6>
        <h6><b>DESTINO:</b> {{ $prestamo->destino }}</h6>
    </div>

    <div class="col-md-4">
        <h6><b>CLIENTES:</b>
            @foreach ($prestamo->clientes as $cliente)
            {{ $cliente->nombre }}@if (!$loop->last), @endif
            @endforeach
        </h6>
        <h6><b>RESPONSABLE:</b> {{ $responsable->name }}</h6>
    </div>
    <div class="col-md-4">
        <h6><b>ACTIVIDAD:</b> {{ $prestamo->descripcion_negocio }}</h6>
        <h6><b>TOTAL PRESTAMO:</b> S/.{{ $prestamo->monto_total }}</h6>
    </div>
</div>

<div class="row evaluacion">
    <h3 class="titulo">GENERAR DOCUMENTOS</h3>
</div>
<div class="row justify-content-center">
    <div class="col-md-4 text-center mb-3">
        <button type="button" class="btn btn-danger btn-block" onclick="imprimirPDF()">Generar Cronograma</button>
    </div>
    <div class="col-md-4 text-center mb-3">
        <button type="button" class="btn btn-primary btn-block" onclick="generarDocumento('documento')">Generar Documento</button>
    </div>
    <!-- <div class="col-md-4 text-center mb-3">
        <button type="button" class="btn btn-primary btn-block" onclick="generarDocumento('ejemplo')">Ejemplo</button>
    </div> -->
</div>

<div class="row evaluacion">
    <h3 class="titulo">SUBIR DOCUMENTOS</h3>

</div>

<div class="row justify-content-center">
    <div class="col-md-6">
    <form action="" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="documento">Selecciona un documento (PDF, Word, Imagen):</label>
                <div class="input-group">
                    <input type="file" class="form-control" id="documento" name="documento" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif" required>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Subir Documento</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>



<div class="row" style="text-align:center;">
    <div class="col-md-12 mb-5">

        <button type="button" class="btn btn-primary btnprestamo">Realizar desembolso</button>
        <a href="{{ url('admin/caja/pagarcredito') }}" class="btn btn-secondary btnprestamo">Cancelar</a>
    </div>
</div>


<script>
function imprimirPDF() {
    var prestamoId = '{{$prestamo->id}}';
    var url = '{{ url('/generar-cronograma')}}' + '/' + prestamoId;

    // Abre la URL en una nueva pestaña
    window.open(url, '_blank');
}

</script>



@endsection