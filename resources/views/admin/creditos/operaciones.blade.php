@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Iniciar Operaciones</h3>
            </div>
            <div class="card-body">
                @if($activado)
                    <div class="alert">
                        <button onclick="cerrar()" class="btn btn-danger">Cerrar Operaciones</button>
                    </div>
                @else
                    <div class="alert">
                        <button onclick="iniciar()" class="btn btn-primary">Iniciar Operaciones</button>
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
                                    <span class="badge badge-danger">Operaciones Cerradas</span>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function iniciar() {
        fetch("{{ route('inicio_operaciones.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                user_id: '{{ auth()->user()->id }}',
                sucursal_id: '{{ auth()->user()->sucursal_id }}',
                permiso_abierto: true
            })
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Éxito',
                    text: 'Operaciones iniciadas correctamente.',
                    icon: 'success'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: 'Error iniciando operaciones.',
                    icon: 'error'
                });
            }
        });
    }

    function cerrar() {
        fetch("{{ route('inicio_operaciones.close') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                user_id: '{{ auth()->user()->id }}',
                sucursal_id: '{{ auth()->user()->sucursal_id }}',
                permiso_abierto: false
            })
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Éxito',
                    text: 'Operaciones cerradas correctamente.',
                    icon: 'success'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: 'Error cerrando operaciones.',
                    icon: 'error'
                });
            }
        });
    }
</script>
@endsection
