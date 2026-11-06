@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Estado de Cuenta</h1>
    </div>

    <div class="col-md-12">
        @include('admin.creditos.partials.filtros', ['accion' => route('reporte.estadocuenta'), 'mostrarResumen' => false])

        <div class="card card-outline card-primary">
            <div class="card-header">
                <p class="mb-0 text-muted">
                    Busca el crédito por número, cliente, DNI o nombre del grupo. "Ver" lo abre en el sistema; "PDF", para imprimir.
                    En créditos grupales puedes ver o imprimir el consolidado del grupo o la hoja de un integrante.
                </p>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="estadoCuentaTable" class="table table-bordered table-sm table-striped table-hover">
                        <thead>
                            <tr>
                                <th>N° Crédito</th>
                                <th>Cliente / Grupo</th>
                                <th>DNI</th>
                                <th>Modalidad</th>
                                <th>Monto (S/)</th>
                                <th>Desembolso</th>
                                <th>Asesor</th>
                                <th>Estado</th>
                                <th>Estado de cuenta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($creditos as $credito)
                                @php
                                    $esGrupal = $credito->categoria === 'grupal';
                                    $integrantes = $credito->creditoClientes->filter(fn ($cc) => $cc->clientes)->unique('cliente_id');
                                @endphp
                                <tr>
                                    <td>{{ $credito->id }}</td>
                                    <td>
                                        @if ($esGrupal)
                                            <strong>{{ $credito->nombre_prestamo ?: 'Grupo #' . $credito->id }}</strong><br>
                                            <small class="text-muted">
                                                {{ $integrantes->map(fn ($cc) => $cc->clientes->nombre)->implode(', ') }}
                                            </small>
                                        @else
                                            {{ optional($integrantes->first())->clientes->nombre ?? '—' }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $integrantes->map(fn ($cc) => $cc->clientes->documento_identidad)->filter()->implode(', ') }}
                                    </td>
                                    <td>{{ $esGrupal ? 'Grupal' : ($credito->categoria === 'credijoya' || $credito->subproducto === 'credijoya' ? 'CrediJoya' : 'Individual') }}</td>
                                    <td class="text-right">{{ number_format((float) $credito->monto_total, 2) }}</td>
                                    <td data-order="{{ $credito->fecha_desembolso ? \Carbon\Carbon::parse($credito->fecha_desembolso)->format('Y-m-d') : '' }}">
                                        {{ $credito->fecha_desembolso ? \Carbon\Carbon::parse($credito->fecha_desembolso)->format('d/m/Y') : '—' }}
                                    </td>
                                    <td>{{ optional($credito->user)->name }}</td>
                                    <td>
                                        @if ($credito->estado == 'pagado')
                                            <span class="badge badge-primary">Activo</span>
                                        @elseif ($credito->estado == 'mora')
                                            <span class="badge badge-danger">Mora</span>
                                        @elseif ($credito->estado == 'terminado')
                                            <span class="badge badge-secondary">Terminado</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('creditos.estadocuenta.ver', $credito->id) }}"
                                            class="btn btn-sm btn-info" title="Ver en el sistema">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                        @if ($esGrupal)
                                            <div class="btn-group">
                                                <a href="{{ route('creditos.estadocuenta', $credito->id) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-info" title="PDF: grupo + una hoja por integrante">
                                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-info dropdown-toggle dropdown-toggle-split"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="sr-only">Más opciones de PDF</span>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" target="_blank"
                                                        href="{{ route('creditos.estadocuenta', ['id' => $credito->id, 'integrantes' => 0]) }}">
                                                        Solo consolidado del grupo
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    @foreach ($integrantes as $cc)
                                                        <a class="dropdown-item" target="_blank"
                                                            href="{{ route('creditos.estadocuenta', ['id' => $credito->id, 'cliente' => $cc->cliente_id]) }}">
                                                            {{ $cc->clientes->nombre }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <a href="{{ route('creditos.estadocuenta', $credito->id) }}" target="_blank"
                                                class="btn btn-sm btn-outline-info" title="Estado de cuenta en PDF">
                                                <i class="bi bi-file-earmark-pdf"></i> PDF
                                            </a>
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

    <script>
        $(function() {
            $('#estadoCuentaTable').DataTable({
                // Respetar el orden del servidor: fecha de desembolso, el más reciente primero.
                order: [],
                pageLength: 25,
                language: {
                    sProcessing: 'Procesando...',
                    sLengthMenu: 'Mostrar _MENU_ registros',
                    sZeroRecords: 'No se encontraron resultados',
                    sEmptyTable: 'No hay créditos desembolsados',
                    sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ créditos',
                    sInfoEmpty: 'Mostrando 0 a 0 de 0 créditos',
                    sInfoFiltered: '(filtrado de _MAX_ créditos)',
                    sSearch: 'Buscar (N°, cliente, DNI, grupo):',
                    oPaginate: { sFirst: 'Primero', sLast: 'Último', sNext: 'Siguiente', sPrevious: 'Anterior' }
                }
            });
        });
    </script>
@endsection
