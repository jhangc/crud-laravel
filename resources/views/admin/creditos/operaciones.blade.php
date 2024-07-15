@extends('layouts.admin')

@section('content')
<div class="row ">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Iniciar Operaciones</h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Permiso Abierto</th>
                            <th>Fecha de Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($autorizaciones as $autorizacion)
                            <tr>
                                <td>{{ $autorizacion->id }}</td>
                                <td>{{ $autorizacion->user->name }}</td>
                                <td>{{ $autorizacion->permiso_abierto ? 'Sí' : 'No' }}</td>
                                <td>{{ $autorizacion->created_at }}</td>
                                <td>
                                    @if($autorizacion->user_id === auth()->user()->id && !$autorizacion->permiso_abierto)
                                        <form action="{{ route('inicio_operaciones.start', $autorizacion->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary">Iniciar Operaciones</button>
                                        </form>
                                    @elseif($autorizacion->permiso_abierto)
                                        <span class="badge badge-success">Operaciones Iniciadas</span>
                                    @else
                                        <span class="badge badge-secondary">Sin Permiso</span>
                                    @endif
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
