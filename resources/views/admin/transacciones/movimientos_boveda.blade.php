@extends('layouts.admin')

@section('title', 'Movimientos de Bóveda')

@section('page-title', 'Movimientos de Bóveda')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-sm-12">
                <h3 class="m-0">Movimientos de la Bóveda: {{ $boveda->id }}</h3>
            </div>
            <div class="col-lg-3 mt-2">
                <button onclick="agregarMovimiento()" type="button" class="btn bg-info">
                    <i class="fa fa-plus" aria-hidden="true"></i> Registrar Movimiento
                </button>
            </div>
        </div>
        <br>

        <div class="row mb-3">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body bg-primary text-white">
                        <h5 class="card-title">Monto Inicial</h5>
                        <input type="text" class="form-control text-white bg-primary" value="S/. {{ number_format($boveda->monto_inicio, 2) }}" readonly>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body bg-success text-white">
                        <h5 class="card-title">Total Ingresos</h5>
                        <input type="text" class="form-control text-white bg-success" value="S/. {{ number_format($totalIngresos, 2) }}" readonly>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body bg-warning text-white">
                        <h5 class="card-title">Total Egresos</h5>
                        <input type="text" class="form-control text-white bg-warning" value="S/. {{ number_format($totalEgresos, 2) }}" readonly>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body bg-info text-white">
                        <h5 class="card-title">Saldo Actual</h5>
                        <input type="text" class="form-control text-white bg-info" value="S/. {{ number_format($saldoActual, 2) }}" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body table-responsive" id="listarMovimientos">
                        <table id="tablaMovimientos" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tipo</th>
                                    <th>Monto</th>
                                    <th>Número Documento</th>
                                    <th>Serie Documento</th>
                                    <th>Motivo</th>
                                    <th>Usuario</th>
                                    <th>Fecha de Actualización</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($movimientos as $movimiento)
                                <tr class="{{ $movimiento->tipo == 'ingreso' ? 'table-success' : 'table-warning' }}">
                                    <td>{{ $movimiento->id }}</td>
                                    <td>{{ ucfirst($movimiento->tipo) }}</td>
                                    <td>{{ $movimiento->monto }}</td>
                                    <td>{{ $movimiento->numero_documento }}</td>
                                    <td>{{ $movimiento->serie_documento }}</td>
                                    <td>{{ $movimiento->motivo }}</td>
                                    <td>{{ $movimiento->user->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($movimiento->updated_at)->format('d/m/Y h:i A') }}</td>
                                    <td>
                                        <button onclick="editarMovimiento('{{ $movimiento->id }}')" type="button" class="btn bg-warning">Editar</button>
                                        <button onclick="eliminarMovimiento('{{ $movimiento->id }}')" type="button" class="btn bg-danger">Eliminar</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-body col-lg-12" id="registrarMovimiento" style="display:none;">
                        <form id="movimientoForm" name="movimientoForm">
                            @csrf
                            <input type="hidden" name="id" id="movimiento-id">
                            <div class="form-group">
                                <label for="tipo">Tipo</label>
                                <select name="tipo" id="tipo" class="form-control" required>
                                    <option value="ingreso">Ingreso</option>
                                    <option value="egreso">Egreso</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="monto">Monto</label>
                                <input type="number" name="monto" id="monto" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="numero_documento">Número Documento</label>
                                <input type="text" name="numero_documento" id="numero_documento" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="serie_documento">Serie Documento</label>
                                <input type="text" name="serie_documento" id="serie_documento" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="motivo">Motivo</label>
                                <input type="text" name="motivo" id="motivo" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="observaciones">Observaciones</label>
                                <textarea name="observacion" id="observacion" class="form-control"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="archivo">Archivo</label>
                                <input type="file" name="archivo" id="archivo" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <button type="button" class="btn btn-secondary" onclick="cancelarMovimiento()">Cancelar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tablaMovimientos').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": false,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });

    function agregarMovimiento() {
        document.getElementById('registrarMovimiento').style.display = 'block';
        document.getElementById('listarMovimientos').style.display = 'none';
        document.getElementById('movimiento-id').value = '';
        document.getElementById('tipo').value = 'ingreso';
        document.getElementById('monto').value = '';
        document.getElementById('numero_documento').value = '';
        document.getElementById('serie_documento').value = '';
        document.getElementById('motivo').value = '';
        document.getElementById('observacion').value = '';
        document.getElementById('archivo').value = '';
    }

    function editarMovimiento(id) {
        document.getElementById('registrarMovimiento').style.display = 'block';
        document.getElementById('listarMovimientos').style.display = 'none';

        $.ajax({
            url: '{{ url('/admin/boveda') }}/' + '{{ $boveda->id }}' + '/movimientos/' + id + '/edit',
            method: 'GET',
            success: function(response) {
                let movimiento = response.data;
                document.getElementById('movimiento-id').value = movimiento.id;
                document.getElementById('tipo').value = movimiento.tipo;
                document.getElementById('monto').value = movimiento.monto;
                document.getElementById('numero_documento').value = movimiento.numero_documento;
                document.getElementById('serie_documento').value = movimiento.serie_documento;
                document.getElementById('motivo').value = movimiento.motivo;
                document.getElementById('observacion').value = movimiento.observacion;
                document.getElementById('archivo').value = '';
            },
            error: function() {
                alert('Error al cargar los datos');
            }
        });
    }

    function eliminarMovimiento(id) {
        Swal.fire({
            icon: 'warning',
            title: '¡Alerta!',
            text: '¿Seguro de eliminar este movimiento?',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'DELETE',
                    url: '{{ url('/admin/boveda') }}/' + '{{ $boveda->id }}' + '/movimientos/' + id,
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Movimiento eliminado correctamente'
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al eliminar el movimiento'
                        });
                    }
                });
            }
        });
    }

    $('#movimientoForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var url = '{{ url('/admin/boveda') }}/' + '{{ $boveda->id }}' + '/movimientos';
        var method = 'POST';

        if ($('#movimiento-id').val()) {
            url = '{{ url('/admin/boveda') }}/' + '{{ $boveda->id }}' + '/movimientos/' + $('#movimiento-id').val();
            method = 'POST';
        }

        $.ajax({
            type: method,
            url: url,
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Movimiento guardado correctamente'
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(response) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al guardar el movimiento'
                });
            }
        });
    });

    function cancelarMovimiento() {
        document.getElementById('registrarMovimiento').style.display = 'none';
        document.getElementById('listarMovimientos').style.display = 'block';
    }
</script>
@endsection
