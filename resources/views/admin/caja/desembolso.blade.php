@extends('layouts.admin')

@section('content')
<div class="row evaluacion">
    <h3 class="titulo">DATOS GENERALES DEL CREDITO</h3>
    <h6><b><span>TIPO DE CREDITO:</span></b> {{ $prestamo->tipo }}</h6>
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
    <h6><b>TOTAL PRESTAMO:</b> S/.{{ $prestamo->monto_total }}</h6>

</div>

<div class="row evaluacion">
    <h3 class="titulo">GENERAR DOCUMENTOS</h3>

</div>

<div class="row evaluacion">
    <h3 class="titulo">SUBIR DOCUMENTOS</h3>

</div>



<div class="row" style="text-align:center;">
    <div class="col-md-12 mb-5">

        <button type="button" class="btn btn-primary btnprestamo">Realizar desembolso</button>
        <a href="{{ url('admin/caja/pagarcredito') }}" class="btn btn-secondary btnprestamo">Cancelar</a>
    </div>
</div>


@endsection