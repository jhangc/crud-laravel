@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Reporte de Total de clientes</h1>
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
                                <center>Dni</center>
                            </th>
                            <th>
                                <center>Teléfono</center>
                            </th>
                            <th>
                                <center>Email</center>
                            </th>
                            {{-- <th>
                                <center>Dirección de Domicilio</center>
                            </th>
                            th>
                                <center>Dirección Laboral</center>
                            </th>
                            <th>
                                <center>Lugar de Nacimiento</center>
                            </th>
                            <th>
                                <center>Fecha de Nacimiento</center>
                            </th> --}}
                            <th>
                                <center>Profesión</center>
                            </th>
                            <th>
                                <center>Estado Civil</center>
                            </th>
                            {{-- <th>
                                <center>Conyugue</center>
                            </th>
                            <th>
                                <center>Dni Conyugue</center>
                            </th> --}}
                            
                        </tr>
                    </thead>
                    <tbody>
                        @php $contador = 0; @endphp

                        @foreach ($clientes as $cliente)
                        @php
                        $contador = $contador + 1;
                        $id = $cliente->id;
                        @endphp
                        <tr>
                            <td style="text-align: center">{{ $contador }}</td>
                            <td>{{ $cliente->nombre }}</td>
                            <td>{{ $cliente->documento_identidad }}</td>
                            <td>{{ $cliente->telefono }}</td>
                            <td>{{ $cliente->email }}</td>
                            {{-- <td>{{ $cliente->direccion }}</td>
                            <td>{{ $cliente->direccion_laboral }}</td>
                            <td>{{ $cliente->lugar_nacimiento }}</td>
                            <td>
                                {{ optional($cliente->fecha_nacimiento)->format('d/m/Y') ?? 'No especificada' }}
                            </td> --}}
                            <td>{{ $cliente->profesion }}</td>
                            <td>{{ $cliente->estado_civil }}</td>
                            {{-- <td>{{ $cliente->conyugue }}</td>
                            <td>{{ $cliente->dni_conyugue }}</td> --}}

                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </table>
            </div>
        </div>
    </div>

@endsection
