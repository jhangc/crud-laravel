@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Reporte de Total de Clientes</h1>
    </div>
        <style>
        .btn-success {
            background-color: #28a745 !important; /* Verde intenso */
            border-color: #28a745 !important; /* Borde del mismo color */
            font-weight: bold; /* Texto en negrita */
        }

        .btn-success:hover {
            background-color: #218838 !important; /* Verde más oscuro al pasar el mouse */
            border-color: #1e7e34 !important; /* Ajuste del borde */
        }
</style>

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
                    <!-- <div class="col-md-6">
                        <div class="card-tools float-right">
                            <a href="{{ url('/admin/reportes/credito/exportarcreditosindividual') }}"
                                class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Exportar a Excel</a>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="tableindividual" class="table table-bordered table-sm table-striped table-hover">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Tipo Doc</th>
                            <th>N° Documento</th>
                            <th>Nombre o Razón social</th>
                            <th>Genero</th>
                            <th>Código de agencia</th>
                            <th>Agencia</th>
                            <th>N° Pagaré</th>
                            <th>Fecha de desembolso</th>
                            <th>Fecha de vencimiento de cuota</th>
                            <th>N° Cuotas</th>
                            <th>Periocidad de cuotas</th>
                            <th>Periodo de gracia</th>
                            <th>Fecha de último pago</th>
                            <!-- <th>Fecha de última refinanciación</th>
                            <th>Fecha de última reprogramación</th> -->
                            <th>N° Cuotas pagadas</th>
                            <th>N° Cuotas pendientes</th>
                            <th>Capital cancelado</th>
                            <th>Interés cancelado</th>
                            <th>Interés moratorio cancelado</th>
                            <th>Destino del credito</th>
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
                            <th>Nombre del asesor de credito</th>
                            <th>TEA</th>
                            <th>Fecha de nacimiento</th>
                            <th>Profesión/ocupacion</th>
                            <th>Estado civil interno</th>
                            <th>Dirección</th>
                            <th>Distrito</th>
                            <th>Provincia</th>
                            <th>Departamento</th>
                            <th>Monto Cuota</th>
                            <th>Periocidad Pago</th>
                            <th>Aprobada Con Excepcion</th>
                            <th>Tiene Aval</th>
                            <th>Datos Aval</th>
                            <th>Tipo Garantia</th>
                            <th>Monto Garantia</th>
                            <th>Numero Creditos</th>
                            <th>Numero celular</th>
                        </tr>
                    </thead>


                    <tbody>

                        @php $contador = 0; @endphp

                        @foreach ($creditos as $credito)
                            @php
                                $contador++;
                                $cliente = $credito->creditoClientes->first()->clientes; // Obtener el primer cliente relacionado

                                $cuotasPagadas = $credito->ingresos->count();
                                $cuotasTotales = $credito->cronograma->count();
                                $cuotasPendientes = $cuotasTotales - $cuotasPagadas;

                                $pagadasCronogramaIds = $credito->ingresos->pluck('cronograma_id');
                                $cronogramaPagadas = $credito->cronograma->whereIn('id', $pagadasCronogramaIds);
                                $cronogramaPendientes = $credito->cronograma->whereNotIn('id', $pagadasCronogramaIds);

                                $capitalCancelado = $cronogramaPagadas->sum('amortizacion');
                                $interesCancelado = $cronogramaPagadas->sum('interes');

                                $interesporcobrar = $cronogramaPendientes->sum('interes');

                                //$capitalCancelado = $ultimoCronogramaPagado ? $ultimoCronogramaPagado->capital : 0;
                                //$interesCancelado = $ultimoCronogramaPagado ? $ultimoCronogramaPagado->interes : 0;

                                $interesMoratorioCancelado = $credito->ingresos->sum('monto_mora');

                                $now = \Carbon\Carbon::now();

                                $cronogramaPendientesNormal = $cronogramaPendientes->where('fecha', '>', $now);
                                $cronogramaPendientesVencido = $cronogramaPendientes->where('fecha', '<=', $now);

                                // Obtener la fecha del último pago
                                $ultimoPago = $credito->ingresos()->latest('fecha_pago')->first();
                                $fechaUltimoPago = $ultimoPago ? $ultimoPago->fecha_pago : 'No hay pagos';

                                // Obtener la fecha de vencimiento de la próxima cuota
                                $ultimaCuotaPagada = $credito->ingresos()->latest('fecha_pago')->first();
                                if ($ultimaCuotaPagada) {
                                    $proximaCuota = $credito
                                        ->cronograma()
                                        ->where('id', '>', $ultimaCuotaPagada->cronograma_id)
                                        ->orderBy('fecha')
                                        ->first();
                                    $fechaVencimientoProximaCuota = $proximaCuota
                                        ? $proximaCuota->fecha
                                        : 'No hay próxima cuota';
                                } else {
                                    $primeraCuota = $credito->cronograma()->orderBy('fecha')->first();
                                    $fechaVencimientoProximaCuota = $primeraCuota
                                        ? $primeraCuota->fecha
                                        : 'No hay cuotas';
                                }

                                // Calcular los días de atraso o los días restantes
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
                                <td>DNI</td>
                                <td>{{ $cliente->documento_identidad }}</td>
                                <td>{{ $cliente->nombre }}</td>
                                <td>{{ $cliente->sexo }}</td>
                                <td>{{ $credito->user->sucursal->id }}</td>
                                <td>{{ $credito->user->sucursal->nombre }}</td>
                                <td>{{ $credito->correlativoPagare ? $credito->correlativoPagare->correlativo : 'No tiene' }}
                                </td>
                                <td>{{ $credito->fecha_desembolso }}</td>
                                <td>{{ $fechaVencimientoProximaCuota }}</td>
                                <td>{{ $credito->tiempo }}</td>
                                <td>{{ $credito->recurrencia }}</td>
                                <td>{{ $credito->periodo_gracia_dias }}</td>
                                <td>{{ $fechaUltimoPago }}</td>
                                <!-- <td>Fecha de última refinanciación</td>
                            <td>Fecha de última reprogramación</td> -->
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
                                <td>{{ $saldoCapitalNormal }}</td> <!-- Mostrar saldo capital normal -->
                                <td>{{ $saldoCapitalVencido }}</td> <!-- Mostrar saldo capital vencido -->
                                <td>{{ $diasAtraso }}</td>
                                <td>{{ $riesgoIndividual }}</td>
                                <td>{{ $situacionContable }}</td>
                                <td>{{ $interesporcobrar }}</td>
                                <td>{{ $credito->user->name }}</td>
                                <td>{{ $credito->tasa }}</td>
                                <td>{{ $cliente->fecha_nacimiento }}</td>
                                <td>{{ $cliente->profesion }}</td>
                                <td>{{ $cliente->estado_civil }}</td>
                                <td>{{ $cliente->direccion }}</td>
                                <td>{{ $cliente->distrito->dis_nombre }}</td>
                                <td>{{ $cliente->distrito->provincia->pro_nombre }}</td>
                                <td>{{ $cliente->distrito->provincia->departamento->dep_nombre }}</td>
                                <td>{{ $credito->cronograma->first()->monto }}</td>
                                <td>{{ $credito->recurrencia }}</td>
                                <td>Aprobada Con Excepcion</td>
                                <td>
                                    @if ($cliente->aval)
                                        Sí
                                    @else
                                        No
                                    @endif
                                </td>
                                <td>{{ $cliente->aval }}</td>
                                <td>{{ $credito->garantia ? $credito->garantia->descripcion : 'Sin garantía' }}</td>
                                <td>{{ $credito->garantia ? $credito->garantia->valor_mercado : '0' }}</td>
                                <td>{{ $credito->cliente_creditos_count }}</td>
                                <td>{{ $cliente->telefono }}</td>
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

                        $('#tableindividual').DataTable({
                            "paging": true,
                            "lengthChange": false,
                            "searching": true,
                            "ordering": true,
                            "info": true,
                            "language": spanish,
                            "autoWidth": true,
                            "pageLength": 10,
                            dom: 'Bfrtip', // Agregar botones
                            buttons: [
                                {
                                    extend: 'excelHtml5',
                                    text: '<i class="bi bi-file-earmark-excel"></i> Exportar a Excel',
                                    className: 'btn btn-success text-white', // Estilo mejorado
                                    title: 'Reporte de Clientes',
                                    exportOptions: {
                                        columns: ':visible'
                                    }
                                }
                            ]
                        });

                        $('#btn-buscar-cliente').on('click', function() {
                            var table = $('#tableindividual').DataTable();
                            table.search($('#buscar-cliente').val()).draw();
                        });
                    });
                </script>
            </div>
        </div>
    </div>
@endsection
