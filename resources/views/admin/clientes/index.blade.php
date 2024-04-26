@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Listado de Clientes</h1>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" id="buscar-cliente" class="form-control"
                                    placeholder="Buscar cliente...">
                                <button class="btn btn-outline-primary" type="button" id="btn-buscar-cliente"><i
                                        class="bi bi-search"></i> Buscar</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card-tools float-right">
                                <a href="{{ url('/admin/clientes/create') }}" class="btn btn-primary"><i
                                        class="bi bi-person-fill-add"></i> Nuevo cliente</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table class="table table-bordered table-sm table-striped table-hover">
                    <thead>
                        <tr>
                            <th><center>Nro</center></th>
                                <th><center>Nombres</center></th>
                                <th><center>Dni</center></th>
                                <th><center>Teléfono</center></th>
                                <th><center>Email</center></th>
                                <th><center>Dirección de Domicilio</center></th>
                                <th><center>Dirección Laboral</center></th>
                                <th><center>Lugar de Nacimiento</center></th>
                                <th><center>Fecha de Nacimiento</center></th>
                                <th><center>Profesión</center></th>
                                <th><center>Estado Civil</center></th>
                                <th><center>Conyugue</center></th>
                                <th><center>Dni Conyugue</center></th>
                                <th><center>Foto</center></th>
                                <th><center>Dni PDF</center></th>
                                <th><center>Acciones</center></th>
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
                                <td>{{ $cliente->direccion }}</td>
                                <td>{{ $cliente->direccion_laboral }}</td>
                                <td>{{ $cliente->lugar_nacimiento }}</td>
                                <td>
                                    {{ optional($cliente->fecha_nacimiento)->format('d/m/Y') ?? 'No especificada' }}
                                </td>
                                <td>{{ $cliente->profesion }}</td>
                                <td>{{ $cliente->estado_civil }}</td>
                                <td>{{ $cliente->conyugue }}</td>
                                <td>{{ $cliente->dni_conyugue }}</td>
                                <td>
                                    @if ($cliente->foto)
                                        <a href="{{ Storage::url($cliente->foto) }}" target="_blank">
                                            <img src="{{ Storage::url($cliente->foto) }}" alt="Foto del Cliente" style="width: 100px; height: auto;">
                                        </a>
                                    @else
                                        Sin Foto
                                    @endif
                                </td>
                                
                                <td> @if ($cliente->dni_pdf)<a href="{{ Storage::url($cliente->dni_pdf) }}" target="_blank">Descargar DNI</a>
                                    @else Sin PDF @endif
                                </td>
                                <td style="text-align:center">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        {{-- <a href="{{ route('clientes.show', $cliente->id) }}" type="button"
                                                class="btn btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a> --}}
                                        <a href="{{ route('clientes.edit', $cliente->id) }}" type="button"
                                            class="btn btn-success"><i class="bi bi-pencil"></i></a>
                                        <form action="{{ route('clientes.destroy', $cliente->id) }}"
                                            onclick="preguntar<?= $id ?>(event)" method="post" id="miFormulario<?= $id ?>">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger"
                                                style="border-radius: 0px 5px 5px 0px"><i class="bi bi-trash"></i></button>
                                        </form>
                                        <script>
                                            function preguntar<?= $id ?>(event) {
                                                event.preventDefault();
                                                Swal.fire({
                                                    title: 'Eliminar registro',
                                                    text: '¿Desea eliminar este registro?',
                                                    icon: 'question',
                                                    showDenyButton: true,
                                                    confirmButtonText: 'Eliminar',
                                                    confirmButtonColor: '#a5161d',
                                                    denyButtonColor: '#270a0a',
                                                    denyButtonText: 'Cancelar',
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        var form = $('#miFormulario<?= $id ?>');
                                                        form.submit();
                                                    }
                                                });
                                            }
                                        </script>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
@endsection

