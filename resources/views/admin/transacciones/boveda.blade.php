@extends('layouts.admin')

@section('title', 'Bóvedas')

@section('page-title', 'Bóvedas')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="m-0">Bóvedas</h3>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body table-responsive" id="listarBovedas">
                        <table id="tablaBovedas" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Sucursal</th>
                                    <th>Monto Inicio</th>
                                    <th>Total Ingresos</th>
                                    <th>Total Egresos</th>
                                    <th>Saldo Actual</th>
                                    <th>Fecha de Actualización</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bovedas as $boveda)
                                <tr>
                                    <td>{{ $boveda->id }}</td>
                                    <td>{{ $boveda->sucursal->nombre }}</td>
                                    <td>S/. {{ number_format($boveda->monto_inicio, 2) }}</td>
                                    <td>S/. {{ number_format($boveda->total_ingresos, 2) }}</td>
                                    <td>S/. {{ number_format($boveda->total_egresos, 2) }}</td>
                                    <td>S/. {{ number_format($boveda->saldo_actual, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($boveda->updated_at)->format('d/m/Y h:i A') }}</td>
                                    <td>
                                        <button onclick="editarBoveda('{{ $boveda->id }}')" type="button" class="btn bg-warning">Editar</button>
                                        <a href="{{ route('boveda.movimientos', $boveda->id) }}" class="btn bg-info">Movimientos</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body col-lg-12" id="registrarBoveda" style="display:none;">
                        <form id="bovedaForm" name="bovedaForm">
                            @csrf
                            <input type="hidden" name="id" id="boveda-id">
                            <div class="form-group">
                                <label for="monto_inicio">Monto de Inicio</label>
                                <input type="number" name="monto_inicio" id="monto_inicio" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="fecha_inicio">Fecha de Inicio</label>
                                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <button type="button" class="btn btn-secondary" onclick="cancelarBoveda()">Cancelar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tablaBovedas').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });

    function agregarBoveda() {
        document.getElementById('registrarBoveda').style.display = 'block';
        document.getElementById('listarBovedas').style.display = 'none';
        document.getElementById('boveda-id').value = '';
        document.getElementById('monto_inicio').value = '';
        document.getElementById('fecha_inicio').value = '';
    }

    function editarBoveda(id) {
        document.getElementById('registrarBoveda').style.display = 'block';
        document.getElementById('listarBovedas').style.display = 'none';

        $.ajax({
            url: '{{ url('/admin/boveda') }}/' + id + '/edit',
            method: 'GET',
            success: function(response) {
                let boveda = response.data;
                document.getElementById('boveda-id').value = boveda.id;
                document.getElementById('monto_inicio').value = boveda.monto_inicio;
                document.getElementById('fecha_inicio').value = boveda.fecha_inicio;
            },
            error: function() {
                alert('Error al cargar los datos');
            }
        });
    }

    function eliminarBoveda(id) {
        Swal.fire({
            icon: 'warning',
            title: '¡Alerta!',
            text: '¿Seguro de eliminar esta bóveda?',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'DELETE',
                    url: '{{ url('/admin/boveda') }}/' + id,
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Bóveda eliminada correctamente'
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(response) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al eliminar la bóveda'
                        });
                    }
                });
            }
        });
    }

    $('#bovedaForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var url = '{{ url('/admin/boveda') }}';
        var method = 'POST';

        if ($('#boveda-id').val()) {
            url = '{{ url('/admin/boveda') }}/' + $('#boveda-id').val();
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
                    text: 'Bóveda guardada correctamente'
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(response) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al guardar la bóveda'
                });
            }
        });
    });

    function cancelarBoveda() {
        document.getElementById('registrarBoveda').style.display = 'none';
        document.getElementById('listarBovedas').style.display = 'block';
    }
</script>
@endsection
