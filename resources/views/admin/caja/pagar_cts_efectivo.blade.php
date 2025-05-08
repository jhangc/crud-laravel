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
                                                <td>
                                                    @if ($deposito->estado === 1)
                                                        <span class="badge badge-primary">Pagado</span>
                                                    @else
                                                        <button onclick="depositar('{{ $deposito->id }}')" type="button"
                                                            class="btn bg-warning">Pagar</button>
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


        function depositar(depositoId) {
            // construye la URL con el ID correcto
            const url = `{{ url('admin/cts/pagar-desembolso') }}/${depositoId}`;
            // abre el ticket en otra pestaña
            window.open(url, '_blank');
            // muestra confirmación y redirige al listado
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'Desembolso pagado'
            }).then(() => {
                window.location.href = '{{ url('admin/desembolso-cts') }}';
            });
        }
    </script>
@endsection
