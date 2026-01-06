@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Reporte de Créditos Grupales</h1>
    </div>

    <style>
        .btn-success {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            font-weight: bold;
        }

        .btn-success:hover {
            background-color: #218838 !important;
            border-color: #1e7e34 !important;
        }
    </style>

    <div class="col-md-12">
        <div class="card card-outline">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" id="buscar-cliente" class="form-control"
                                placeholder="Buscar grupo...">
                            <button class="btn btn-outline-primary" type="button" id="btn-buscar-cliente">
                                <i class="bi bi-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="tablegrupal" class="table table-bordered table-sm table-striped table-hover">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Nombre del Grupo</th>
                            <th>Código Agencia</th>
                            <th>Agencia</th>
                            <th>Código Crédito</th>
                            <th>ID Crédito</th>
                            <th>Fecha Desembolso</th>
                            <th>Fecha Última Cuota</th>
                            <th>Fecha Próxima Cuota</th>
                            <th>N° Cuotas</th>
                            <th>Periodicidad</th>
                            <th>Periodo Gracia</th>
                            <th>Fecha Último Pago</th>
                            <th>Cuotas Pagadas</th>
                            <th>Cuotas Pendientes</th>
                            <th>Capital Cancelado</th>
                            <th>Interés Cancelado</th>
                            <th>Interés Moratorio</th>
                            <th>Destino</th>
                            <th>Producto</th>
                            <th>Subproducto</th>
                            <th>Monto Crédito</th>
                            <th>Saldo Capital Crédito</th>
                            <th>Saldo Capital Normal</th>
                            <th>Saldo Capital Vencido</th>
                            <th>Días Atraso</th>
                            <th>Riesgo</th>
                            <th>Situación Contable</th>
                            <th>Interés por Cobrar</th>
                            <th>Asesor</th>
                            <th>TEA</th>
                            <th>Tipo Cronograma</th>
                            <th>Monto Cuota</th>
                            <th>Monto Garantía</th>
                            <th>N° Integrantes</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php $contador = 0; @endphp

                        @foreach ($creditos as $credito)
                            @php
                                $contador++;

                                /** ================================
                                 *  CRONOGRAMA GENERAL (FUENTE VERDAD)
                                 * ================================ */
                                $cronogramaGeneral = $credito->cronograma->whereNull('cliente_id');

                                /** ================================
                                 *  CRONOGRAMAS PAGADOS
                                 * ================================ */
                                $cronogramaPagadas = $cronogramaGeneral->filter(function ($cuota) use ($credito) {
                                    return $credito->ingresos
                                        ->where('cronograma_id', $cuota->id)
                                        ->isNotEmpty();
                                });

                                /** ================================
                                 *  CRONOGRAMAS PENDIENTES
                                 * ================================ */
                                $cronogramaPendientes = $cronogramaGeneral
                                    ->whereNotIn('id', $cronogramaPagadas->pluck('id'));

                                /** ================================
                                 *  CONTEOS
                                 * ================================ */
                                $cuotasTotales = $cronogramaGeneral->count();
                                $cuotasPagadas = $cronogramaPagadas->count();
                                $cuotasPendientes = $cuotasTotales - $cuotasPagadas;

                                /** ================================
                                 *  MONTOS
                                 * ================================ */
                                $capitalCancelado = $cronogramaPagadas->sum('amortizacion');
                                $interesCancelado = $cronogramaPagadas->sum('interes');
                                $interesporcobrar = $cronogramaPendientes->sum('interes');

                                $interesMoratorioCancelado = $credito->ingresos
                                    ->whereNotNull('cronograma_id')
                                    ->sum('monto_mora');

                                /** ================================
                                 *  FECHAS
                                 * ================================ */
                                $ultimaCuotaPagada = $cronogramaPagadas->sortByDesc('fecha')->first();
                                $fechaUltimoPago = $ultimaCuotaPagada
                                    ? $ultimaCuotaPagada->fecha
                                    : 'No hay pagos';

                                $proximaCuota = $cronogramaPendientes->sortBy('fecha')->first();
                                $fechaVencimientoProximaCuota = $proximaCuota
                                    ? $proximaCuota->fecha
                                    : 'No hay próxima cuota';

                                $ultimaCuota = $cronogramaGeneral->sortByDesc('fecha')->first();
                                $fechaUltimaCuota = $ultimaCuota
                                    ? $ultimaCuota->fecha
                                    : 'No hay cuotas';

                                /** ================================
                                 *  ATRASO Y RIESGO
                                 * ================================ */
                                $now = \Carbon\Carbon::now();
                                $diasAtraso = 0;

                                if ($fechaVencimientoProximaCuota && $fechaVencimientoProximaCuota !== 'No hay próxima cuota') {
                                    $diasAtraso = \Carbon\Carbon::parse($fechaVencimientoProximaCuota)->diffInDays(
                                        $now,
                                        false
                                    );
                                    if ($diasAtraso < 0) $diasAtraso = 0;
                                }

                                if ($diasAtraso < 8) {
                                    $riesgo = 'Normal';
                                } elseif ($diasAtraso <= 30) {
                                    $riesgo = 'CPP';
                                } elseif ($diasAtraso <= 60) {
                                    $riesgo = 'Deficiente';
                                } elseif ($diasAtraso <= 120) {
                                    $riesgo = 'Dudoso';
                                } else {
                                    $riesgo = 'Pérdida';
                                }

                                $situacionContable = $diasAtraso > 0 ? 'Vencido' : 'Vigente';

                                /** ================================
                                 *  SALDOS
                                 * ================================ */
                                $saldoCapitalCredito = $credito->monto_total - $capitalCancelado;
                                $saldoCapitalNormal = $cronogramaPendientes
                                    ->where('fecha', '>', $now)
                                    ->sum('amortizacion');

                                $saldoCapitalVencido = $cronogramaPendientes
                                    ->where('fecha', '<=', $now)
                                    ->sum('amortizacion');

                                if ($diasAtraso > 30) {
                                    $saldoCapitalVencido = $credito->monto_total;
                                }
                            @endphp

                            <tr>
                                <td>{{ $contador }}</td>
                                <td>{{ $credito->nombre_prestamo }}</td>
                                <td>{{ $credito->user->sucursal->id }}</td>
                                <td>{{ $credito->user->sucursal->nombre }}</td>
                                <td>{{ optional($credito->correlativos->first())->correlativo ?? 'No asignado' }}</td>
                                <td>{{ $credito->id }}</td>
                                <td>{{ $credito->fecha_desembolso }}</td>
                                <td>{{ $fechaUltimaCuota }}</td>
                                <td>{{ $fechaVencimientoProximaCuota }}</td>
                                <td>{{ $credito->tiempo }}</td>
                                <td>{{ $credito->recurrencia }}</td>
                                <td>{{ $credito->periodo_gracia_dias }}</td>
                                <td>{{ $fechaUltimoPago }}</td>
                                <td>{{ $cuotasPagadas }}</td>
                                <td>{{ $cuotasPendientes }}</td>
                                <td>{{ $capitalCancelado }}</td>
                                <td>{{ $interesCancelado }}</td>
                                <td>{{ $interesMoratorioCancelado }}</td>
                                <td>{{ $credito->destino }}</td>
                                <td>{{ $credito->producto }}</td>
                                <td>{{ $credito->subproducto }}</td>
                                <td>{{ $credito->monto_total }}</td>
                                <td>{{ $saldoCapitalCredito }}</td>
                                <td>{{ $saldoCapitalNormal }}</td>
                                <td>{{ $saldoCapitalVencido }}</td>
                                <td>{{ $diasAtraso }}</td>
                                <td>{{ $riesgo }}</td>
                                <td>{{ $situacionContable }}</td>
                                <td>{{ $interesporcobrar }}</td>
                                <td>{{ $credito->user->name }}</td>
                                <td>{{ $credito->tasa }}</td>
                                <td>{{ $credito->recurrencia }}</td>
                                <td>{{ optional($credito->cronograma->first())->monto }}</td>
                                <td>{{ optional($credito->garantia)->valor_mercado ?? 0 }}</td>
                                <td>{{ $credito->cantidad_integrantes }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <script>
                    $(document).ready(function() {
                        $('#tablegrupal').DataTable({
                            paging: true,
                            lengthChange: false,
                            searching: true,
                            ordering: true,
                            info: true,
                            autoWidth: true,
                            pageLength: 10,
                            dom: 'Bfrtip',
                            buttons: [{
                                extend: 'excelHtml5',
                                text: '<i class="bi bi-file-earmark-excel"></i> Exportar a Excel',
                                className: 'btn btn-success text-white',
                                title: 'Reporte Créditos Grupales'
                            }]
                        });

                        $('#btn-buscar-cliente').on('click', function() {
                            $('#tablegrupal').DataTable()
                                .search($('#buscar-cliente').val())
                                .draw();
                        });
                    });
                </script>
            </div>
        </div>
    </div>
@endsection
