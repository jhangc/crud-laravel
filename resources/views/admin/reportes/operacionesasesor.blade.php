@extends('layouts.admin')

@section('content')
    @php
        $n0 = fn ($v) => number_format((float) $v, 0, '.', ',');
        $n2 = fn ($v) => number_format((float) $v, 2, '.', ',');
        $mesActual = now()->format('Y-m');
        $mesAnterior = now()->subMonthNoOverflow()->format('Y-m');
        $periodoTexto = $mes
            ? ucfirst($desde->copy()->locale('es')->translatedFormat('F Y'))
            : $desde->format('d/m/Y') . ' al ' . $hasta->format('d/m/Y');
        $filas = [
            ['key' => 'operaciones', 'label' => 'N° OPERACIONES',     'color' => '#4472c4'],
            ['key' => 'grupal',      'label' => 'N° OP. GRUPAL',      'color' => '#ed7d31'],
            ['key' => 'individual',  'label' => 'N° OP. INDIVIDUAL',  'color' => '#a5a5a5'],
        ];
        if ($hayCrediJoya) {
            $filas[] = ['key' => 'credijoya', 'label' => 'N° OP. CREDIJOYA', 'color' => '#70ad47'];
        }
        $filas[] = ['key' => 'monto', 'label' => 'MONTO (S/)', 'color' => '#ffc000'];
        $tipoBadge = ['grupal' => 'badge-warning', 'individual' => 'badge-secondary', 'credijoya' => 'badge-success'];
        $tipoTexto = ['grupal' => 'Grupal', 'individual' => 'Individual', 'credijoya' => 'CrediJoya'];
    @endphp

    <style>
        .oa { --oa-azul: #0b4a6b; color: #34495e; }
        .oa .oa-titulo { font-size: 1.6rem; font-weight: 700; color: var(--oa-azul); margin: 0; }
        .oa .oa-sub { color: #17a2b8; font-weight: 600; }

        .oa .tabla-resumen { margin-bottom: 0; }
        .oa .tabla-resumen th, .oa .tabla-resumen td { vertical-align: middle; text-align: center; font-size: 1rem; }
        .oa .tabla-resumen thead th {
            color: var(--oa-azul);
            font-weight: 800;
            text-transform: uppercase;
            background: #f4f6f9;
            border-top: 4px solid #ffc000;
            white-space: nowrap;
        }
        .oa .tabla-resumen thead th:first-child { background: transparent; border-top-color: transparent; }
        .oa .tabla-resumen td.metrica {
            text-align: left;
            font-weight: 800;
            color: var(--oa-azul);
            white-space: nowrap;
            width: 1%;
        }
        .oa .cuadro { display: inline-block; width: 12px; height: 12px; margin-right: 6px; vertical-align: -1px; }
        .oa .tabla-resumen td { font-weight: 700; font-variant-numeric: tabular-nums; }
        .oa .tabla-resumen .col-total { background: #fff8e1; color: #b45309; }
        .oa .tabla-resumen thead th.col-total { background: #ffc000; color: #fff; border-top-color: #ffc000; }
        .oa .tabla-resumen tr.fila-monto td:not(.metrica) { color: #1e7b3c; }
        .oa .tabla-resumen tr.fila-monto td.col-total { color: #b45309; }

        .oa .table td.num, .oa .table th.num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
    </style>

    <div class="container-fluid oa pt-3 pb-4">

        {{-- ================= Cabecera ================= --}}
        <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
            <div class="mb-2">
                <h1 class="oa-titulo"><i class="bi bi-people"></i> Operaciones por asesor</h1>
                <div class="oa-sub">
                    Desembolsos de {{ $periodoTexto }}
                    @if ($agencia)
                        · Agencia {{ $agencia }}
                    @endif
                    @unless ($esAdmin)
                        <span class="text-muted font-weight-normal">· solo tus operaciones</span>
                    @endunless
                </div>
            </div>
        </div>

        {{-- ================= Filtros ================= --}}
        <div class="card card-outline card-primary mb-3">
            <div class="card-header py-2">
                <h3 class="card-title font-weight-bold text-primary"><i class="bi bi-funnel"></i> Periodo</h3>
            </div>
            <div class="card-body pb-1">
                <form method="GET" action="{{ route('reporte.operacionesasesor') }}">
                    <div class="row">
                        <div class="col-xl-2 col-md-4 col-sm-6 form-group">
                            <label class="small text-muted text-uppercase font-weight-bold mb-1">Mes de desembolso</label>
                            <input type="month" name="mes" value="{{ $mes }}" class="form-control form-control-sm"
                                onchange="this.form.desde.value=''; this.form.hasta.value=''; this.form.submit()">
                        </div>
                        <div class="col-xl-2 col-md-4 col-sm-6 form-group">
                            <label class="small text-muted text-uppercase font-weight-bold mb-1">O desde</label>
                            <input type="date" name="desde" value="{{ $mes ? '' : $desde->toDateString() }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-xl-2 col-md-4 col-sm-6 form-group">
                            <label class="small text-muted text-uppercase font-weight-bold mb-1">Hasta</label>
                            <input type="date" name="hasta" value="{{ $mes ? '' : $hasta->toDateString() }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-xl-2 col-md-4 col-sm-6 form-group d-flex align-items-end">
                            <div class="custom-control custom-switch mb-1">
                                <input type="hidden" name="credijoya" value="0">
                                <input type="checkbox" class="custom-control-input" id="swCJ" name="credijoya" value="1"
                                    @checked($incluirCJ) onchange="this.form.submit()">
                                <label class="custom-control-label" for="swCJ">Incluir CrediJoya</label>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-8 col-sm-12 form-group d-flex align-items-end flex-wrap" style="gap: 6px;">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i> Ver</button>
                            <a href="{{ route('reporte.operacionesasesor', ['mes' => $mesActual, 'credijoya' => $incluirCJ ? 1 : 0]) }}"
                                class="btn btn-sm {{ $mes === $mesActual ? 'btn-info' : 'btn-outline-info' }}">Este mes</a>
                            <a href="{{ route('reporte.operacionesasesor', ['mes' => $mesAnterior, 'credijoya' => $incluirCJ ? 1 : 0]) }}"
                                class="btn btn-sm {{ $mes === $mesAnterior ? 'btn-info' : 'btn-outline-info' }}">Mes anterior</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= Resumen por asesor ================= --}}
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title font-weight-bold" style="color:#0b4a6b;"><i class="bi bi-table"></i> Resumen · {{ $periodoTexto }}</h3>
            </div>
            <div class="card-body">
                @if (empty($asesores))
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i><br>
                        No hay créditos desembolsados en este periodo. Prueba con otro mes o con un rango de fechas.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered tabla-resumen" id="tablaResumen">
                            <thead>
                                <tr>
                                    <th></th>
                                    @foreach ($asesores as $a)
                                        <th>{{ $a['nombre'] }}</th>
                                    @endforeach
                                    @if ($esAdmin)
                                        <th class="col-total">Total agencia</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($filas as $fila)
                                    <tr class="{{ $fila['key'] === 'monto' ? 'fila-monto' : '' }}">
                                        <td class="metrica"><span class="cuadro" style="background: {{ $fila['color'] }};"></span>{{ $fila['label'] }}</td>
                                        @foreach ($asesores as $a)
                                            <td>{{ $fila['key'] === 'monto' ? $n2($a['monto']) : $n0($a[$fila['key']]) }}</td>
                                        @endforeach
                                        @if ($esAdmin)
                                            <td class="col-total">{{ $fila['key'] === 'monto' ? $n2($total['monto']) : $n0($total[$fila['key']]) }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted d-block mt-2">
                        Cuenta los créditos desembolsados (activos, en mora o terminados) según su fecha de desembolso.
                        El asesor es el usuario que registró el crédito.
                        @if ($incluirCJ && !$hayCrediJoya)
                            No hubo operaciones CrediJoya en este periodo.
                        @endif
                    </small>
                @endif
            </div>
        </div>

        {{-- ================= Detalle de operaciones ================= --}}
        @if ($creditos->count())
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold" style="color:#0b4a6b;"><i class="bi bi-list-ul"></i> Detalle de operaciones ({{ $creditos->count() }})</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover table-bordered" id="tablaDetalle">
                            <thead>
                                <tr>
                                    <th>Desembolso</th>
                                    <th>N° crédito</th>
                                    <th>Asesor</th>
                                    <th>Cliente / Grupo</th>
                                    <th>Tipo</th>
                                    <th>Producto</th>
                                    <th class="num">Monto (S/)</th>
                                    <th>Estado</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($creditos as $cr)
                                    <tr>
                                        <td data-order="{{ \Carbon\Carbon::parse($cr->fecha_desembolso)->format('Y-m-d') }}" class="text-info">
                                            {{ \Carbon\Carbon::parse($cr->fecha_desembolso)->format('d/m/Y') }}
                                        </td>
                                        <td class="font-weight-bold" style="color:#0b4a6b;">{{ $cr->id }}</td>
                                        <td>{{ optional($cr->user)->name ?? 'Sin asesor' }}</td>
                                        <td class="text-primary">
                                            @if ($cr->tipo_operacion === 'grupal')
                                                {{ $cr->nombre_prestamo ?: 'Grupo #' . $cr->id }}
                                                <small class="text-muted">({{ $cr->clientes->count() }} integrantes)</small>
                                            @else
                                                {{ optional($cr->clientes->first())->nombre ?? '—' }}
                                            @endif
                                        </td>
                                        <td><span class="badge {{ $tipoBadge[$cr->tipo_operacion] }}">{{ $tipoTexto[$cr->tipo_operacion] }}</span></td>
                                        <td>{{ ucfirst($cr->producto) }}</td>
                                        <td class="num text-success font-weight-bold">{{ $n2($cr->monto_total) }}</td>
                                        <td>
                                            @if ($cr->estado == 'pagado')
                                                <span class="badge badge-primary">Activo</span>
                                            @elseif ($cr->estado == 'mora')
                                                <span class="badge badge-danger">Mora</span>
                                            @else
                                                <span class="badge badge-secondary">Terminado</span>
                                            @endif
                                        </td>
                                        <td class="text-nowrap">
                                            <a href="{{ route('creditos.estadocuenta.ver', $cr->id) }}" class="btn btn-xs btn-info"
                                                title="Ver estado de cuenta"><i class="bi bi-eye"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        $(function() {
            var titulo = 'Operaciones por asesor - {{ $periodoTexto }}';

            if ($('#tablaResumen').length) {
                $('#tablaResumen').DataTable({
                    paging: false, searching: false, ordering: false, info: false,
                    dom: 'Bt',
                    buttons: [{ extend: 'excelHtml5', text: '<i class="bi bi-file-earmark-excel"></i> Exportar resumen a Excel',
                                className: 'btn btn-sm btn-success mb-2', title: titulo }]
                });
            }

            if ($('#tablaDetalle').length) {
                $('#tablaDetalle').DataTable({
                    order: [],
                    pageLength: 25,
                    dom: "<'row'<'col-sm-6'B><'col-sm-6'f>>t<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    buttons: [{ extend: 'excelHtml5', text: '<i class="bi bi-file-earmark-excel"></i> Exportar detalle a Excel',
                                className: 'btn btn-sm btn-success', title: titulo + ' (detalle)',
                                exportOptions: { columns: ':not(:last-child)' } }],
                    language: {
                        sZeroRecords: 'No se encontraron resultados',
                        sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ operaciones',
                        sInfoEmpty: 'Mostrando 0 a 0 de 0 operaciones',
                        sInfoFiltered: '(filtrado de _MAX_)',
                        sSearch: 'Buscar:',
                        oPaginate: { sFirst: 'Primero', sLast: 'Último', sNext: 'Siguiente', sPrevious: 'Anterior' }
                    }
                });
            }
        });
    </script>
@endsection
