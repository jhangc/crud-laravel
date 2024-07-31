@extends('layouts.admin')

@section('title', 'Ingresos Extras')

@section('page-title', 'Ingresos Extras')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="m-0">Ingresos a Caja Diarios</h3>
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
                                    <th>Motivo</th>
                                    <th>Número Documento</th>
                                    <th>Serie Documento</th>
                                    <th>Observaciones</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($ingresosExtras as $ingresoExtra)
                                <tr>
                                    <td>{{ $ingresoExtra->monto }}</td>
                                    <td>{{ $ingresoExtra->motivo }}</td>
                                    <td>{{ $ingresoExtra->numero_documento }}</td>
                                    <td>{{ $ingresoExtra->serie_documento }}</td>
                                    <td>{{ $ingresoExtra->observaciones }}</td>
                                    <td>
                                        <button onclick="editar('{{ $ingresoExtra->id }}')" type="button" class="btn bg-warning">Editar</button>
                                        <button onclick="eliminar('{{ $ingresoExtra->id }}')" type="button" class="btn bg-danger">Eliminar</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>
                    <div class="card-body col-lg-12" id="registrar" style="display:none;">
                        <form id="ingresoExtraForm" name="ingresoExtraForm">
                            @csrf
                            <input type="hidden" name="id" id="ingreso-extra-id">

                            <div class="form-group">
                                <label for="monto">Monto del Ingreso</label>
                                <input type="number" name="monto" id="monto" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="motivo">Motivo</label>
                                <input type="text" name="motivo" id="motivo" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="numero_documento">Número del Documento</label>
                                <input type="text" name="numero_documento" id="numero_documento" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="serie_documento">Serie del Documento</label>
                                <input type="text" name="serie_documento" id="serie_documento" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="observaciones">Observaciones</label>
                                <textarea name="observaciones" id="observaciones" class="form-control" rows="3"></textarea>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        document.getElementById('ingreso-extra-id').value = '';
        document.getElementById('monto').value = '';
        document.getElementById('motivo').value = '';
        document.getElementById('numero_documento').value = '';
        document.getElementById('serie_documento').value = '';
        document.getElementById('observaciones').value = '';
        document.getElementById('archivo').value = '';
    }

    function editar(id) {
        document.getElementById('registrar').style.display = 'block';
        document.getElementById('listar').style.display = 'none';

        $.ajax({
            url: '{{ route('ingresos-extras.edit', '') }}/' + id,
            method: 'GET',
            success: function(response) {
                let ingresoExtra = response.data;
                document.getElementById('ingreso-extra-id').value = ingresoExtra.id;
                document.getElementById('monto').value = ingresoExtra.monto;
                document.getElementById('monto').setAttribute('disabled', 'disabled');
                document.getElementById('motivo').value = ingresoExtra.motivo;
                document.getElementById('numero_documento').value = ingresoExtra.numero_documento;
                document.getElementById('serie_documento').value = ingresoExtra.serie_documento;
                document.getElementById('observaciones').value = ingresoExtra.observaciones;
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
            text: '¿Seguro de eliminar este ingreso extra?',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'DELETE',
                    url: '{{ route('ingresos-extras.destroy', '') }}/' + id,
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Ingreso extra eliminado correctamente'
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al eliminar el ingreso extra'
                        });
                    }
                });
            }
        });
    }

    $('#ingresoExtraForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var url = '{{ url('/admin/ingresos-extras') }}';
        var method = 'POST';
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
                    text: 'Ingreso extra guardado correctamente'
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(response) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al guardar el ingreso extra'
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
