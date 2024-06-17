@extends('layouts.admin')

@section('content')
    <div class="row evaluacion">
        <h3 class="titulo">EVALUACION FINANCIERA AGRICOLA</h3>
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
    </div>

    <div class="row" style="text-align:center;">
        <div class="col-md-12 mb-5">
            <button type="button" class="btn btn-primary btnprestamo">Aprobar</button>
            <button type="button" class="btn btn-warning btnprestamo">Rechazar</button>
        </div>
    </div>
@endsection
