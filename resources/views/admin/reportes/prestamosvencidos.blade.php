@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Reporte de Créditos Vencidos</h1>
    </div>

    <div class="col-md-12">
        <div class="card card-outline">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" id="buscar-cliente" class="form-control" placeholder="Buscar cliente...">
                            <button class="btn btn-outline-primary" type="button" id="btn-buscar-cliente"><i class="bi bi-search"></i> Buscar</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-tools float-right">
                            <a href="{{ url('/admin/reportes/credito/exportcreditoactivo') }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Exportar a Excel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
                <div class="table-responsive">
                    <table id="creditosTable" class="table table-bordered table-sm table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nro</th>
                                <th>ID</th>
                                <th>Nombres</th>
                                <th>Negocio</th>
                                <th>Tipo de credito</th>
                                <th>Producto</th>
                                <!-- <th>SubProducto</th>
                                    <th>Destino de credito</th> -->
                                {{-- <th>Intervalo</th>
                                <th>Tasa (%)</th>
                                <th>Tiempo</th> --}}
                                <th>Monto (S/.)</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $contador = 0; @endphp
                            @foreach ($creditos as $credito)
                                @php
                                    $contador++;
                                    $id = $credito->id;
                                @endphp
                                <tr>
                                    <td>{{ $contador }}</td>
                                    <td>{{ $credito->id }}</td>
                                    <td>
                                        @foreach ($credito->clientes as $cliente)
                                            {{ $cliente->nombre }}<br>
                                        @endforeach
                                    </td>
                                    <td>{{ $credito->descripcion_negocio }}</td>
                                    <td>{{ $credito->tipo }}</td>
                                    <td>{{ $credito->producto }}</td>
                                    {{-- <td>{{ $credito->subproducto }}</td>
                                        <td>{{ $credito->destino }}</td> --}}
                                    {{-- <td>{{ $credito->recurrencia }}</td>
                                    <td>{{ $credito->tasa }}</td>
                                    <td>{{ $credito->tiempo }}</td> --}}
                                    <td>{{ $credito->monto_total }}</td>
                                    <td>
                                        @if ($credito->estado == 'pendiente')
                                            <span style="background-color: yellow; padding: 3px 10px; border-radius: 5px;">Pendiente</span>
                                        @elseif($credito->estado == 'revisado')
                                            <span style="background-color: orange; padding: 3px 10px; border-radius: 5px;">Revisado</span>
                                        @elseif($credito->estado == 'rechazado')
                                            <span style="background-color: red; padding: 3px 10px; border-radius: 5px;">Rechazado</span>
                                        @elseif($credito->estado == 'aprobado')
                                            <span style="background-color: green; padding: 3px 10px; border-radius: 5px;">Aprobado</span>
                                        @elseif($credito->estado == 'rechazado por sistema')
                                            <span style="background-color: SkyBlue; padding: 3px 10px; border-radius: 5px;">Rechazado por sistema</span>
                                        @elseif($credito->estado == 'observado')
                                            <span style="background-color: purple; padding: 3px 10px; border-radius: 5px; color: white;">Observado</span>
                                        @elseif($credito->estado == 'pagado')
                                            <span style="background-color: blue; padding: 3px 10px; border-radius: 5px; color: white;">Activo</span>
                                        @elseif($credito->estado == 'terminado')
                                            <span style="background-color: grey; padding: 3px 10px; border-radius: 5px; color: white;">Terminado</span>
                                        @elseif($credito->estado == 'mora')
                                            <span style="background-color: darkred; padding: 3px 10px; border-radius: 5px; color: white;">Mora</span>
                                        @endif
                                    </td>
                                    
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
    </div>

@endsection
