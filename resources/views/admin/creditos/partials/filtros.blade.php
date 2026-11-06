{{--
    Barra de filtros para listados de créditos.
    Variables: $accion (url del listado), $filtros (array), $estados (valor => etiqueta),
               $categorias (colección de valores), $creditos (resultado, para el resumen).
--}}
@php
    $nombresCategoria = [
        'individual' => 'Individual',
        'servicio'   => 'Servicio',
        'produccion' => 'Producción',
        'comercio'   => 'Comercio',
        'agricola'   => 'Agrícola',
        'grupal'     => 'Grupal',
        'credijoya'  => 'CrediJoya',
    ];
    $f = fn ($k) => $filtros[$k] ?? '';
    $hayFiltros = collect($filtros)->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty();
    $mesActual = now()->format('Y-m');
    $mesAnterior = now()->subMonthNoOverflow()->format('Y-m');
    $mesTexto = function ($ym) {
        try {
            return ucfirst(\Carbon\Carbon::createFromFormat('Y-m-d', $ym . '-01')->locale('es')->translatedFormat('F Y'));
        } catch (\Throwable $e) {
            return $ym;
        }
    };
@endphp

<div class="card card-outline card-primary mb-3">
    <div class="card-header py-2">
        <h3 class="card-title font-weight-bold text-primary"><i class="bi bi-funnel"></i> Filtros</h3>
        @if ($mostrarResumen ?? true)
            <div class="card-tools">
                <span class="badge badge-primary mr-1">{{ $creditos->count() }} créditos</span>
                <span class="badge badge-success">S/ {{ number_format((float) $creditos->sum('monto_total'), 2) }}</span>
            </div>
        @endif
    </div>
    <div class="card-body pb-1">
        <form method="GET" action="{{ $accion }}" id="formFiltrosCreditos">
            <div class="row">
                <div class="col-xl-2 col-md-4 col-sm-6 form-group">
                    <label class="small text-muted text-uppercase font-weight-bold mb-1">Estado</label>
                    <select name="estado" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        @foreach ($estados as $valor => $etiqueta)
                            <option value="{{ $valor }}" @selected($f('estado') === $valor)>{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6 form-group">
                    <label class="small text-muted text-uppercase font-weight-bold mb-1">Tipo de crédito</label>
                    <select name="categoria" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        @foreach ($categorias as $cat)
                            <option value="{{ $cat }}" @selected($f('categoria') === $cat)>{{ $nombresCategoria[$cat] ?? ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6 form-group">
                    <label class="small text-muted text-uppercase font-weight-bold mb-1">Mes de desembolso</label>
                    <input type="month" name="mes" value="{{ $f('mes') }}" class="form-control form-control-sm"
                        onchange="this.form.submit()">
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6 form-group">
                    <label class="small text-muted text-uppercase font-weight-bold mb-1">Desembolso desde</label>
                    <input type="date" name="desde" value="{{ $f('desde') }}" class="form-control form-control-sm">
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6 form-group">
                    <label class="small text-muted text-uppercase font-weight-bold mb-1">Desembolso hasta</label>
                    <input type="date" name="hasta" value="{{ $f('hasta') }}" class="form-control form-control-sm">
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6 form-group d-flex align-items-end">
                    <button type="submit" class="btn btn-sm btn-primary mr-1"><i class="bi bi-search"></i> Filtrar</button>
                    @if ($hayFiltros)
                        <a href="{{ $accion }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-circle"></i> Limpiar</a>
                    @endif
                </div>
            </div>
        </form>

        <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 6px;">
            <span class="small text-muted mr-1">Rápido:</span>
            <a href="{{ $accion }}?{{ http_build_query(array_merge($filtros, ['mes' => $mesActual, 'desde' => null, 'hasta' => null])) }}"
                class="badge {{ $f('mes') === $mesActual ? 'badge-primary' : 'badge-light border' }} px-2 py-1">Este mes</a>
            <a href="{{ $accion }}?{{ http_build_query(array_merge($filtros, ['mes' => $mesAnterior, 'desde' => null, 'hasta' => null])) }}"
                class="badge {{ $f('mes') === $mesAnterior ? 'badge-primary' : 'badge-light border' }} px-2 py-1">Mes anterior</a>
            @foreach (['pagado' => 'Activos', 'mora' => 'En mora', 'terminado' => 'Terminados'] as $valor => $etq)
                @if (isset($estados[$valor]))
                    <a href="{{ $accion }}?{{ http_build_query(array_merge($filtros, ['estado' => $valor])) }}"
                        class="badge {{ $f('estado') === $valor ? 'badge-primary' : 'badge-light border' }} px-2 py-1">{{ $etq }}</a>
                @endif
            @endforeach
            @if ($f('mes'))
                <span class="small text-info ml-2"><i class="bi bi-calendar3"></i> Desembolsados en {{ $mesTexto($f('mes')) }}</span>
            @endif
            <span class="small text-muted ml-auto"><i class="bi bi-sort-down"></i> Ordenado por fecha de desembolso, el más reciente primero</span>
        </div>
    </div>
</div>
