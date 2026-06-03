@extends('layouts.admin')

@section('content')
    <style>
        .container.flex {
            width: 100%;
            max-width: 1700px;
            padding: 0 15px;
            margin: 0 auto;
        }

        .cuotas-table-wrap {
            width: 100%;
            overflow-x: visible;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #fff;
        }

        .cuotas-table-wrap .table {
            margin-bottom: 0;
        }

        .cuotas-table-wrap .table th,
        .cuotas-table-wrap .table td {
            white-space: normal;
            vertical-align: middle;
        }

        /* El ancho mínimo va en la columna de Acciones (última), no en Estado */
        .action-cell,
        .cuotas-table-wrap .table td:last-child {
            min-width: 200px;
        }
        .estado-cell { white-space: nowrap; }

        .btn-saldar-total {
            font-weight: 600;
        }

        /* Encabezados agrupados por sección para interpretar mejor la info de abonos */
        .cuotas-table-wrap .table thead th {
            vertical-align: middle;
            text-align: center;
            font-size: 0.78rem;
        }
        .cuotas-table-wrap .table td.num,
        .cuotas-table-wrap .table th.num {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }
        .col-grp { border-left: 2px solid #cbd5e1 !important; }
        .grp-mem  { background: #f5f3ff; }   /* Miembros (grupal) */
        .grp-pago { background: #eef8f0; }   /* Pagado */
        .grp-pend { background: #fff5ee; }   /* Pendiente */
        .cuotas-table-wrap .table td small { color: #94a3b8; }

        /* Colores semánticos para que la info no se vea plana */
        .txt-ok     { color: #157347; font-weight: 600; }
        .txt-warn   { color: #b45309; font-weight: 600; }
        .txt-danger { color: #c0392b; font-weight: 600; }
        .txt-info   { color: #1d4ed8; }
        .detalle    { font-size: 0.78rem; line-height: 1.2; }

        /* Días de atraso de la cuota vigente, en grande */
        .dias-atraso-grande {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            line-height: 1;
            margin-top: 4px;
            color: #c0392b;
            font-weight: 800;
        }
        .dias-atraso-grande .num-dias { font-size: 1.9rem; }
        .dias-atraso-grande .lbl-dias {
            font-size: 0.62rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #b45309;
        }
        .cuotas-table-wrap .table thead th.grp-mem  { color: #6d28d9; }
        .cuotas-table-wrap .table thead th.grp-pago { color: #157347; }
        .cuotas-table-wrap .table thead th.grp-pend { color: #b45309; }

        @media (max-width: 1600px) {
            .cuotas-table-wrap {
                overflow-x: auto;
            }

            .cuotas-table-wrap .table th,
            .cuotas-table-wrap .table td {
                white-space: nowrap;
            }
        }

        @media (max-width: 1440px) {
            .cuotas-table-wrap .table th,
            .cuotas-table-wrap .table td {
                font-size: 0.82rem;
                padding: 0.45rem;
            }

            .btn {
                padding: 0.3rem 0.55rem;
            }
        }
    </style>
    <div class="container flex container-large">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <h1 class="mb-3 mb-md-0">Cuotas del Crédito #{{ $credito->id }} </h1>

            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-md-center">
                @role('Administrador|Asesor de creditos')
                    @if (is_null($reprogramacion))
                        {{-- No hay solicitud previa: mostramos el botón --}}
                        <button type="button" class="btn btn-success mb-2 mb-sm-0 mr-sm-2" data-toggle="modal"
                            data-target="#solicitarReprogramacionModal" onclick="cargarDatosReprogramacion()">
                            Solicitar Reprogramación
                        </button>
                    @else
                        {{-- Ya hay una solicitud previa: mostramos estado --}}
                        @switch($reprogramacion->estado)
                            @case('pendiente')
                                <button class="btn btn-warning mb-2 mb-sm-0 mr-sm-2" disabled>
                                    Reprogramacion en revisión
                                </button>
                            @break

                            @case('aprobada')
                                <button type="button" class="btn btn-success mb-2 mb-sm-0 mr-sm-2"
                                    onclick="reprogramarCronograma({{ $reprogramacion->id }})">
                                    Generar Nuevo Cronograma
                                </button>
                            @break

                            @case('generado')
                                <button type="button" class="btn btn-success mb-2 mb-sm-0 mr-sm-2"
                                    onclick="verCronogramaReprogramado({{ $credito->id }})">
                                    Ver Nuevo Cronograma
                                </button>
                            @break

                            @case('rechazada')
                                <button class="btn btn-danger mb-2 mb-sm-0 mr-sm-2" disabled>
                                    Reprogramacion Rechazada
                                </button>
                            @break
                        @endswitch
                    @endif
                @endrole
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#amortizarCapitalModal"
                    onclick="calcularcuotapendiente()">
                    Pago Capital
                </button>
            </div>
        </div>



        @if ($credito->categoria == 'grupal')
            <h2>Cuotas Generales</h2>
            <div class="cuotas-table-wrap">
            <table class="table table-striped table-sm table-hover">
                <thead>
                    <tr>
                        <th rowspan="2">#</th>
                        <th rowspan="2">Vence</th>
                        <th rowspan="2" class="num">Monto</th>
                        <th rowspan="2">Estado</th>
                        <th rowspan="2">Días atraso</th>
                        <th colspan="3" class="grp-mem col-grp">Miembros</th>
                        <th colspan="4" class="grp-pago col-grp">Pagado</th>
                        <th colspan="4" class="grp-pend col-grp">Pendiente</th>
                        <th rowspan="2">Detalle</th>
                        <th rowspan="2">Acciones</th>
                    </tr>
                    <tr>
                        <th class="grp-mem col-grp">Pagadas</th>
                        <th class="grp-mem">Pend.</th>
                        <th class="grp-mem">Venc.</th>
                        <th class="num grp-pago col-grp">Total abonado</th>
                        <th class="num grp-pago">Abono cap.</th>
                        <th class="num grp-pago">Mora pag.</th>
                        <th class="grp-pago">Últ. abono</th>
                        <th class="num grp-pend col-grp">M. pendiente</th>
                        <th class="num grp-pend">M. vencido</th>
                        <th class="num grp-pend">Mora vig.</th>
                        <th class="num grp-pend">Total a pagar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cuotasGenerales as $cuota)
                        <tr>
                            <td>{{ $cuota->numero }}</td>
                            <td>{{ \Carbon\Carbon::parse($cuota->fecha)->format('d/m/Y') }}</td>
                            <td class="num">{{ number_format($cuota->monto, 2) }}</td>
                            <td class="text-center estado-cell">
                                @if ($cuota->estado == 'pagado')
                                    <span class="badge badge-success">Pagado</span>
                                @elseif ($cuota->estado == 'vencida')
                                    <span class="badge badge-danger">Vencida</span>
                                @elseif ($cuota->estado == 'pendiente')
                                    <span class="badge badge-warning">Pendiente</span>
                                @else
                                    <span class="badge badge-info">Parcial</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $diasAtraso = 0;
                                    if ($cuota->estado != 'pagado') {
                                        $vencDA = \Carbon\Carbon::parse($cuota->fecha)->startOfDay();
                                        $hoyDA = \Carbon\Carbon::now()->startOfDay();
                                        $diasAtraso = $hoyDA->greaterThan($vencDA) ? (int) $vencDA->diffInDays($hoyDA) : 0;
                                    }
                                @endphp
                                @if($diasAtraso > 0)
                                    @if(($cuota->ultima ?? 0) == 1)
                                        <div class="dias-atraso-grande"><span class="num-dias">{{ $diasAtraso }}</span><span class="lbl-dias">días de atraso</span></div>
                                    @else
                                        <span class="txt-danger">{{ $diasAtraso }}d</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="col-grp">{{ $cuota->pagadas }}</td>
                            <td>{{ $cuota->pendientes }}</td>
                            <td>{{ $cuota->vencidas }}</td>
                            <td class="num col-grp">S/ {{ number_format($cuota->total_abonado ?? 0, 2) }}</td>
                            <td class="num">S/ {{ number_format($cuota->abono_capital ?? 0, 2) }}</td>
                            <td class="num">S/ {{ number_format($cuota->mora_pagada ?? 0, 2) }}</td>
                            <td>
                                {{ $cuota->fecha_ultimo_abono ?? '-' }}
                                @if(!is_null($cuota->dias_desde_ultimo_abono ?? null))
                                    <br><small>hace {{ $cuota->dias_desde_ultimo_abono }}d</small>
                                @endif
                            </td>
                            <td class="num col-grp">S/ {{ number_format($cuota->monto_pendiente, 2) }}</td>
                            <td class="num">S/ {{ number_format($cuota->monto_vencido, 2) }}</td>
                            <td class="num">S/ {{ number_format($cuota->estado == 'pagado' ? 0 : $cuota->monto_mora, 2) }}
                                @if($cuota->estado != 'pagado' && $cuota->dias_mora)<br><small>mora
                                     {{ $cuota->dias_mora }}d

                                </small>@endif</td>
                            <td class="num"><strong class="{{ ($cuota->estado != 'pagado' && $cuota->monto_total_pago_final > 0) ? 'txt-warn' : '' }}">S/ {{ number_format($cuota->estado == 'pagado' ? 0 : $cuota->monto_total_pago_final, 2) }}</strong></td>
                            <td class="detalle {{ $cuota->estado == 'pagado' ? 'txt-ok' : ($cuota->estado == 'pendiente' ? 'txt-info' : 'txt-danger') }}">{{ $cuota->detalle_estado ?? '-' }}</td>
                            <td>
                                @if ($cuota->estado == 'pagado')
                                    {{ $cuota->fecha_pago }}
                                    <div class="d-flex flex-wrap">
                                        <a href="{{ route('generar.ticket.pagogrupal', ['array' => implode('-', $cuota->ingreso_ids)]) }}"
                                            target="_blank" class="btn btn-info mr-2 mb-2">Ticket Ultimo Pago</a>
                                        <a href="{{ route('generar.ticket.pagogrupal.historial', ['prestamo_id' => $credito->id, 'numero_cuota' => $cuota->numero, 'fecha' => $cuota->fecha]) }}"
                                            target="_blank" class="btn btn-outline-info mr-2 mb-2">Ticket Historial</a>
                                        @if ($cuota->pago_capital != null)
                                            <a href="{{ url('/vernuevocronograma/' . $credito->id) }}" target="_blank"
                                                class="btn btn-warning mb-2">Ver Nuevo Cronograma</a>
                                        @endif
                                    </div>
                                @elseif ($cuota->estado == 'pendiente' || $cuota->estado == 'vencida' || $cuota->estado == 'parcial')
                                    @if ($cuota->ultima == 1)
                                        <button class="btn btn-{{ $cuota->estado == 'parcial' ? 'warning' : 'secondary' }} btn-saldar-total" data-toggle="modal" data-target="#pagarTodoModal"
                                            onclick="pagarTodogrupal({{ $credito->id }}, '{{ $cuota->fecha }}', '{{ $cuota->numero }}')">
                                            {{ $cuota->estado == 'parcial' ? 'SALDAR TODO' : 'PAGAR TODO' }}
                                        </button>
                                    @endif
                                    @if (($cuota->total_abonado ?? 0) > 0)
                                        @if (!empty($cuota->ingreso_ids))
                                            <a href="{{ route('generar.ticket.pagogrupal', ['array' => implode('-', $cuota->ingreso_ids)]) }}"
                                                target="_blank" class="btn btn-info mb-1">Ticket Ultimo Pago</a>
                                        @endif
                                        <a href="{{ route('generar.ticket.pagogrupal.historial', ['prestamo_id' => $credito->id, 'numero_cuota' => $cuota->numero, 'fecha' => $cuota->fecha]) }}"
                                            target="_blank" class="btn btn-outline-info mb-1">Ticket Historial</a>
                                    @endif
                                    <button class="btn btn-info"
                                        onclick="abonarCuotaGeneral({{ $credito->id }}, '{{ $cuota->fecha }}', '{{ $cuota->numero }}', {{ number_format($cuota->monto_total_pago_final, 2, '.', '') }})">Abonar</button>
                                    <button class="btn btn-{{ $cuota->estado == 'vencida' ? 'warning' : 'primary' }}"
                                        onclick="pagarCuotaGeneral({{ $credito->id }}, '{{ $cuota->fecha }}')">Pagar</button>
                                @else
                                    {{ $cuota->fecha_pago }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        @endif

        @foreach ($clientesCredito as $clienteCredito)
            <br><br>
            <h3>Cliente: {{ $clienteCredito->clientes->nombre }}</h3>
            <div class="cuotas-table-wrap">
            <table class="table table-striped table-sm table-hover">
                <thead>
                    <tr>
                        <th rowspan="2">#</th>
                        <th rowspan="2">Vence</th>
                        <th rowspan="2" class="num">Monto cuota</th>
                        <th rowspan="2">Estado</th>
                        <th rowspan="2">Días atraso</th>
                        <th colspan="4" class="grp-pago col-grp">Pagado</th>
                        <th colspan="3" class="grp-pend col-grp">Pendiente</th>
                        <th rowspan="2">Detalle</th>
                        <th rowspan="2">Acciones</th>
                    </tr>
                    <tr>
                        <th class="num grp-pago col-grp">Abono cap.</th>
                        <th class="num grp-pago">Mora pag.</th>
                        <th class="num grp-pago">Total abonado</th>
                        <th class="grp-pago">Últ. abono</th>
                        <th class="num grp-pend col-grp">Saldo</th>
                        <th class="num grp-pend">Mora vig.</th>
                        <th class="num grp-pend">Total a pagar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cuotasPorCliente[$clienteCredito->cliente_id] as $cuota)
                        <tr>
                            <td>{{ $cuota->numero }}</td>
                            <td>{{ \Carbon\Carbon::parse($cuota->fecha)->format('d/m/Y') }}</td>
                            <td class="num">{{ number_format($cuota->monto, 2) }}</td>
                            <td class="text-center estado-cell">
                                @if ($cuota->estado == 'pagado')
                                    <span class="badge badge-success">Pagado</span>
                                @elseif ($cuota->estado == 'parcial')
                                    <span class="badge badge-info">Parcial</span>
                                @elseif ($cuota->estado == 'vencida')
                                    <span
                                        class="badge badge-danger">{{ $cuota->dias_mora > 1 ? 'Vencida' : 'VENCE-HOY' }}</span>
                                @else
                                    <span class="badge badge-warning">Pendiente</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $diasAtraso = 0;
                                    if ($cuota->estado != 'pagado') {
                                        $vencDA = \Carbon\Carbon::parse($cuota->fecha)->startOfDay();
                                        $hoyDA = \Carbon\Carbon::now()->startOfDay();
                                        $diasAtraso = $hoyDA->greaterThan($vencDA) ? (int) $vencDA->diffInDays($hoyDA) : 0;
                                    }
                                @endphp
                                @if($diasAtraso > 0)
                                    @if(($cuota->ultima ?? 0) == 1)
                                        <div class="dias-atraso-grande"><span class="num-dias">{{ $diasAtraso }}</span><span class="lbl-dias">días de atraso</span></div>
                                    @else
                                        <span class="txt-danger">{{ $diasAtraso }}d</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="num col-grp">S/ {{ number_format($cuota->abono_capital ?? 0, 2) }}</td>
                            <td class="num">S/ {{ number_format($cuota->mora_pagada ?? 0, 2) }}</td>
                            <td class="num">S/ {{ number_format($cuota->total_abonado ?? 0, 2) }}</td>
                            <td>
                                {{ $cuota->fecha_ultimo_abono ?? '-' }}
                                @if(!is_null($cuota->dias_desde_ultimo_abono ?? null))
                                    <br><small>hace {{ $cuota->dias_desde_ultimo_abono }}d</small>
                                @endif
                            </td>
                            <td class="num col-grp"><strong class="{{ ($cuota->saldo ?? 0) > 0 ? 'txt-danger' : 'text-muted' }}">S/ {{ number_format($cuota->saldo ?? 0, 2) }}</strong></td>
                            <td class="num">
                                S/ {{ number_format($cuota->estado == 'pagado' ? 0 : $cuota->monto_mora, 2) }}
                                @if($cuota->estado != 'pagado' && $cuota->dias_mora)<br><small>mora {{ $cuota->dias_mora }}d</small>@endif
                            </td>
                            <td class="num"><strong class="{{ ($cuota->estado != 'pagado' && $cuota->monto_total_pago_final > 0) ? 'txt-warn' : '' }}">S/ {{ number_format($cuota->estado == 'pagado' ? 0 : $cuota->monto_total_pago_final, 2) }}</strong></td>
                            <td class="detalle {{ $cuota->estado == 'pagado' ? 'txt-ok' : ($cuota->estado == 'pendiente' ? 'txt-info' : 'txt-danger') }}">{{ $cuota->detalle_estado ?? '-' }}</td>
                            <td>
                                @if ($cuota->estado == 'pagado')
                                    {{ $cuota->fecha_pago }}
                                    <div class="d-flex flex-wrap">
                                        <a href="{{ route('generar.ticket.pago', [
                                            'id' => $cuota->ingreso_id,
                                            'diferencia' => $cuota->diferencia,
                                        ]) }}"
                                            target="_blank" class="btn btn-info mr-2 mb-2">
                                            Ticket Ultimo Pago
                                        </a>
                                        <a href="{{ route('generar.ticket.cuotaindividual.historial', ['prestamo_id' => $credito->id, 'cliente_id' => $clienteCredito->cliente_id, 'numero_cuota' => $cuota->numero, 'fecha' => $cuota->fecha]) }}"
                                            target="_blank" class="btn btn-outline-info mr-2 mb-2">Ticket Historial</a>
                                        @if ($cuota->pago_capital != null)
                                            <a href="{{ url('/vernuevocronograma/' . $credito->id) }}" target="_blank"
                                                class="btn btn-warning mb-2">Ver Nuevo Cronograma</a>
                                        @endif
                                    </div>
                                @elseif ($cuota->estado == 'pendiente' || $cuota->estado == 'vencida' || $cuota->estado == 'parcial')
                                    @if ($cuota->ultima == '1' && $credito->categoria != 'grupal')
                                        <button class="btn btn-{{ $cuota->estado == 'parcial' ? 'warning' : 'secondary' }} btn-saldar-total" data-toggle="modal" data-target="#pagarTodoModal"
                                            onclick="pagarTodoindividual({{ $credito->id }}, '{{ $cuota->fecha }}','{{ $cuota->numero }}')">
                                            {{ $cuota->estado == 'parcial' ? 'SALDAR TODO' : 'PAGAR TODO' }}
                                        </button>
                                    @endif
                                    @if (($cuota->total_abonado ?? 0) > 0)
                                        @if (!empty($cuota->ingreso_id))
                                            <a href="{{ route('generar.ticket.pago', ['id' => $cuota->ingreso_id, 'diferencia' => $cuota->diferencia ?? 0]) }}"
                                                target="_blank" class="btn btn-info mb-1">Ticket Ultimo Pago</a>
                                        @endif
                                        <a href="{{ route('generar.ticket.cuotaindividual.historial', ['prestamo_id' => $credito->id, 'cliente_id' => $clienteCredito->cliente_id, 'numero_cuota' => $cuota->numero, 'fecha' => $cuota->fecha]) }}"
                                            target="_blank" class="btn btn-outline-info mb-1">Ticket Historial</a>
                                    @endif
                                    <button class="btn btn-{{ $cuota->estado == 'vencida' ? 'warning' : ($cuota->estado == 'parcial' ? 'info' : 'primary') }}"
                                        data-toggle="modal" data-target="#modalPagarCuota"
                                        onclick="pagarCuota({{ $credito->id }}, {{ $clienteCredito->cliente_id }}, {{ $cuota->id }}, {{ $cuota->numero }}, {{ $cuota->monto_total_pago_final }}, {{ $cuota->monto }}, {{ $cuota->dias_mora }}, {{ $cuota->porcentaje_mora }})">{{ $cuota->estado == 'parcial' ? 'Abonar' : 'Pagar' }}</button>
                                @else
                                    {{ $cuota->fecha_pago }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        @endforeach


        <div class="modal fade" id="pagarTodoModal" tabindex="-1" role="dialog" aria-labelledby="pagarTodoModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pagarTodoModalLabel">Pagar Todo el Crédito</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Descripción</th>
                                    <th>Monto (S/.)</th>
                                    <th>Dias de Mora</th>
                                    <th>monto de Mora</th>
                                    <th>Total (S/.)</th>
                                </tr>
                            </thead>
                            <tbody id="detallePagoTodo">
                                <!-- Aquí se llenarán los datos dinámicamente -->
                            </tbody>
                        </table>
                        <h4>Total a Pagar: <span id="totalPagarTodo"></span></h4>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="amortizarCapitalModal" tabindex="-1" role="dialog"
            aria-labelledby="amortizarCapitalModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title" id="amortizarCapitalModalLabel">Amortización al Capital</h2>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if ($cuotasVencidas > 0)
                            <div class="alert alert-warning">
                                Tienes cuotas vencidas. Debes pagarlas antes de realizar una amortización al capital.
                            </div>
                        @elseif ($puedeAmortizar < 2)
                            <div class="alert alert-warning">
                                Debes haber pagado al menos 2 cuotas para realizar una amortización al capital.
                            </div>
                        @else
                            <div id='detalleCuotaPendiente'>
                                <form id="amortizarCapitalForm">
                                    @csrf
                                    <input type="hidden" name="prestamo_id" value="{{ $credito->id }}">
                                    <!-- Sección de datos a enviar -->
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Número de Cuota-Actual</label>
                                                <input type="text" class="form-control" id="numero_cuota"
                                                    name="numero_cuota" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Intereses de cuota Actual</label>
                                                <input type="text" class="form-control" id="intereses_a_pagar"
                                                    name="intereses_a_pagar" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Capital Pendiente </label>
                                                <input type="text" class="form-control" id="capital_pendiente"
                                                    name="capital_pendiente" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Max-Total a Pagar</label>
                                                <input type="text" class="form-control" id="total_a_pagar_posible"
                                                    name="total_a_pagar_posible" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div id="opcionesAmortizacion" class="mb-3">
                                        <label class="d-block"><strong>Opciones de Amortización:</strong></label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="opcion_amortizacion"
                                                id="opcionReducirCuota" value="reducir_cuota" checked>
                                            <label class="form-check-label" for="opcionReducirCuota">Reducir Cuota y
                                                Mantener Plazo</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="opcion_amortizacion"
                                                id="opcionReducirPlazo" value="reducir_plazo">
                                            <label class="form-check-label" for="opcionReducirPlazo">Reducir plazo y
                                                Mantener cuota</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="monto_pago_capital">Monto a Pagar ( Pago Capital + Intereses):</label>
                                        <input type="number" class="form-control" name="monto_pago_capital"
                                            id="monto_pago_capital" required>
                                    </div>
                                    <button type="button" class="btn btn-secondary mb-3"
                                        onclick="generarNuevoCronograma()">Ver Nuevo Cronograma</button>

                                    <div id="nuevoCronograma" class="mb-3"></div>
                                    <button type="button" class="btn btn-primary" id="btnAmortizar" disabled
                                        onclick="confirmarpagoCapital()">Amortizar Capital</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Solicitar Reprogramación -->
        <div class="modal fade" id="solicitarReprogramacionModal" tabindex="-1" role="dialog"
            aria-labelledby="solicitarReprogramacionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title">Solicitar Reprogramación del Crédito</h2>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        @if ($cuotasVencidas > 0)
                            <div class="alert alert-warning">
                                Tienes cuotas vencidas. Debes pagarlas antes de reprogramar.
                            </div>
                        @else
                            <form id="solicitarReprogramacionForm">
                                @csrf
                                <input type="hidden" name="credito_id" id="credito_id" value="{{ $credito->id }}">

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label>Cuotas Restantes</label>
                                        <input type="number" id="rep_cuotas_restantes" class="form-control" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Capital Pendiente (S/.)</label>
                                        <input type="text" id="rep_capital_pendiente" class="form-control" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Tasa de Interés (%)</label>
                                        <input type="number" step="0.01" id="tasa_interes" name="tasa_interes"
                                            class="form-control" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Periodo de Pago</label>
                                        <input type="text" id="rep_periodo_pago" class="form-control" disabled>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="cantidad_cuotas">Cuotas a Reprogramar</label>
                                        <select id="cantidad_cuotas" name="cuotas_solicitadas" class="form-control"
                                            required>
                                            <option value="" disabled selected>Selecciona...</option>
                                            @if (in_array($credito->recurrencia, ['quincenal', 'catorcenal']))
                                                @for ($i = 1; $i <= 4; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            @else
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Nueva Cantidad de cuotas</label>
                                        <input type="number" id="cuotas_reprogramacion" class="form-control">
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <label for="observaciones">Observaciones</label>
                                        <textarea id="observaciones" name="observaciones" class="form-control" rows="3"
                                            placeholder="Escribe aquí tu comentario..."></textarea>
                                    </div>
                                </div>

                                <br>
                                <button type="button" class="btn btn-primary"
                                    onclick="confirmarSolicitudReprogramacion()">
                                    Solicitar Reprogramación
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Pago de Cuota -->
        <!-- --- 1. Modal actualizado: -->
        <div class="modal fade" id="modalPagarCuota" tabindex="-1" role="dialog"
            aria-labelledby="modalPagarCuotaLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form id="formPagarCuota">
                    @csrf
                    <!-- Hidden inputs para todas las variables -->
                    <input type="hidden" id="mpc_prestamo_id">
                    <input type="hidden" id="mpc_cliente_id">
                    <input type="hidden" id="mpc_cronograma_id">
                    <input type="hidden" id="mpc_numero_cuota">
                    <input type="hidden" id="mpc_monto_total_hidden">
                    <input type="hidden" id="mpc_monto_base_hidden">
                    <input type="hidden" id="mpc_dias_mora_hidden">
                    <input type="hidden" id="mpc_porcentaje_mora_hidden">

                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalPagarCuotaLabel">
                                Pagar Cuota #<span id="mpc_numero_cuota_text"></span>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Saldo + mora a pagar</label>
                                <!-- solo para mostrar, no se envía directamente -->
                                <input type="text" class="form-control" id="mpc_monto_total_display" disabled>
                            </div>
                            <div class="form-group">
                                <label>Monto a Abonar</label>
                                <input type="number" step="0.01" class="form-control" name="monto_pagado"
                                    id="mpc_monto_pagado" required>
                                <small class="form-text text-muted">
                                    Puedes pagar el total o una parte (abono). Primero se cubre la mora y el resto baja el saldo;
                                    la mora seguirá corriendo sobre el saldo restante.
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Confirmar Pago
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // --- 2. pagarCuota: rellena todos los campos y muestra el modal (sin SweetAlert)
        function pagarCuota(prestamo_id, cliente_id, cronograma_id,
            numero_cuota, monto_total_pago_final,
            monto_base, dias_mora, porcentaje_mora) {
            // asignar a los hidden inputs
            $('#mpc_prestamo_id').val(prestamo_id);
            $('#mpc_cliente_id').val(cliente_id);
            $('#mpc_cronograma_id').val(cronograma_id);
            $('#mpc_numero_cuota').val(numero_cuota);
            $('#mpc_numero_cuota_text').text(numero_cuota);

            // monto total (display + hidden)
            $('#mpc_monto_total_display').val(monto_total_pago_final.toFixed(2));
            $('#mpc_monto_total_hidden').val(monto_total_pago_final.toFixed(2));

            // monto base para el cálculo de mora
            $('#mpc_monto_base_hidden').val(monto_base.toFixed(2));

            // días y porcentaje de mora
            $('#mpc_dias_mora_hidden').val(dias_mora);
            $('#mpc_porcentaje_mora_hidden').val(porcentaje_mora);

            // sugerir el saldo + mora (para terminar de pagar), pero editable: puede abonar una parte
            $('#mpc_monto_pagado').val(monto_total_pago_final.toFixed(2));

        }

        // --- 3. Envío por AJAX con los mismos datos que antes
        $('#formPagarCuota').on('submit', function(e) {
            e.preventDefault();

            // leer valores
            const prestamo_id = $('#mpc_prestamo_id').val();
            const cliente_id = $('#mpc_cliente_id').val();
            const cronograma_id = $('#mpc_cronograma_id').val();
            const numero_cuota = $('#mpc_numero_cuota').val();

            const monto_total = parseFloat($('#mpc_monto_total_hidden').val());
            const monto_base = parseFloat($('#mpc_monto_base_hidden').val());
            const dias_mora = $('#mpc_dias_mora_hidden').val();
            const porcentaje_mora = $('#mpc_porcentaje_mora_hidden').val();

            // mismo cálculo de mora que antes
            const monto_mora = (monto_total - monto_base).toFixed(2);

            const mpc_monto_pagado = parseFloat($('#mpc_monto_pagado').val());

            // ——— Validación front: se permite abonar una parte (pago parcial) —
            if (!(mpc_monto_pagado > 0)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Monto inválido',
                    text: 'Ingresa un monto a abonar mayor a 0.'
                });
                return; // salimos y no hacemos la petición
            }


            $.ajax({
                url: '{{ route('creditos.pagocuota') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    prestamo_id: prestamo_id,
                    cliente_id: cliente_id,
                    cronograma_id: cronograma_id,
                    numero_cuota: numero_cuota,
                    monto: monto_total,
                    monto_mora: monto_mora,
                    dias_mora: dias_mora,
                    porcentaje_mora: porcentaje_mora,
                    mpc_monto_pagado: mpc_monto_pagado
                },
                success: function(response) {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: response.success,
                        icon: 'success'
                    }).then(() => {
                        window.open(
                            `/admin/generar-ticket-pago/${response.ingreso_id}/${response.diferencia}`,
                            '_blank'
                        );
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error',
                        text: xhr.responseJSON?.error || 'Hubo un problema',
                        icon: 'error'
                    });
                }
            });
        });

        function pagarCuotaGeneral(prestamo_id, fecha) {
            Swal.fire({
                title: '¿Está seguro?',
                text: 'Está a punto de pagar todas las cuotas vencidas en la fecha ' + fecha + '.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, pagar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('creditos.pagogrupal') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            prestamo_id: prestamo_id,
                            fecha: fecha
                        },
                        success: function(response) {
                            Swal.fire({
                                title: '¡Éxito!',
                                text: response.success,
                                icon: 'success'
                            }).then(() => {
                                var ids = response.ingreso_ids.join('-');
                                window.open('/admin/generar-ticket-pagogrupal/' + ids,
                                    '_blank');
                                location.reload();
                            });
                        },
                        error: function(response) {
                            Swal.fire({
                                title: 'Error',
                                text: response.responseJSON.error,
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }

        function abonarCuotaGeneral(prestamo_id, fecha, numero_cuota, monto_referencia) {
            Swal.fire({
                title: 'Abono a cuota general',
                text: 'Ingresa el monto a abonar. Se repartira entre los integrantes de la cuota.',
                input: 'number',
                inputAttributes: {
                    min: '0.01',
                    step: '0.01'
                },
                inputValue: monto_referencia > 0 ? Number(monto_referencia).toFixed(2) : '',
                showCancelButton: true,
                confirmButtonText: 'Aplicar abono',
                cancelButtonText: 'Cancelar',
                preConfirm: (value) => {
                    const v = parseFloat(value);
                    if (!(v > 0)) {
                        Swal.showValidationMessage('Ingresa un monto mayor a 0.');
                        return false;
                    }
                    return v.toFixed(2);
                }
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: '{{ route('creditos.abonogrupal') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        prestamo_id: prestamo_id,
                        fecha: fecha,
                        numero_cuota: numero_cuota,
                        monto_abono: result.value
                    },
                    success: function(response) {
                        let msg = response.success || 'Abono registrado.';
                        if (Number(response.diferencia || 0) > 0) {
                            msg += ' Sobra no aplicada: S/. ' + Number(response.diferencia).toFixed(2);
                        }
                        Swal.fire({
                            title: 'Exito',
                            text: msg,
                            icon: 'success'
                        }).then(() => {
                            if (response.ingreso_ids && response.ingreso_ids.length) {
                                window.open('/admin/generar-ticket-pagogrupal/' + response.ingreso_ids.join('-'), '_blank');
                            }
                            location.reload();
                        });
                    },
                    error: function(response) {
                        Swal.fire({
                            title: 'Error',
                            text: response.responseJSON?.error || 'No se pudo registrar el abono grupal.',
                            icon: 'error'
                        });
                    }
                });
            });
        }

        function pagarTodogrupal(prestamo_id, fecha, numero_cuota) {
            console.log(prestamo_id, fecha, numero_cuota);
            $.ajax({
                url: '{{ route('credito.verpagototalgrupal') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    prestamo_id: prestamo_id,
                    fecha: fecha,
                    numero_cuota: numero_cuota
                },
                success: function(response) {
                    var detalle = '';
                    var totalPagar = response.total_pagar;

                    response.detalle_cuotas.forEach(function(cuota) {
                        detalle += `<tr>
                                    <td>Cuota ${cuota.numero}</td>
                                    <td>S/. ${parseFloat(cuota.monto).toFixed(2)}</td>
                                    <td>${cuota.dias_mora} días</td>
                                    <td>S/. ${parseFloat(cuota.monto_mora).toFixed(2)}</td>
                                    <td>S/. ${parseFloat(cuota.total_pagar).toFixed(2)}</td>
                                </tr>`;
                    });

                    // Mostrar el detalle en el modal
                    $('#detallePagoTodo').html(detalle);
                    // Mostrar el total a pagar
                    $('#totalPagarTodo').text(`S/. ${totalPagar.toFixed(2)}`);

                    // Añadir botón de pagar
                    var botonPagar = `<button class="btn btn-warning" onclick="confirmarpagarTodogrupal(event,'${prestamo_id}','${fecha}','${numero_cuota}')">Pagar Todo</button>
                 <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>`;
                    $('#pagarTodoModal .modal-footer').html(botonPagar);
                },
                error: function(err) {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudieron obtener los datos del crédito.',
                        icon: 'error'
                    });
                }
            });
        }

        function pagarTodoindividual(prestamo_id, fecha, numero_cuota) {
            $.ajax({
                url: '{{ route('credito.verpagototalindividual') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    prestamo_id: prestamo_id,
                    fecha: fecha,
                    numero_cuota: numero_cuota,
                },
                success: function(response) {
                    var detalle = '';
                    var totalPagar = response.total_pagar;

                    response.detalle_cuotas.forEach(function(cuota) {
                        detalle += `<tr>
                                    <td>Cuota ${cuota.numero}</td>
                                    <td>S/. ${parseFloat(cuota.monto).toFixed(2)}</td>
                                    <td>${cuota.dias_mora} días</td>
                                    <td>S/. ${parseFloat(cuota.monto_mora).toFixed(2)}</td>
                                    <td>S/. ${parseFloat(cuota.total_pagar).toFixed(2)}</td>
                                </tr>`;
                    });

                    // Mostrar el detalle en el modal
                    $('#detallePagoTodo').html(detalle);
                    // Mostrar el total a pagar
                    $('#totalPagarTodo').text(`S/. ${totalPagar.toFixed(2)}`);

                    // Añadir botón de pagar
                    var botonPagar = `<button class="btn btn-warning" onclick="confirmarpagarTodoindividual(event,'${prestamo_id}','${fecha}','${numero_cuota}')">Pagar Todo</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>`;
                    $('#pagarTodoModal .modal-footer').html(botonPagar);
                },
                error: function(err) {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudieron obtener los datos del crédito.',
                        icon: 'error'
                    });
                }
            });
        }

        function confirmarpagarTodoindividual(event, prestamo_id, fecha, numero_cuota) {
            Swal.fire({
                title: '¿Confirmar Pago?',
                text: "Está a punto de confirmar el pago de la cuota individual.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('credito.confirmarPagoIndividual') }}', // Ruta al método de pago individual
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            prestamo_id: prestamo_id,
                            fecha: fecha,
                            numero_cuota: numero_cuota
                        },
                        success: function(response) {
                            Swal.fire({
                                title: '¡Éxito!',
                                text: response.success,
                                icon: 'success'
                            }).then(() => {
                                window.open('/admin/generar-ticket-pagototal-individual/' +
                                    response.ingreso_ids.join('-'), '_blank');
                                location.reload();
                            });
                        },
                        error: function(response) {
                            Swal.fire({
                                title: 'Error',
                                text: response.responseJSON.error,
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }

        function confirmarpagarTodogrupal(event, prestamo_id, fecha, numero_cuota) {
            Swal.fire({
                title: '¿Confirmar Pago Grupal?',
                text: "Está a punto de confirmar el pago de todas las cuotas vencidas del grupo.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, confirmar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('credito.confirmarPagoGrupal') }}', // Ruta al método de pago grupal
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            prestamo_id: prestamo_id,
                            fecha: fecha,
                            numero_cuota: numero_cuota
                        },
                        success: function(response) {
                            Swal.fire({
                                title: '¡Éxito!',
                                text: response.success,
                                icon: 'success'
                            }).then(() => {
                                window.open('/admin/generar-ticket-pagogrupal/' + response
                                    .ingreso_ids.join('-'), '_blank');
                                location.reload();
                            });
                        },
                        error: function(response) {
                            Swal.fire({
                                title: 'Error',
                                text: response.responseJSON.error,
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }

        function calcularcuotapendiente() {
            $.ajax({
                url: '{{ route('calcular.cuota.pendiente') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    prestamo_id: '{{ $credito->id }}',
                    fecha_amortizacion: $('#fecha_amortizacion').val(),
                },
                success: function(response) {
                    if (response.cuota) {
                        if (response.puedeamortizar == 0) {
                            $('#detalleCuotaPendiente').html(`
                            <div class="alert alert-warning">
                             La cuota número ${response.cuota.numero} está próxima a vencer el ${response.cuota.fecha}.<br>
                             Por favor, pague la cuota para poder realizar un Pago a Capital.
                            </div>
                        `);
                        } else {
                            $('#numero_cuota').val(response.cuota.numero);
                            $('#intereses_a_pagar').val(response.intereses);
                            $('#capital_pendiente').val(response.amortizacion_faltante);
                            $('#total_a_pagar_posible').val(response.monto_total);
                        }
                    } else {
                        $('#detalleCuotaPendiente').html(`
                <div class="alert alert-success">
                    No hay cuotas pendientes.
                </div>
                `);
                    }
                },
                error: function(response) {
                    Swal.fire({
                        title: 'Error',
                        text: response.responseJSON.error,
                        icon: 'error'
                    });
                }
            });
        }

        function cargarDatosReprogramacion() {
            $.ajax({
                url: '{{ route('solicitar.reprogramacion') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    prestamo_id: '{{ $credito->id }}'
                },
                success: function(d) {
                    // Rellenar los campos del modal
                    $('#rep_cuotas_restantes').val(d.cuotas_restantes);
                    $('#cuotas_reprogramacion').val(d.cuotas_restantes);
                    $('#rep_capital_pendiente').val(d.capital_pendiente.toFixed(2));
                    $('#rep_periodo_pago').val(d.periodo_pago);
                    $('#tasa_interes').val(d.tasa_interes);

                },
                error: function() {
                    Swal.fire('Error', 'No se pudieron cargar los datos de reprogramación.', 'error');
                }
            });
        }

        function confirmarSolicitudReprogramacion() {
            let data = {
                _token: '{{ csrf_token() }}',
                credito_id: $('#credito_id').val(),
                cuotas_solicitadas: $('#cantidad_cuotas').val(),
                nuevo_numero_cuotas: $('#cuotas_reprogramacion').val(),
                cuotas_pendientes: $('#rep_cuotas_restantes').val(),
                tasa_interes: $('#tasa_interes').val(),
                observaciones: $('#observaciones').val(),
                // si necesitas enviar estos campos al backend para mostrar en modal:
                capital_restante: $('#rep_capital_pendiente').val(),

                periodo_pago: $('#rep_periodo_pago').val()
            };

            $.ajax({
                url: '/reprogramaciones/store',
                method: 'POST',
                data: data,
                success: function(res) {
                    // 2) Muestra alerta y, al confirmarla, recarga la página
                    Swal.fire({
                        icon: 'success',
                        title: '¡Listo!',
                        text: 'Tu solicitud ha sido enviada.'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(err) {
                    let msg = err.responseJSON?.message || 'Error al enviar la solicitud.';
                    Swal.fire('Ups...', msg, 'error');
                }
            });
        }

        function generarNuevoCronograma() {
            var opcion = $('input[name="opcion_amortizacion"]:checked').val();
            var prestamo_id = '{{ $credito->id }}';
            var numero_cuota = $('#numero_cuota').val();
            var intereses_a_pagar = parseFloat($('#intereses_a_pagar').val()) || 0;
            var capital_pendiente = $('#capital_pendiente').val();
            var total_a_pagar_posible = $('#total_a_pagar_posible').val();
            var monto_pago_capital = parseFloat($('#monto_pago_capital').val()) || 0;

            if (monto_pago_capital <= intereses_a_pagar || monto_pago_capital === 0) {
                Swal.fire({
                    title: 'Error en el Pago de Capital',
                    text: 'El monto a pagar de capital debe ser mayor que los intereses a pagar y no puede estar vacío.',
                    icon: 'warning'
                });
                return;
            }

            // ✅ Si pasa la validación, ejecuta la petición AJAX
            $.ajax({
                url: '/generarcronogram/temp',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    prestamo_id: prestamo_id,
                    opcion: opcion,
                    numero_cuota: numero_cuota,
                    intereses_a_pagar: intereses_a_pagar,
                    capital_pendiente: capital_pendiente,
                    total_a_pagar_posible: total_a_pagar_posible,
                    monto_pago_capital: monto_pago_capital
                },
                success: function(response) {
                    mostrarNuevoCronograma(response.nuevo_principal, response.cuotas);
                    $('#btnAmortizar').prop('disabled', false);
                    Swal.fire({
                        title: 'Nuevo Cronograma Generado',
                        text: 'Revisa el nuevo cronograma y procede con el pago.',
                        icon: 'success'
                    });
                },
                error: function(response) {
                    Swal.fire({
                        title: 'Error',
                        text: response.responseJSON.error,
                        icon: 'error'
                    });
                }
            });
        }


        function mostrarNuevoCronograma(nuevo_principal, cuotas) {
            var tablaHtml = `
           <h4>💰 <strong>Nuevo Capital Pendiente: S/.${nuevo_principal}</strong> </h4><br>
            <table class="table table-bordered text-center">
                <thead class="thead-dark">
                    <tr>
                        <th># Cuota</th>
                        <th>Fecha de Pago</th>
                        <th>Cuota Total (S/.)</th>
                        <th>Capital (S/.)</th>
                        <th>Interés (S/.)</th>
                        <th>Saldo Deuda (S/.)</th>
                    </tr>
                </thead>
                <tbody>`;
            cuotas.forEach(function(cuota) {
                tablaHtml += `
                <tr>
                    <td><strong>${cuota.numero_cuota}</strong></td>
                    <td>${new Date(cuota.fecha_pago).toLocaleDateString('es-PE')}</td>
                    <td><strong>S/. ${cuota.cuota.toFixed(2)}</strong></td>
                    <td class="text-success">S/. ${cuota.capital.toFixed(2)}</td>
                    <td class="text-danger">S/. ${cuota.interes.toFixed(2)}</td>
                    <td><strong>S/. ${cuota.saldo_deuda.toFixed(2)}</strong></td>
                </tr>`;
            });
            tablaHtml += `</tbody></table>`;
            $('#nuevoCronograma').html(tablaHtml);
        }

        function confirmarpagoCapital() {
            var opcion = $('input[name="opcion_amortizacion"]:checked').val();
            var prestamo_id = '{{ $credito->id }}';
            var numero_cuota = $('#numero_cuota').val();
            var intereses_a_pagar = parseFloat($('#intereses_a_pagar').val()) || 0;
            var capital_pendiente = $('#capital_pendiente').val();
            var total_a_pagar_posible = $('#total_a_pagar_posible').val();
            var monto_pago_capital = parseFloat($('#monto_pago_capital').val()) || 0;

            if (monto_pago_capital <= intereses_a_pagar || monto_pago_capital === 0) {
                Swal.fire({
                    title: 'Error en el Pago de Capital',
                    text: 'El monto a pagar de capital debe ser mayor que los intereses a pagar y no puede estar vacío.',
                    icon: 'warning'
                });
                return;
            }
            $.ajax({
                url: '/generarcronogram/final',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    prestamo_id: prestamo_id,
                    opcion: opcion,
                    numero_cuota: numero_cuota,
                    intereses_a_pagar: intereses_a_pagar,
                    capital_pendiente: capital_pendiente,
                    total_a_pagar_posible: total_a_pagar_posible,
                    monto_pago_capital: monto_pago_capital
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Pago Capital Realizado',
                        text: 'Revisa el Nuevo Cronograma.',
                        icon: 'success'
                    }).then(() => {
                        // Abrir en nueva pestaña
                        window.open('/vernuevocronograma/' + prestamo_id, '_blank');

                        // Recargar la página actual después de abrir la nueva pestaña
                        location.reload();
                    });

                },
                error: function(response) {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo generar el nuevo cronograma.',
                        icon: 'error'
                    });
                }
            });
        }

        function reprogramarCronograma(reprogramacionId) {

            $.ajax({
                url: '/generarcronogramreprogramado',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    reprogramacionId: reprogramacionId,
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Reprogramación Exitosa',
                        text: 'Revisa el Nuevo Cronograma.',
                        icon: 'success'
                    }).then(() => {
                        // Usa response.prestamo_id en lugar de una variable externa
                        window.open('/vernuevocronogramareprogramado/' + response.prestamo_id,
                            '_blank');
                        location.reload();
                    });
                },
                error: function(response) {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo generar el nuevo cronograma.',
                        icon: 'error'
                    });
                }
            });
        }

        function verCronogramaReprogramado(creditoId) {
            window.open('/vernuevocronogramareprogramado/' + creditoId, '_blank');
        }
    </script>


@endsection

