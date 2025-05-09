@extends('layouts.admin')

@section('title', 'Desembolso CTS')
@section('page-title', 'Desembolso CTS')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="m-0">Desembolso CTS</h3>
                </div>
                <div class="col-lg-3">
                    <button onclick="agregar()" type="button" class="btn bg-info mt-2 mb-2">
                        <i class="fa fa-plus" aria-hidden="true"></i> Agregar
                    </button>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body" id="listar">
                            <div class="table-responsive">
                                <table id="tabla" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Usuario</th>
                                            <th>N° Cuenta</th>
                                            <th>Monto</th>
                                            <th>Responsable</th>
                                            <th>Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($depositos as $deposito)
                                            <tr>
                                                <td>{{ $deposito->fecha_deposito }}</td>
                                                <td>{{ $deposito->ctsUsuario->user->name }}</td>
                                                <td>{{ $deposito->ctsUsuario->numero_cuenta }}</td>
                                                <td>{{ $deposito->monto }}</td>
                                                <td>{{ optional($deposito->realizadoPor)->name }}</td>

                                                <td>
                                                    <button onclick="editar('{{ $deposito->id }}')" type="button"
                                                        class="btn bg-warning">Editar</button>
                                                    <button onclick="generarticket('{{ $deposito->id }}')" type="button"
                                                        class="btn bg-danger">Imprimir Ticket</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-body col-lg-12" id="registrar" style="display:none;">
                            <form id="depositoForm" name="depositoForm">
                                @csrf
                                <input type="hidden" name="id" id="deposito-id">

                                <div class="form-group">
                                    <label for="cts_usuario_id">Seleccionar Usuario</label>
                                    <select name="cts_usuario_id" id="cts_usuario_id" class="form-control" required>
                                        <option value="">-- Seleccione --</option>
                                        @foreach ($cuentas as $cuenta)
                                            <option value="{{ $cuenta->id }}">
                                                {{ $cuenta->user->name }} - {{ $cuenta->numero_cuenta }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="monto">Monto</label>
                                    <input type="text" name="monto" id="monto" class="form-control" required>
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
        $(function() {
            $('#tabla').DataTable({
                dom: 'Bfrtip', // Botones + tabla + info + paginación
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel"></i> Exportar a Excel',
                    className: 'btn btn-success mb-3',
                    titleAttr: 'Descargar en formato Excel'
                }],
                paging: true,
                pageLength: 10,
                searching: false,
                info: true,
                lengthChange: false,
                responsive: true, // ¡RESPONSIVE activado!
                order: false,
            });
        });

        // Mostrar form vacío
        window.agregar = () => {
            $('#registrar').show();
            $('#listar').hide();
            $('#deposito-id, #monto, #fecha_deposito, #cts_usuario_id').val('');
        };

        // Cargar para editar
        window.editar = id => {
            $('#registrar').show();
            $('#listar').hide();
            $.getJSON("{{ url('admin/depositos-cts') }}/" + id + "/edit", response => {
                const d = response.data;
                $('#deposito-id').val(d.id);
                $('#monto').val(d.monto);
                $('#cts_usuario_id').val(d.cts_usuario_id);
            });

        };

        // Eliminar
        window.eliminar = id => {
            Swal.fire({
                icon: 'warning',
                title: 'Alerta',
                text: '¿Eliminar este depósito?',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar'
            }).then(r => {
                if (!r.isConfirmed) return;
                $.ajax({
                    url: "{{ route('depositos-cts.destroy', '') }}/" + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: () => location.reload()
                });
            });
        };

        // Guardar o actualizar
        $('#depositoForm').submit(function(e) {
            e.preventDefault();
            const id = $('#deposito-id').val();
            const url = "{{ route('depositos-cts.store') }}";
            const data = new FormData(this);
            if (id) data.append('id', id);

            $.ajax({
                url,
                method: 'POST',
                data,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: () => location.reload(),
                error: () => Swal.fire('Error', 'No se pudo guardar', 'error')
            });
        });

        function generarticket(depositoId) {
            // Construye la URL que genera y streamea el PDF
            const url = `{{ url('admin/cts/depositoticket') }}/${depositoId}`;

            // Abre el comprobante en una nueva pestaña
            window.open(url, '_blank');

            // Muestra confirmación
            Swal.fire({
                icon: 'success',
                title: '¡Ticket generado!',
                text: 'El comprobante se ha abierto en una nueva pestaña.'
            });
        }


        function cancelar() {
            document.getElementById('registrar').style.display = 'none';
            document.getElementById('listar').style.display = 'block';
        }
    </script>
@endsection
