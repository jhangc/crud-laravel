@extends('layouts.admin')

@section('title', 'Gastos')

@section('page-title', 'Gastos')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="m-0">Gastos de Caja  Realizados</h3>
            </div>
            <div class="col-lg-3">
                <button onclick="agregar()" type="button" class="btn bg-info mt-2 mb-2">
                    <i class="fa fa-plus" aria-hidden="true"></i> Agregar
                </button>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body" id="listar">
                        <table id="tabla" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Monto</th>
                                    <th>Número Documento</th>
                                    <th>Serie Documento</th>
                                    <th>Responsable</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($gastos as $gasto)
                                <tr>
                                    <td>{{ $gasto->monto_gasto }}</td>
                                    <td>{{ $gasto->numero_doc }}</td>
                                    <td>{{ $gasto->serie_doc }}</td>
                                    <td>{{ $gasto->nombre_responsable }}</td>
                                    <td>
                                        <button onclick="editar('{{ $gasto->id }}')" type="button" class="btn bg-warning">Editar</button>
                                        <button onclick="eliminar('{{ $gasto->id }}')" type="button" class="btn bg-danger">Eliminar</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>
                    <div class="card-body col-lg-12" id="registrar" style="display:none;">
                        <form id="gastoForm" name="gastoForm">
                            @csrf
                            <input type="hidden" name="id" id="gasto-id">

                            <div class="form-group">
                                <label for="monto_gasto">Monto del Gasto</label>
                                <input type="number" name="monto_gasto" id="monto_gasto" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="numero_doc">Número del Documento</label>
                                <input type="text" name="numero_doc" id="numero_doc" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="serie_doc">Serie del Documento</label>
                                <input type="text" name="serie_doc" id="serie_doc" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="numero_documento_responsable">Número de Documento del Responsable</label>
                                <input type="text" name="numero_documento_responsable" id="numero_documento_responsable" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="nombre_responsable">Nombre del Responsable</label>
                                <input type="text" name="nombre_responsable" id="nombre_responsable" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="archivo">Archivo</label>
                                <input type="file" name="archivo" id="archivo" class="form-control">
                            </div>

                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <button type="button" class="btn btn-secondary" onclick="cancelar()">Cancelar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tabla').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });

    function agregar() {
        document.getElementById('registrar').style.display = 'block';
        document.getElementById('listar').style.display = 'none';
        document.getElementById('gasto-id').value = '';
        document.getElementById('monto_gasto').value = '';
        document.getElementById('numero_doc').value = '';
        document.getElementById('serie_doc').value = '';
        document.getElementById('numero_documento_responsable').value = '';
        document.getElementById('nombre_responsable').value = '';
        document.getElementById('archivo').value = '';
    }

    function editar(id) {
        document.getElementById('registrar').style.display = 'block';
        document.getElementById('listar').style.display = 'none';

        $.ajax({
            url: '{{ url('/admin/gastos') }}/' + id + '/edit',
            method: 'GET',
            success: function(response) {
                let gasto = response.data;
                document.getElementById('gasto-id').value = gasto.id;
                document.getElementById('monto_gasto').value = gasto.monto_gasto;
                document.getElementById('monto_gasto').setAttribute('disabled', 'disabled');
                document.getElementById('numero_doc').value = gasto.numero_doc;
                document.getElementById('serie_doc').value = gasto.serie_doc;
                document.getElementById('numero_documento_responsable').value = gasto.numero_documento_responsable;
                document.getElementById('nombre_responsable').value = gasto.nombre_responsable;
                document.getElementById('archivo').value = '';
            },
            error: function() {
                alert('Error al cargar los datos');
            }
        });
    }

    function eliminar(id) {
        Swal.fire({
            icon: 'warning',
            title: '¡Alerta!',
            text: '¿Seguro de eliminar este gasto?',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'DELETE',
                    url: '{{ url('/admin/gastos') }}/' + id,
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Gasto eliminado correctamente'
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al eliminar el gasto'
                        });
                    }
                });
            }
        });
    }

    $('#gastoForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var url = '{{ url('/admin/gastos') }}';
        var method ='POST';
        var data = new FormData(this);

        $.ajax({
            type: method,
            url: url,
            data: data,
            contentType: false,
            processData: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Gasto guardado correctamente'
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(response) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al guardar el gasto'
                });
            }
        });
    });

    function cancelar() {
        document.getElementById('registrar').style.display = 'none';
        document.getElementById('listar').style.display = 'block';
    }
</script>
@endsection
