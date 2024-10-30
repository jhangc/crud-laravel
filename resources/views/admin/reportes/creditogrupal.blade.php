@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Reporte de Total de Clientes</h1>
    </div>

    <div class="col-md-12">
        <div class="card card-outline">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" id="buscar-cliente" class="form-control" placeholder="Buscar cliente...">
                            <button class="btn btn-outline-primary" type="button" id="btn-buscar-cliente"><i
                                    class="bi bi-search"></i> Buscar</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-tools float-right">
                            <a href="{{ url('/admin/reportes/credito/exportarcreditosgrupal') }}" class="btn btn-success"><i
                                    class="bi bi-file-earmark-excel"></i> Exportar a Excel</a>
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
                            <th>Código de agencia</th>
                            <th>Agencia</th>
                            <th>Código del crédito Grupal</th>
                            <th>Codigo de Prestamo</th>
                            <th>Fecha de desembolso</th>
                            <th>Fecha de vencimiento</th>
                            <th>Fecha de vencimiento de cuota</th>
                            <th>N° Cuotas</th>
                            <th>Periocidad de cuotas</th>
                            <th>Periodo de gracia</th>
                            <th>Fecha de último pago</th>
                            <th>N° Cuotas pagadas</th>
                            <th>N° Cuotas pendientes</th>
                            <th>Capital cancelado</th>
                            <th>Interés cancelado</th>
                            <th>Interés moratorio cancelado</th>
                            <th>Destino del Credito</th>
                            <th>Producto</th>
                            <th>Sub producto</th>
                            <th>Monto original</th>
                            <th>Saldo capital crédito</th>
                            <th>Saldo capital normal</th>
                            <th>Saldo capital vencido</th>
                            <th>N° Días de atraso</th>
                            <th>Riesgo individual</th>
                            <th>Situacion contable</th>
                            <th>Interés por cobrar</th>
                            <th>Nombre de asesor de credito</th>
                            <th>TEA</th>
                            <th>Tipo de cronograma</th>
                            <th>Monto Cuota</th>
                            <th>Monto Garantia</th>
                            <th>N° Integrantes</th>
                            <!-- <th></th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @php $contador = 0; @endphp
                        @foreach ($creditos as $credito)
                            @php
                                $contador++;
                                $cliente = $credito->creditoClientes->first()->clientes; // Obtener el primer cliente relacionado

                                // Filtrar cuotas pagadas donde id_cliente es null
                                $cuotasPagadas = $credito
                                    ->ingresos()
                                    ->whereHas('cronograma', function ($query) {
                                        $query->whereNull('cliente_id');
                                    })
                                    ->count();

                                // Filtrar cuotas totales donde id_cliente es null
                                $cuotasTotales = $credito->cronograma()->whereNull('cliente_id')->count();

                                $ultimaCuota = $credito
                                    ->cronograma()
                                    ->whereNull('cliente_id')
                                    ->orderBy('fecha', 'desc')
                                    ->first();

                                $fechaUltimaCuota = $ultimaCuota ? $ultimaCuota->fecha : 'No hay cuotas disponibles';

                                // Calcular cuotas pendientes
                                $cuotasPendientes = $cuotasTotales - $cuotasPagadas;

                                $pagadasCronogramaIds = $credito
                                    ->ingresos()
                                    ->whereHas('cronograma', function ($query) {
                                        $query->whereNull('cliente_id');
                                    })
                                    ->pluck('cronograma_id');

                                // Filtrar cronograma pagadas y pendientes donde cliente_id es null
                                $cronogramaPagadas = $credito
                                    ->cronograma()
                                    ->whereIn('id', $pagadasCronogramaIds)
                                    ->whereNull('cliente_id')
                                    ->get();
                                $cronogramaPendientes = $credito
                                    ->cronograma()
                                    ->whereNotIn('id', $pagadasCronogramaIds)
                                    ->whereNull('cliente_id')
                                    ->get();

                                $capitalCancelado = $cronogramaPagadas->sum('amortizacion');
                                $interesCancelado = $cronogramaPagadas->last()
                                    ? $cronogramaPagadas->last()->interes
                                    : 0;

                                $interesporcobrar = $cronogramaPendientes->sum('interes');

                                $interesMoratorioCancelado = $credito->ingresos->sum('monto_mora');

                                $now = \Carbon\Carbon::now();

                                $cronogramaPendientesNormal = $cronogramaPendientes->where('fecha', '>', $now);
                                $cronogramaPendientesVencido = $cronogramaPendientes->where('fecha', '<=', $now);

                                // $saldoCapitalNormal=$cronogramaPendientesNormal->last() ? $cronogramaPendientesNormal->last()->amortizacion : 0;
                                // $saldoCapitalVencido = $cronogramaPendientesVencido->last() ? $cronogramaPendientesVencido->last()->amortizacion : 0;
                                // $saldoCapitalCredito = $saldoCapitalNormal + $saldoCapitalVencido;

                                // Obtener la fecha del último pago
                                $ultimoPago = $credito->ingresos()->latest('fecha_pago')->first();
                                $fechaUltimoPago = $ultimoPago ? $ultimoPago->fecha_pago : 'No hay pagos';

                                // Obtener la fecha de vencimiento de la próxima cuota
                                $ultimaCuotaPagada = $credito
                                    ->ingresos()
                                    ->latest('fecha_pago')
                                    ->where('cliente_id', null)
                                    ->first();

                                //dd($ultimaCuotaPagada);

                                if ($ultimaCuotaPagada) {
                                    $proximaCuota = $credito
                                        ->cronograma()
                                        ->where('cliente_id', null) // Filtro para cuotas generales
                                        ->where('id', '>', $ultimaCuotaPagada->cronograma_id)
                                        ->orderBy('fecha')
                                        ->first();

                                    $fechaVencimientoProximaCuota = $proximaCuota
                                        ? $proximaCuota->fecha
                                        : 'No hay próxima cuota';
                                } else {
                                    $primeraCuota = $credito
                                        ->cronograma()
                                        ->where('cliente_id', null) // Filtro para cuotas generales
                                        ->orderBy('fecha')
                                        ->first();
                                    $fechaVencimientoProximaCuota = $primeraCuota
                                        ? $primeraCuota->fecha
                                        : 'No hay cuotas';
                                }

                                // Calcular los días de atraso o los días restantes
                                // $diasAtraso = 0;
                                // if ($fechaVencimientoProximaCuota) {
                                //     $fechaVencimientoProximaCuotaFormatted = \Carbon\Carbon::parse(
                                //         $fechaVencimientoProximaCuota,
                                //     )->format('Y-m-d');
                                //     $fechaActualFormatted = $now->format('Y-m-d');

                                //     if ($fechaVencimientoProximaCuotaFormatted < $fechaActualFormatted) {
                                //         $diasAtraso = \Carbon\Carbon::parse($fechaActualFormatted)->diffInDays(
                                //             $fechaVencimientoProximaCuotaFormatted,
                                //         );
                                //     } else {
                                //         $diasAtraso = -\Carbon\Carbon::parse($fechaActualFormatted)->diffInDays(
                                //             $fechaVencimientoProximaCuotaFormatted,
                                //         );
                                //     }
                                // }

                                $diasAtraso = 0;
                                if ($fechaVencimientoProximaCuota!='No hay próxima cuota' && $fechaVencimientoProximaCuota!='No hay cuotas' ) {
                                    $fechaVencimientoProximaCuotaFormatted = \Carbon\Carbon::parse(
                                        $fechaVencimientoProximaCuota,
                                    )->format('Y-m-d');
                                    $fechaActualFormatted = $now->format('Y-m-d');

                                    if ($fechaVencimientoProximaCuotaFormatted < $fechaActualFormatted) {
                                        $diasAtraso = \Carbon\Carbon::parse($fechaActualFormatted)->diffInDays(
                                            $fechaVencimientoProximaCuotaFormatted,
                                        );
                                    } else {
                                        $diasAtraso = -\Carbon\Carbon::parse($fechaActualFormatted)->diffInDays(
                                            $fechaVencimientoProximaCuotaFormatted,
                                        );
                                    }
                                } else {
                                    $diasAtraso = 0;
                                }

                                // Calcular riesgo individual
                                $riesgoIndividual = 'normal';
                                if ($diasAtraso < 8) {
                                    $riesgoIndividual = 'normal';
                                } elseif ($diasAtraso >= 8 && $diasAtraso <= 30) {
                                    $riesgoIndividual = 'CPP';
                                } elseif ($diasAtraso > 30 && $diasAtraso <= 60) {
                                    $riesgoIndividual = 'Deficiente';
                                } elseif ($diasAtraso > 60 && $diasAtraso <= 120) {
                                    $riesgoIndividual = 'Dudoso';
                                } else {
                                    $riesgoIndividual = 'Pérdida';
                                }

                                // Calcular situación contable
                                $situacionContable = $diasAtraso >= 1 ? 'Vencido' : 'Vigente';

                                $saldoCapitalNormal = 0;
                                $saldoCapitalCredito = $credito->monto_total - $capitalCancelado;

                                $saldoCapitalVencido = $cronogramaPendientesVencido->sum('amortizacion');

                                if ($diasAtraso > 30) {
                                    $saldoCapitalVencido = $credito->monto_total;
                                }

                            @endphp
                            <tr>
                                <td style="text-align: center">{{ $contador }}</td>
                                <td>{{ $credito->nombre_prestamo }}</td>
                                <td>{{ $credito->user->sucursal->id }}</td>
                                <td>{{ $credito->user->sucursal->nombre }}</td>
                                <td>
                                    @if ($credito->correlativos->isNotEmpty())
                                        {{ $credito->correlativos->first()->correlativo }}
                                    @else
                                        No asignado
                                    @endif
                                </td>
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
                                <td>{{ $riesgoIndividual }}</td>
                                <td>{{ $situacionContable }}</td>
                                <td>{{ $interesporcobrar }}</td>
                                <td>{{ $credito->user->name }}</td>
                                <td>{{ $credito->tasa }}</td>
                                <td>{{ $credito->recurrencia }}</td>
                                <td>{{ $credito->cronograma->first()->monto }}</td>
                                <td>{{ $credito->garantia ? $credito->garantia->valor_mercado : '0' }}</td>
                                <td>{{ $credito->cantidad_integrantes }}</td>
                                <!-- <td>{{ $credito->cuotasPagadas }}</td> -->
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <script>
                    $(document).ready(function() {
                        var spanish = {
                            "sProcessing": "Procesando...",
                            "sLengthMenu": "Mostrar _MENU_ registros",
                            "sZeroRecords": "No se encontraron resultados",
                            "sEmptyTable": "Ningún dato disponible en esta tabla",
                            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                            "sInfoPostFix": "",
                            "sSearch": "Buscar:",
                            "sUrl": "",
                            "sInfoThousands": ",",
                            "sLoadingRecords": "Cargando...",
                            "oPaginate": {
                                "sFirst": "Primero",
                                "sLast": "Último",
                                "sNext": "Siguiente",
                                "sPrevious": "Anterior"
                            },
                            "oAria": {
                                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                            }
                        };

                        $('#tablegrupal').DataTable({
                            "paging": true,
                            "lengthChange": false,
                            "searching": true,
                            "ordering": true,
                            "info": true,
                            "language": spanish,
                            "autoWidth": true,
                            "pageLength": 10,
                        });

                        $('#btn-buscar-cliente').on('click', function() {
                            var table = $('#tablegrupal').DataTable();
                            table.search($('#buscar-cliente').val()).draw();
                        });
                    });
                </script>
            </div>
        </div>
    </div>
@endsection
