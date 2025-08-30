@extends('layouts.admin')

@section('content')
    {{-- <div class="row">
        <h1>Nuevo Préstamo</h1>
    </div> --}}
    <div class="row">
        {{-- Botones visibles para Admin y Asesor (no CrediJoya) --}}
        @hasanyrole('Administrador|Asesor de creditos')
            <div class="col-md-2 d-flex justify-content-center">
            <button type="button" class="btn btn-primary" onclick="loadContent('comercio')">CREDITO COMERCIO</button>
            </div>
            <div class="col-md-2 d-flex justify-content-center">
            <button type="button" class="btn btn-secondary" onclick="loadContent('servicio')">CREDITO SERVICIO</button>
            </div>
            <div class="col-md-2 d-flex justify-content-center">
            <button type="button" class="btn btn-success" onclick="loadContent('produccion')">CREDITO PRODUCCIÓN EMPRESA</button>
            </div>
            <div class="col-md-2 d-flex justify-content-center">
            <button type="button" class="btn btn-success" onclick="loadContent('agricola')">CREDITO PRODUCCIÓN AGRICOLA</button>
            </div>
            <div class="col-md-2 d-flex justify-content-center">
            <button type="button" class="btn btn-warning" onclick="loadContent('grupal')">CREDITO GRUPAL</button>
            </div>
        @endhasanyrole

        {{-- CREDIJOYA: solo para Cajera --}}
         @hasanyrole('Administrador|Asesor de creditos|| Cajera')
            <div class="col-md-2 d-flex justify-content-center">
            <button type="button" class="btn btn-info" onclick="loadContent('joya')">CREDIJOYA</button>
            </div>
        @endhasanyrole
        </div>

    <br>

    <div id="content" class="mt-4">
        <!-- El contenido se cargará aquí -->
    </div>
    

<script>
    function loadContent(route) {
        $('#content').load('/admin/creditos/' + route);
    }
</script>
@endsection
