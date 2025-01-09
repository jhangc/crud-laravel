@extends('layouts.admin')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('/admin/welcome') }}">Principal</a></li>
                            <li class="breadcrumb-item active">Cuentas Contables</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>Lista de Cuentas Contables</h3>
                        <button class="btn btn-info rounded-pill" onclick="mostrarFormularioNuevaCuenta()">
                            <i class='bx bxs-pencil'></i> Nueva Cuenta
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tablaCuentas" class="table table-hover table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Nivel</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cuentas as $key => $cuenta)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $cuenta->codigo }}</td>
                                        <td>{{ $cuenta->nombre }}</td>
                                        <td>{{ $cuenta->tipo }}</td>
                                        <td>{{ $cuenta->nivel }}</td>
                                        <td>{{ $cuenta->estado }}</td>
                                        <td>
                                            <button class="btn btn-primary btn-xs" onclick="editarCuenta({{ $cuenta->id }})">
                                                <span class='bx bx-pencil'></span> Editar
                                            </button>
                                            <button class="btn btn-danger btn-xs" onclick="eliminarCuenta({{ $cuenta->id }})">
                                                <span class='bx bx-trash'></span> Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal fade" id="modalCuenta" tabindex="-1" aria-labelledby="modalCuentaLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalCuentaLabel">Crear/Editar Cuenta</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="formCuenta">
                                        @csrf
                                        <input type="hidden" id="id" name="id">
                                        <div class="mb-3">
                                            <label for="codigo" class="form-label">Código</label>
                                            <input type="text" class="form-control" id="codigo" name="codigo" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">Nombre</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="tipo" class="form-label">Tipo</label>
                                            <select class="form-control" id="tipo" name="tipo" required>
                                                <option value="Activo">Activo</option>
                                                <option value="Pasivo">Pasivo</option>
                                                <option value="Patrimonio">Patrimonio</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="nivel" class="form-label">Nivel</label>
                                            <input type="number" class="form-control" id="nivel" name="nivel" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="estado" class="form-label">Estado</label>
                                            <select class="form-control" id="estado" name="estado" required>
                                                <option value="1">Activo</option>
                                                <option value="0">Inactivo</option>
                                            </select>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                    <button type="button" class="btn btn-success" onclick="guardarCuenta()">Guardar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function mostrarFormularioNuevaCuenta() {
        document.getElementById('formCuenta').reset();
        document.getElementById('id').value = '';
        new bootstrap.Modal(document.getElementById('modalCuenta')).show();
    }

    function guardarCuenta() {
        const formData = new FormData(document.getElementById('formCuenta'));
        const url = formData.get('id') ? `/cuentas/${formData.get('id')}` : '/cuentas';
        const method = formData.get('id') ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            body: formData,
        }).then(response => response.json())
        .then(data => {
            if (data.state === '0') {
                location.reload();
            } else {
                alert(data.mensaje);
            }
        }).catch(error => {
            alert('Error al guardar la cuenta: ' + error.message);
        });
    }

    function editarCuenta(id) {
        fetch(`/cuentas/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('id').value = data.cuenta.id;
                document.getElementById('codigo').value = data.cuenta.codigo;
                document.getElementById('nombre').value = data.cuenta.nombre;
                document.getElementById('tipo').value = data.cuenta.tipo;
                document.getElementById('nivel').value = data.cuenta.nivel;
                document.getElementById('estado').value = data.cuenta.estado;
                new bootstrap.Modal(document.getElementById('modalCuenta')).show();
            });
    }

    function eliminarCuenta(id) {
        if (confirm('¿Estás seguro de eliminar esta cuenta?')) {
            fetch(`/cuentas/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => response.json())
            .then(data => {
                if (data.state === '0') {
                    location.reload();
                } else {
                    alert(data.mensaje);
                }
            }).catch(error => {
                alert('Error al eliminar la cuenta: ' + error.message);
            });
        }
    }
</script>
@endsection
