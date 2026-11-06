@extends('layouts.admin')

@section('content')
    @php
        $c = $e['cabecera'];
        $esGrupal = $credito->categoria === 'grupal';
        $n = fn ($v) => number_format((float) $v, 2, '.', ',');
        $f = fn ($d) => $d ? \Carbon\Carbon::parse($d)->format('d/m/Y') : '—';
        $badgeEstado = [
            'Pagado'       => 'badge-success',
            'Parcial'      => 'badge-info',
            'Vencido'      => 'badge-danger',
            'Pendiente'    => 'badge-secondary',
            'Pago capital' => 'badge-warning',
        ];
        $badgeCond = [
            'Normal'         => 'badge-success',
            'Vencido'        => 'badge-danger',
            'Cancelado'      => 'badge-primary',
            'Sin cronograma' => 'badge-secondary',
        ];
        // Parámetros del PDF equivalentes a lo que se está viendo.
        $pdfParams = ['id' => $credito->id] + ($clienteId ? ['cliente' => $clienteId] : []);
        $puedeCobrar = in_array($credito->estado, ['pagado', 'mora']);
    @endphp

    @include('admin.creditos.partials.estadocuenta_estilos')

    <div class="container-fluid ec pt-3 pb-4">

        {{-- ================= Cabecera ================= --}}
        <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
            <div class="mb-2">
                <h1 class="ec-titulo">
                    <i class="bi bi-journal-text"></i> Estado de cuenta
                    <span class="badge {{ $badgeCond[$c['condicion']] ?? 'badge-secondary' }} ml-1">
                        {{ $c['condicion'] }}{{ $c['condicion'] === 'Vencido' ? ' · ' . $c['dias_atraso'] . ' días' : '' }}
                    </span>
                </h1>
                <div class="ec-sub">
                    Crédito N° {{ $credito->id }} · {{ $c['modalidad'] }}
                    @if ($esGrupal)
                        · {{ $c['es_grupo'] ? 'Consolidado del grupo' : 'Integrante' }}
                    @endif
                    <span class="text-muted font-weight-normal">· al {{ $hoy->format('d/m/Y') }}</span>
                </div>
            </div>

            <div class="mb-2">
                <a href="{{ route('creditos.estadocuenta', $pdfParams) }}" target="_blank" class="btn btn-danger">
                    <i class="bi bi-file-earmark-pdf"></i> Ver PDF
                </a>
                @if ($esGrupal && !$clienteId)
                    <a href="{{ route('creditos.estadocuenta', ['id' => $credito->id, 'integrantes' => 0]) }}" target="_blank"
                        class="btn btn-outline-danger" title="PDF solo con la hoja consolidada del grupo">
                        <i class="bi bi-file-earmark-text"></i> PDF solo grupo
                    </a>
                @endif
                @if ($puedeCobrar)
                    <a href="{{ route('creditos.verpagocuota', $credito->id) }}" class="btn btn-success">
                        <i class="bi bi-cash-stack"></i> Ir a cobrar
                    </a>
                @endif
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('reporte.estadocuenta') }}"
                    class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        {{-- ================= Grupo / integrantes ================= --}}
        @if ($esGrupal && $integrantes->count())
            <div class="card card-outline card-primary mb-3">
                <div class="card-body py-2">
                    <ul class="nav nav-pills flex-wrap">
                        <li class="nav-item">
                            <a class="nav-link {{ !$clienteId ? 'active' : '' }}"
                                href="{{ route('creditos.estadocuenta.ver', $credito->id) }}">
                                <i class="bi bi-people-fill"></i> Grupo
                            </a>
                        </li>
                        @foreach ($integrantes as $cc)
                            <li class="nav-item">
                                <a class="nav-link {{ $clienteId === (int) $cc->cliente_id ? 'active' : '' }}"
                                    href="{{ route('creditos.estadocuenta.ver', ['id' => $credito->id, 'cliente' => $cc->cliente_id]) }}">
                                    <i class="bi bi-person"></i> {{ $cc->clientes->nombre }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- ================= Datos del crédito ================= --}}
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title text-brand font-weight-bold"><i class="bi bi-info-circle"></i> Datos del crédito</h3>
            </div>
            <div class="card-body pb-1">
                <div class="row">
                    <div class="col-xl-3 col-md-4 col-sm-6">
                        <div class="ec-dato-lbl">{{ $c['es_grupo'] ? 'Grupo' : 'Cliente' }}</div>
                        <div class="ec-dato-val text-primary">{{ $c['cliente'] }}</div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="ec-dato-lbl">{{ $c['es_grupo'] ? 'Integrantes' : 'DNI / RUC' }}</div>
                        <div class="ec-dato-val text-brand">{{ $c['es_grupo'] ? count($c['integrantes']) : ($c['documento'] ?: '—') }}</div>
                    </div>
                    @if (!$c['es_grupo'] && $c['grupo'])
                        <div class="col-xl-2 col-md-4 col-sm-6">
                            <div class="ec-dato-lbl">Grupo</div>
                            <div class="ec-dato-val text-indigo">{{ $c['grupo'] }}</div>
                        </div>
                    @endif
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="ec-dato-lbl">Operación</div>
                        <div class="ec-dato-val text-brand">
                            {{ $c['operacion'] }} @if ($c['codigo'])<small class="text-muted">({{ $c['codigo'] }})</small>@endif
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="ec-dato-lbl">Desembolso</div>
                        <div class="ec-dato-val text-success">S/ {{ $n($c['desembolso']) }}</div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="ec-dato-lbl">Fecha desembolso</div>
                        <div class="ec-dato-val text-info">{{ $f($c['fecha_desembolso']) }}</div>
                    </div>
                    <div class="col-xl-3 col-md-4 col-sm-6">
                        <div class="ec-dato-lbl">Producto</div>
                        <div class="ec-dato-val text-brand">{{ $c['producto'] ?: $c['modalidad'] }}</div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="ec-dato-lbl">Frecuencia</div>
                        <div class="ec-dato-val text-brand">{{ ucfirst($c['frecuencia'] ?? '—') }} <small class="text-muted">· {{ $c['num_cuotas'] }} cuotas</small></div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="ec-dato-lbl">TEA</div>
                        <div class="ec-dato-val text-orange">{{ $c['tea'] !== null ? rtrim(rtrim(number_format((float) $c['tea'], 2, '.', ''), '0'), '.') . '%' : '—' }}</div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="ec-dato-lbl">Vence el crédito</div>
                        <div class="ec-dato-val text-info">{{ $f($c['fecha_fin']) }}</div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="ec-dato-lbl">Oficina</div>
                        <div class="ec-dato-val text-brand">{{ $c['oficina'] ?: '—' }}</div>
                    </div>
                    <div class="col-xl-3 col-md-4 col-sm-6">
                        <div class="ec-dato-lbl">Asesor</div>
                        <div class="ec-dato-val text-brand">{{ $c['asesor'] ?: '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= Indicadores ================= --}}
        <div class="row">
            <div class="col-xl col-md-4 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-success elevation-1"><i class="bi bi-check2-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total pagado</span>
                        <span class="info-box-number text-success">S/ {{ $n($e['pagos']['total_pagado']) }}</span>
                        <small>{{ $e['pagos']['cuotas_pagadas'] }} de {{ $e['pagos']['cuotas_total'] }} cuotas · último {{ $f($e['pagos']['ultimo_pago']) }}</small>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="bi bi-calendar3"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Saldo según cronograma</span>
                        <span class="info-box-number text-info">S/ {{ $n($e['saldo']['total']) }}</span>
                        <small>Capital {{ $n($e['saldo']['capital']) }} · interés {{ $n($e['saldo']['interes']) }}</small>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-warning elevation-1"><i class="bi bi-cash-coin text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Para cancelar hoy</span>
                        <span class="info-box-number text-orange">S/ {{ $n($e['cancelar']['total']) }}</span>
                        <small>Mismo monto que cobra caja</small>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-6 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon {{ $c['dias_atraso'] > 0 ? 'bg-danger' : 'bg-teal' }} elevation-1"><i class="bi bi-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Días de atraso</span>
                        <span class="info-box-number {{ $c['dias_atraso'] > 0 ? 'text-danger' : 'text-teal' }}">{{ $c['dias_atraso'] }} días</span>
                        <small>{{ $e['pagos']['cuotas_vencidas'] }} vencidas · mora S/ {{ $n($e['saldo']['mora']) }}</small>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-6 col-sm-12">
                <div class="info-box">
                    <span class="info-box-icon bg-lightblue elevation-1"><i class="bi bi-calendar-event"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Próximo vencimiento</span>
                        <span class="info-box-number text-lightblue">{{ $c['proximo_venc'] ? $f($c['proximo_venc']) : '—' }}</span>
                        <small>{{ $c['proximo_venc'] ? 'A pagar S/ ' . $n($c['proximo_monto']) : 'No quedan cuotas pendientes' }}</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= Cuotas ================= --}}
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title text-brand font-weight-bold"><i class="bi bi-list-ol"></i> Detalle de cuotas</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-striped mb-0">
                        <thead class="thead-brand">
                            <tr>
                                <th class="text-center">Nro</th>
                                <th class="text-center">D/P</th>
                                <th>Vencimiento</th>
                                <th>Fecha pago</th>
                                <th class="text-center">Atraso</th>
                                <th class="num">Capital</th>
                                <th class="num">Interés</th>
                                <th class="num">Mora</th>
                                <th class="num">Cuota</th>
                                <th class="num">Total pagado</th>
                                <th class="num">Total pendiente</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="fila-d">
                                <td class="text-center">0</td>
                                <td class="text-center">D</td>
                                <td>—</td>
                                <td>{{ $f($c['fecha_desembolso']) }}</td>
                                <td class="text-center">—</td>
                                <td class="num">{{ $n($c['desembolso']) }}*</td>
                                <td class="num">—</td>
                                <td class="num">—</td>
                                <td class="num">—</td>
                                <td class="num">—</td>
                                <td class="num">—</td>
                                <td class="text-center"><span class="badge bg-lightblue">Desembolsado</span></td>
                            </tr>

                            @forelse ($e['filas'] as $fila)
                                @php
                                    $esK = $fila['tipo'] === 'K';
                                    $vencidaPend = $fila['vencida'] && !$fila['liquidada'];
                                @endphp
                                <tr class="{{ $esK ? 'fila-k' : '' }} {{ $vencidaPend ? 'fila-vencida' : '' }}">
                                    <td class="text-center font-weight-bold text-brand">{{ $fila['numero'] }}</td>
                                    <td class="text-center">{{ $esK ? 'K' : 'P' }}</td>
                                    <td class="text-info">{{ $esK ? '—' : $f($fila['vencimiento']) }}</td>
                                    <td class="{{ $fila['fecha_pago'] ? 'text-success' : 'tenue' }}">{{ $fila['fecha_pago'] ? $f($fila['fecha_pago']) : '—' }}</td>
                                    <td class="text-center {{ $fila['atraso'] > 0 && !$fila['liquidada'] ? 'text-danger font-weight-bold' : ($fila['atraso'] > 0 ? 'text-orange' : 'tenue') }}">
                                        {{ $esK ? '—' : $fila['atraso'] }}
                                    </td>
                                    <td class="num text-primary">{{ $n($fila['capital']) }}</td>
                                    <td class="num text-info">{{ $n($fila['interes']) }}</td>
                                    <td class="num {{ $fila['mora'] > 0 ? 'text-danger' : 'tenue' }}">{{ $n($fila['mora']) }}</td>
                                    <td class="num text-brand font-weight-bold">{{ $esK ? '—' : $n($fila['cuota']) }}</td>
                                    <td class="num {{ $fila['pagado'] > 0 ? 'text-success font-weight-bold' : 'tenue' }}">{{ $n($fila['pagado']) }}</td>
                                    <td class="num {{ $fila['pendiente'] > 0 ? 'text-danger font-weight-bold' : 'tenue' }}">{{ $n($fila['pendiente']) }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $badgeEstado[$fila['estado']] ?? 'badge-secondary' }}">{{ $fila['estado'] }}</span>
                                        @if ($fila['detalle'])
                                            <span class="det-fila">{{ $fila['detalle'] }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center text-muted py-4">
                                        Este crédito aún no tiene cuotas en el cronograma. Se mostrarán aquí cuando se genere.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="num">TOTALES</td>
                                <td class="num">{{ $n($e['totales']['capital']) }}</td>
                                <td class="num">{{ $n($e['totales']['interes']) }}</td>
                                <td class="num">{{ $n($e['totales']['mora']) }}</td>
                                <td class="num">{{ $n($e['totales']['cuota']) }}</td>
                                <td class="num text-success">{{ $n($e['totales']['pagado']) }}</td>
                                <td class="num text-danger">{{ $n($e['totales']['pendiente']) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= Resúmenes ================= --}}
        <div class="row">
            <div class="col-lg-4">
                <div class="card card-outline card-info h-100">
                    <div class="card-header">
                        <h3 class="card-title text-info font-weight-bold"><i class="bi bi-calendar3"></i> Saldo según cronograma</h3>
                    </div>
                    <div class="card-body">
                        <div class="ec-kv"><span>Capital pendiente</span><span class="text-primary">{{ $n($e['saldo']['capital']) }}</span></div>
                        <div class="ec-kv"><span>Interés pendiente</span><span class="text-info">{{ $n($e['saldo']['interes']) }}</span></div>
                        <div class="ec-kv"><span>Mora pendiente</span><span class="text-danger">{{ $n($e['saldo']['mora']) }}</span></div>
                        <div class="ec-kv total text-info"><span>Total S/</span><span>{{ $n($e['saldo']['total']) }}</span></div>
                        <small class="text-muted">Lo que falta pagar si el cliente sigue su cronograma normal.</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card card-warning h-100">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold"><i class="bi bi-cash-coin"></i> Para cancelar hoy {{ $hoy->format('d/m/Y') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="ec-kv"><span>Capital</span><span class="text-primary">{{ $n($e['cancelar']['capital']) }}</span></div>
                        <div class="ec-kv"><span>Interés devengado</span><span class="text-info">{{ $n($e['cancelar']['interes']) }}</span></div>
                        <div class="ec-kv"><span>Mora</span><span class="text-danger">{{ $n($e['cancelar']['mora']) }}</span></div>
                        <div class="ec-kv total text-orange"><span>Total S/</span><span>{{ $n($e['cancelar']['total']) }}</span></div>
                        <small class="text-muted">Cancelación anticipada: mismo monto que cobra caja en el pago total.</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card card-outline card-success h-100">
                    <div class="card-header">
                        <h3 class="card-title text-success font-weight-bold"><i class="bi bi-receipt"></i> Resumen de pagos</h3>
                    </div>
                    <div class="card-body">
                        <div class="ec-kv"><span>Cuotas pagadas</span><span class="text-success">{{ $e['pagos']['cuotas_pagadas'] }} de {{ $e['pagos']['cuotas_total'] }}</span></div>
                        <div class="ec-kv"><span>Cuotas vencidas</span><span class="{{ $e['pagos']['cuotas_vencidas'] ? 'text-danger' : 'text-teal' }}">{{ $e['pagos']['cuotas_vencidas'] }}</span></div>
                        <div class="ec-kv"><span>Mora pagada</span><span class="text-danger">{{ $n($e['pagos']['mora_pagada']) }}</span></div>
                        <div class="ec-kv total text-success"><span>Total pagado S/</span><span>{{ $n($e['pagos']['total_pagado']) }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= Integrantes (vista del grupo) ================= --}}
        @if ($c['es_grupo'] && count($c['integrantes']))
            <div class="card card-outline card-primary mt-3">
                <div class="card-header">
                    <h3 class="card-title text-brand font-weight-bold"><i class="bi bi-people"></i> Detalle por integrante</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped mb-0">
                            <thead class="thead-brand">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Integrante</th>
                                    <th>DNI</th>
                                    <th class="num">Desembolso</th>
                                    <th class="num">Total pagado</th>
                                    <th class="num">Saldo cronograma</th>
                                    <th class="num">Mora pend.</th>
                                    <th class="num">Cancelar hoy</th>
                                    <th class="text-center">Atraso</th>
                                    <th class="text-center">Condición</th>
                                    <th class="text-center">Estado de cuenta</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($c['integrantes'] as $i => $m)
                                    <tr>
                                        <td class="text-center font-weight-bold text-brand">{{ $i + 1 }}</td>
                                        <td class="text-primary font-weight-bold">{{ $m['nombre'] }}</td>
                                        <td>{{ $m['documento'] ?: '—' }}</td>
                                        <td class="num">{{ $n($m['desembolso']) }}</td>
                                        <td class="num text-success">{{ $n($m['pagado']) }}</td>
                                        <td class="num text-info">{{ $n($m['saldo']) }}</td>
                                        <td class="num {{ $m['mora'] > 0 ? 'text-danger' : 'tenue' }}">{{ $n($m['mora']) }}</td>
                                        <td class="num text-orange font-weight-bold">{{ $n($m['cancelar']) }}</td>
                                        <td class="text-center {{ $m['dias_atraso'] > 0 ? 'text-danger font-weight-bold' : 'tenue' }}">{{ $m['dias_atraso'] }}</td>
                                        <td class="text-center"><span class="badge {{ $badgeCond[$m['condicion']] ?? 'badge-secondary' }}">{{ $m['condicion'] }}</span></td>
                                        <td class="text-center text-nowrap">
                                            <a href="{{ route('creditos.estadocuenta.ver', ['id' => $credito->id, 'cliente' => $m['cliente_id']]) }}"
                                                class="btn btn-xs btn-info"><i class="bi bi-eye"></i> Ver</a>
                                            <a href="{{ route('creditos.estadocuenta', ['id' => $credito->id, 'cliente' => $m['cliente_id']]) }}"
                                                target="_blank" class="btn btn-xs btn-outline-danger"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <div class="callout callout-info mt-3 mb-0">
            <small>
                (*) El monto del desembolso no se suma en los totales.
                <b>D</b> = desembolso · <b>P</b> = cuota del cronograma · <b>K</b> = pago adelantado a capital.
                "Atraso" son los días entre el vencimiento y la fecha de pago (o hoy, si la cuota sigue pendiente).
                La mora vigente se calcula a 1.5 por mil diario sobre el saldo vencido de cada cuota.
                @if ($c['es_grupo'])
                    Los montos del grupo son la suma de sus integrantes.
                @endif
            </small>
        </div>
    </div>
@endsection
