@extends('layouts.admin')

@section('content')
<div class="row">
    <h1>Solicitud de Clientes</h1>
</div>
<hr>
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card-tools float-right">
                            <a href="{{ url('/admin/clientes/create') }}" class="btn btn-primary"><i class="bi bi-person-fill-add"></i> Nuevo cliente</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="tabla-clientes" class="table table-bordered table-sm table-striped table-hover">
                    <thead>
                        <tr>
                            <th>
                                <center>Nro</center>
                            </th>
                            <th>
                                <center>Nombres</center>
                            </th>
                            <th>
                                <center>Dni</center>
                            </th>
                            {{-- <<th>
                                <center>Teléfono</center>
                            </th>
                            <th>
                                <center>Email</center>
                            </th>
                            <th>
                                <center>Dirección de Domicilio</center>
                            </th>
                            th>
                                <center>Dirección Laboral</center>
                            </th>
                            <th>
                                <center>Lugar de Nacimiento</center>
                            </th>
                            <th>
                                <center>Fecha de Nacimiento</center> --}}
                            </th>
                            <th>
                                <center>Profesión</center>
                            </th>
                            <th>
                                <center>Estado Civil</center>
                            </th>
                            <th>
                                <center>Conyugue</center>
                            </th>
                            <th>
                                <center>Dni Conyugue</center>
                            </th>
                            <th>
                                <center>Foto</center>
                            </th>
                            <th>
                                <center>Dni PDF</center>
                            </th>
                            <th>
                                <center>Acciones</center>
                            </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        const token = '{{ csrf_token() }}';
        const baseClientesUrl = '{{ url('/admin/clientes') }}';
        const detalleClienteBaseUrl = '{{ url('/generar-detalle-cliente') }}';
        const fotoBaseUrl = '{{ url('storage/foto') }}';
        const pdfBaseUrl = '{{ url('storage/pdf') }}';

        $('#tabla-clientes').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            pageLength: 25,
            ajax: {
                url: '{{ route('clientes.index') }}',
                type: 'GET'
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { data: 'nombre' },
                { data: 'documento_identidad' },
                { data: 'profesion' },
                { data: 'estado_civil' },
                { data: 'conyugue' },
                { data: 'dni_conyugue' },
                {
                    data: 'has_foto',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        if (!data) return 'Sin Foto';
                        return '<a href="' + fotoBaseUrl + '/' + row.id + '" target="_blank">' +
                            '<img src="' + fotoBaseUrl + '/' + row.id + '" alt="Foto del Cliente" style="width: 50px; height: 50px;">' +
                            '</a>';
                    }
                },
                {
                    data: 'has_dni_pdf',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        if (!data) return 'Sin PDF';
                        return '<a href="' + pdfBaseUrl + '/' + row.id + '" target="_blank">Descargar DNI</a>';
                    }
                },
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return '<div class="btn-group" role="group" aria-label="Acciones">' +
                            '<a href="' + detalleClienteBaseUrl + '/' + data + '" target="_blank" type="button" class="btn btn-info"><i class="bi bi-eye"></i></a>' +
                            '<a href="' + baseClientesUrl + '/' + data + '/edit" type="button" class="btn btn-success"><i class="bi bi-pencil"></i></a>' +
                            '<form action="' + baseClientesUrl + '/' + data + '" method="post" id="miFormulario' + data + '" style="display:inline;">' +
                            '<input type="hidden" name="_token" value="' + token + '">' +
                            '<input type="hidden" name="_method" value="DELETE">' +
                            '<button type="button" onclick="preguntarEliminarCliente(event,' + data + ')" class="btn btn-danger" style="border-radius: 0px 5px 5px 0px"><i class="bi bi-trash"></i></button>' +
                            '</form>' +
                            '</div>';
                    }
                }
            ],
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                zeroRecords: 'No se encontraron registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                infoEmpty: 'Mostrando 0 a 0 de 0 registros',
                infoFiltered: '(filtrado de _MAX_ registros totales)',
                loadingRecords: 'Cargando...',
                processing: 'Procesando...',
                paginate: {
                    first: 'Primero',
                    last: 'Último',
                    next: 'Siguiente',
                    previous: 'Anterior'
                }
            }
        });
    });

    function preguntarEliminarCliente(event, id) {
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
                $('#miFormulario' + id).submit();
            }
        });
    }
</script>
@endsection
