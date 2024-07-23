@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Reporte de Total de Clientes</h1>
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
                            <a href="{{ url('/admin/reportes/clientes/export') }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Exportar a Excel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-striped table-hover">
                    <thead>
                        <tr>
                            <th>
                                <center>Nro</center>
                            </th>
                            <th>
                                <center>Nombres</center>
                            </th>
                            <th>
                                <center>DNI</center>
                            </th>
                            <th>
                                <center>Monto</center>
                            </th>
                            <th>
                                <center>Cuota</center>
                            </th>
                            <th>
                                <center>Tasa</center>
                            </th>
                            <th>
                                <center>Recurrencia</center>
                            </th>
                            <th>
                                <center>Tiempo</center>
                            </th>
                            <th>
                                <center>Responsable</center>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $contador = 0; @endphp

                        @foreach ($creditos as $credito)
                            @foreach ($credito->creditoClientes as $creditoCliente)
                                @php
                                $contador++;
                                @endphp
                                <tr>
                                    <td style="text-align: center">{{ $contador }}</td>
                                    <td>{{ $creditoCliente->clientes->nombre }}</td>
                                    <td>{{ $creditoCliente->clientes->documento_identidad }}</td>
                                    <td>{{ $creditoCliente->monto_indivual }}</td>
                                    <td>{{ $credito->cuota }}</td>
                                    <td>{{ $credito->tasa }}</td>
                                    <td>{{ $credito->recurrencia }}</td>
                                    <td>{{ $credito->tiempo }}</td>
                                    <td><{{ $credito->user->name}}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
