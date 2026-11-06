@extends('layouts.admin')

@section('content')
    @php
        $c = $e['cabecera'];
        $h = $e['hoy'];
        $n = fn ($v) => number_format((float) $v, 2, '.', ',');
        $f = fn ($d) => $d ? \Carbon\Carbon::parse($d)->format('d/m/Y') : '—';
        $tea = fn ($v) => rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.') . '%';
        $badgeCond = ['Vigente' => 'badge-success', 'Vencido' => 'badge-danger', 'Cancelado' => 'badge-primary'];
        $badgeRes = ['Renovado' => 'badge-info', 'Cancelado' => 'badge-primary', 'Vigente' => 'badge-success'];
        $badgeJoya = ['En custodia' => 'badge-warning', 'Liberada' => 'badge-info', 'Entregada' => 'badge-success'];
    @endphp

    @include('admin.creditos.partials.estadocuenta_estilos')

    <div class="container-fluid ec pt-3 pb-4">

        {{-- ================= Cabecera ================= --}}
        <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
            <div class="mb-2">
                <h1 class="ec-titulo">
                    <i class="bi bi-gem"></i> Estado de cuenta · CrediJoya
                    <span class="badge {{ $badgeCond[$c['condicion']] ?? 'badge-secondary' }} ml-1">
                        {{ $c['condicion'] }}{{ $c['condicion'] === 'Vencido' ? ' · ' . $c['dias_atraso'] . ' días' : '' }}
                    </span>
                </h1>
                <div class="ec-sub">
                    Crédito vigente N° {{ $c['actual_id'] }}
                    @if ($c['renovaciones'] > 0)
                        · original N° {{ $c['original_id'] }} · {{ $c['renovaciones'] }} {{ $c['renovaciones'] == 1 ? 'renovación' : 'renovaciones' }}
                    @endif
                    <span class="text-muted font-weight-normal">· al {{ $hoy->format('d/m/Y') }}</span>
                </div>
            </div>
            <div class="mb-2">
                <a href="{{ route('creditos.estadocuenta', $credito->id) }}" target="_blank" class="btn btn-danger">
                    <i class="bi bi-file-earmark-pdf"></i> Ver PDF
                </a>
                @if ($h['vigente'])
                    <a href="{{ route('pagocredijoya.create', $c['actual_id']) }}" class="btn btn-success">
                        <i class="bi bi-cash-stack"></i> Ir a cobrar
                    </a>
                @endif
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('reporte.estadocuenta') }}"
                    class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        {{-- ================= Datos ================= --}}
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title text-brand font-weight-bold"><i class="bi bi-info-circle"></i> Datos del préstamo</h3>
            </div>
            <div class="card-body pb-1">
                <div class="row">
                    <div class="col-xl-3 col-md-4 col-sm-6">
                        <div class="ec-dato-lbl">Cliente</div>
                        <div class="ec-dato-val text-primary">{{ $c['cliente'] }}</div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="ec-dato-lbl">DNI / RUC</div>
                        <div class="ec-dato-val text-brand">{{ $c['documento'] ?: '—' }}</div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="ec-dato-lbl">Primer desembolso</div>
                        <div class="ec-dato-val text-success">S/ {{ $n($c['monto_original']) }} <small class="text-muted">el {{ $f($c['fecha_original']) }}</small></div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="ec-dato-lbl">Tasación de joyas</div>
                        <div class="ec-dato-val text-orange">S/ {{ $n($c['tasacion'] ?: $e['totales']['tasacion']) }}</div>
                    </div>
                    <div class="col-xl-3 col-md-4 col-sm-6">
                        <div class="ec-dato-lbl">TEA vigente</div>
                        <div class="ec-dato-val text-orange">{{ $tea($c['tea']) }}</div>
                    </div>
                    <div class="col-xl-3 col-md-4 col-sm-6">
                        <div class="ec-dato-lbl">Vence</div>
                        <div class="ec-dato-val text-info">{{ $f($c['vence']) }}</div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="ec-dato-lbl">Renovaciones</div>
                        <div class="ec-dato-val text-brand">{{ $c['renovaciones'] }}</div>
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
                    <span class="info-box-icon bg-warning elevation-1"><i class="bi bi-cash-coin text-white"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Para cancelar hoy</span>
                        <span class="info-box-number text-orange">S/ {{ $n($h['cancelar']['total']) }}</span>
                        <small>{{ $h['vigente'] ? 'Paga todo y se liberan las joyas' : 'Préstamo cancelado' }}</small>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="bi bi-arrow-repeat"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Para renovar hoy</span>
                        <span class="info-box-number text-info">S/ {{ $n($h['renovar']['total']) }}</span>
                        <small>{{ $h['vigente'] ? 'Interés + mora; se renueva un mes' : '—' }}</small>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-4 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-primary elevation-1"><i class="bi bi-bank"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Capital pendiente</span>
                        <span class="info-box-number text-primary">S/ {{ $n($h['vigente'] ? $h['cancelar']['capital'] : 0) }}</span>
                        <small>Interés programado S/ {{ $n($h['interes_prog']) }}</small>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-6 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon {{ $c['dias_atraso'] > 0 ? 'bg-danger' : 'bg-teal' }} elevation-1"><i class="bi bi-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Días de atraso</span>
                        <span class="info-box-number {{ $c['dias_atraso'] > 0 ? 'text-danger' : 'text-teal' }}">{{ $c['dias_atraso'] }} días</span>
                        <small>Mora S/ {{ $n($h['mora']) }}</small>
                    </div>
                </div>
            </div>
            <div class="col-xl col-md-6 col-sm-12">
                <div class="info-box">
                    <span class="info-box-icon bg-success elevation-1"><i class="bi bi-check2-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total pagado</span>
                        <span class="info-box-number text-success">S/ {{ $n($e['totales']['pagado']) }}</span>
                        <small>{{ count($e['pagos']) }} pagos en toda la cadena</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= Desglose de hoy ================= --}}
        @if ($h['vigente'])
            <div class="row">
                <div class="col-lg-6">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="bi bi-cash-coin"></i> Para cancelar hoy {{ $hoy->format('d/m/Y') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="ec-kv"><span>Capital</span><span class="text-primary">{{ $n($h['cancelar']['capital']) }}</span></div>
                            <div class="ec-kv"><span>Interés</span><span class="text-info">{{ $n($h['cancelar']['interes']) }}</span></div>
                            <div class="ec-kv"><span>Mora</span><span class="text-danger">{{ $n($h['cancelar']['mora']) }}</span></div>
                            <div class="ec-kv total text-orange"><span>Total S/</span><span>{{ $n($h['cancelar']['total']) }}</span></div>
                            <small class="text-muted">
                                @if ($h['con_abonos'])
                                    La cuota ya tiene abonos: es el saldo que falta más la mora.
                                @elseif ($c['condicion'] === 'Vencido')
                                    Cuota vencida: cuota completa más mora.
                                @else
                                    Interés a hoy si faltan más de 7 días para vencer; si no, el interés del mes. Mismo monto que cobra caja.
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title font-weight-bold"><i class="bi bi-arrow-repeat"></i> Para renovar hoy</h3>
                        </div>
                        <div class="card-body">
                            <div class="ec-kv"><span>Interés</span><span class="text-info">{{ $n($h['renovar']['interes']) }}</span></div>
                            <div class="ec-kv"><span>Mora</span><span class="text-danger">{{ $n($h['renovar']['mora']) }}</span></div>
                            <div class="ec-kv total text-info"><span>Total S/</span><span>{{ $n($h['renovar']['total']) }}</span></div>
                            <small class="text-muted">
                                Pago mínimo para renovar: el crédito se cierra y se abre uno nuevo por el mismo capital
                                (S/ {{ $n($h['capital']) }}) a un mes. Las joyas siguen en custodia.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ================= Joyas ================= --}}
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title text-brand font-weight-bold"><i class="bi bi-gem"></i> Joyas en garantía</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-striped mb-0">
                        <thead class="thead-brand">
                            <tr>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th class="text-center">Kilate</th>
                                <th class="text-center">Piezas</th>
                                <th class="num">Peso bruto (g)</th>
                                <th class="num">Peso neto (g)</th>
                                <th class="num">Tasación (S/)</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($e['joyas'] as $j)
                                <tr>
                                    <td class="font-weight-bold text-brand">{{ $j['codigo'] ?: '—' }}</td>
                                    <td class="text-primary">{{ $j['descripcion'] ?: '—' }}</td>
                                    <td class="text-center text-orange font-weight-bold">{{ $j['kilate'] }}k</td>
                                    <td class="text-center">{{ $j['piezas'] }}</td>
                                    <td class="num">{{ number_format($j['peso_bruto'], 3) }}</td>
                                    <td class="num">{{ number_format($j['peso_neto'], 3) }}</td>
                                    <td class="num text-success font-weight-bold">{{ $n($j['tasacion']) }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $badgeJoya[$j['estado']] ?? 'badge-secondary' }}">{{ $j['estado'] }}</span>
                                        @if ($j['entregada'])
                                            <span class="det-fila">{{ $f($j['entregada']) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-muted py-3">No hay joyas registradas en este préstamo.</td></tr>
                            @endforelse
                        </tbody>
                        @if (count($e['joyas']))
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="num">TOTAL TASACIÓN</td>
                                    <td class="num">{{ $n($e['totales']['tasacion']) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= Historial de créditos ================= --}}
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title text-brand font-weight-bold"><i class="bi bi-diagram-3"></i> Historial del préstamo (original y renovaciones)</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-striped mb-0">
                        <thead class="thead-brand">
                            <tr>
                                <th class="text-center">#</th>
                                <th>Crédito</th>
                                <th>Desembolso</th>
                                <th>Vencimiento</th>
                                <th class="num">Capital</th>
                                <th class="num">TEA</th>
                                <th class="num">Interés prog.</th>
                                <th>Fecha pago</th>
                                <th class="num">Interés pag.</th>
                                <th class="num">Mora pag.</th>
                                <th class="num">Capital pag.</th>
                                <th class="num">Total pagado</th>
                                <th class="text-center">Resultado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($e['creditos'] as $cr)
                                <tr>
                                    <td class="text-center font-weight-bold text-brand">{{ $cr['secuencia'] }}</td>
                                    <td class="font-weight-bold text-brand">N° {{ $cr['credito_id'] }}</td>
                                    <td class="text-info">{{ $f($cr['desembolso']) }}</td>
                                    <td class="text-info">{{ $f($cr['vencimiento']) }}</td>
                                    <td class="num text-primary">{{ $n($cr['capital']) }}</td>
                                    <td class="num text-orange">{{ $tea($cr['tea']) }}</td>
                                    <td class="num text-info">{{ $n($cr['interes_prog']) }}</td>
                                    <td class="{{ $cr['fecha_pago'] ? 'text-success' : 'tenue' }}">{{ $f($cr['fecha_pago']) }}</td>
                                    <td class="num {{ $cr['interes_pag'] > 0 ? 'text-info' : 'tenue' }}">{{ $n($cr['interes_pag']) }}</td>
                                    <td class="num {{ $cr['mora_pag'] > 0 ? 'text-danger' : 'tenue' }}">{{ $n($cr['mora_pag']) }}</td>
                                    <td class="num {{ $cr['capital_pag'] > 0 ? 'text-primary' : 'tenue' }}">{{ $n($cr['capital_pag']) }}</td>
                                    <td class="num {{ $cr['total_pag'] > 0 ? 'text-success font-weight-bold' : 'tenue' }}">{{ $n($cr['total_pag']) }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $badgeRes[$cr['resultado']] ?? 'badge-secondary' }}">{{ $cr['resultado'] }}</span>
                                        @if ($cr['renovado_a'])
                                            <span class="det-fila">a N° {{ $cr['renovado_a'] }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ================= Pagos ================= --}}
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title text-brand font-weight-bold"><i class="bi bi-receipt"></i> Pagos realizados</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-striped mb-0">
                        <thead class="thead-brand">
                            <tr>
                                <th>Fecha</th>
                                <th>Crédito</th>
                                <th>Concepto</th>
                                <th class="num">Interés</th>
                                <th class="num">Mora</th>
                                <th class="num">Capital</th>
                                <th class="num">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($e['pagos'] as $p)
                                <tr>
                                    <td class="text-success">{{ $f($p['fecha']) }}</td>
                                    <td class="font-weight-bold text-brand">N° {{ $p['credito_id'] }}</td>
                                    <td>
                                        {{ $p['concepto'] }}
                                        @if ($p['renueva_a'])
                                            <small class="text-muted">→ N° {{ $p['renueva_a'] }}</small>
                                        @endif
                                    </td>
                                    <td class="num {{ $p['interes'] > 0 ? 'text-info' : 'tenue' }}">{{ $n($p['interes']) }}</td>
                                    <td class="num {{ $p['mora'] > 0 ? 'text-danger' : 'tenue' }}">{{ $n($p['mora']) }}</td>
                                    <td class="num {{ $p['capital'] > 0 ? 'text-primary' : 'tenue' }}">{{ $n($p['capital']) }}</td>
                                    <td class="num text-success font-weight-bold">{{ $n($p['total']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-3">Aún no hay pagos. Aparecerán aquí cuando el cliente pague o renueve.</td></tr>
                            @endforelse
                        </tbody>
                        @if (count($e['pagos']))
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="num">TOTALES</td>
                                    <td class="num">{{ $n($e['totales']['interes']) }}</td>
                                    <td class="num">{{ $n($e['totales']['mora']) }}</td>
                                    <td class="num">{{ $n($e['totales']['capital']) }}</td>
                                    <td class="num text-success">{{ $n($e['totales']['pagado']) }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="callout callout-info mt-3 mb-0">
            <small>
                En CrediJoya, cuando el cliente paga solo el interés, el crédito se <b>renueva</b>: se cierra y se abre uno
                nuevo por un mes con las mismas joyas. Este estado de cuenta junta toda la cadena, desde el primer desembolso.
                La mora se calcula a 1.5 por mil diario sobre la cuota vencida.
            </small>
        </div>
    </div>
@endsection
