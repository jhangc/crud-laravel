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
                    {{ $cliente->nombre }}@if (!$loop->last)
                        ,
                    @endif
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
    @if ($prestamo->producto != 'grupal')
        <div class="row justify-content-center">
            <div class="col-md-4 text-center mb-3">
                <button type="button" class="btn btn-danger btn-block" onclick="cronogramaindividualPDF()">Generar Cronograma</button>
            </div>
            <div class="col-md-4 text-center mb-3">
                <button type="button" class="btn btn-primary btn-block" onclick="generarcontratoindividualPDF()">Generar
                    Contrato</button>
            </div>
            <div class="col-md-4 text-center mb-3">
                <button type="button" class="btn btn-primary btn-block" onclick="generarpagarePDF()">Generar
                    Pagaré</button>
            </div>
        </div>
    @else
        <div class="row justify-content-center">
            <div class="col-md-4 text-center mb-3">
                <button type="button" class="btn btn-danger btn-block" onclick="cronogramagrupalPDF()">Generar
                    Cronograma</button>
            </div>
            <div class="col-md-4 text-center mb-3">
                <button type="button" class="btn btn-primary btn-block" onclick="generarcontratogrupal()">Generar Contrato
                    Grupal</button>
            </div>
            <div class="col-md-4 text-center mb-3">
                <button type="button" class="btn btn-primary btn-block" onclick="generarcartilla()">Generar
                    Cartilla</button>
            </div>
        </div>
    @endif





    <div class="row" style="text-align:center;">
        <div class="col-md-12 mb-5">

            <button onclick="depositar()" type="button" class="btn btn-primary btnprestamo">Realizar desembolso</button>
            <a href="{{ url('admin/caja/pagarcredito') }}" class="btn btn-secondary btnprestamo">Cancelar</a>
        </div>
    </div>


    <script>
        function depositar() {

            var prestamoId = '{{ $prestamo->id }}';
            var url = "{{ url('/generar-ticket-desembolso') }}" + '/' + prestamoId;
            window.open(url, '_blank');
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'Prestamo Pagado'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ url('admin/caja/pagarcredito') }}';
                }
            });
        }

        function imprimirPDF() {
            var prestamoId = '{{ $prestamo->id }}';
            var url = "{{ url('/generar-cronograma') }}" + '/' + prestamoId;

            // Abre la URL en una nueva pestaña
            window.open(url, '_blank');
        }


        function cronogramaindividualPDF() {
            var prestamoId = '{{ $prestamo->id }}';
            var url = "{{ url('/generar-cronogramaindividual') }}" + '/' + prestamoId;
            // Abre la URL en una nueva pestaña
            window.open(url, '_blank');
        }

        function cronogramagrupalPDF() {
            var prestamoId = '{{ $prestamo->id }}';
            var url = "{{ url('/generar-cronogramagrupal') }}" + '/' + prestamoId;


            // Abre la URL en una nueva pestaña
            window.open(url, '_blank');
        }

        function generarcontratogrupal() {
            var prestamoId = '{{ $prestamo->id }}';
            var url = "{{ url('/generar-contratogrupal') }}" + '/' + prestamoId;

            // Abre la URL en una nueva pestaña
            window.open(url, '_blank');
        }

        function generarcontratoindividualPDF() {
            var prestamoId = '{{ $prestamo->id }}';
            var url = "{{ url('/generar-contratoindividual') }}" + '/' + prestamoId;

            // Abre la URL en una nueva pestaña
            window.open(url, '_blank');
        }

        function generarcartilla() {
            var prestamoId = '{{ $prestamo->id }}';
            var url = "{{ url('/generar-cartilla') }}" + '/' + prestamoId;


            // Abre la URL en una nueva pestaña
            window.open(url, '_blank');
        }

        function generarpagarePDF() {
            var prestamoId = '{{ $prestamo->id }}';
            var url = "{{ url('/generar-pagare') }}" + '/' + prestamoId;


            // Abre la URL en una nueva pestaña
            window.open(url, '_blank');
        }

        
    </script>
@endsection
