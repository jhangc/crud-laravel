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
                        <button class="btn btn-outline-primary" type="button" id="btn-buscar-cliente"><i class="bi bi-search"></i> Buscar</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-tools float-right">
                        <a href="{{ url('/admin/reportes/clientes/export') }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Exportar a Excel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm table-striped table-hover">
                <!-- <thead>
                    <tr>
                        <th>
                            <center>Nro</center>
                        </th>
                        <th>
                            <center>Nombres</center>
                        </th>
                        <th>
                            <center>DNI</center>
                        </th>
                        <th>
                            <center>Monto</center>
                        </th>
                        <th>
                            <center>Cuota</center>
                        </th>
                        <th>
                            <center>Tasa</center>
                        </th>
                        <th>
                            <center>Recurrencia</center>
                        </th>
                        <th>
                            <center>Tiempo</center>
                        </th>
                        <th>
                            <center>Responsable</center>
                        </th>
                    </tr>
                </thead> -->

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
                        <th>Fecha de última refinanciación</th>
                        <th>Fecha de última reprogramación</th>
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
                <!-- <tbody>
                    @php $contador = 0; @endphp

                    @foreach ($creditos as $credito)
                    @php
                    $contador++;
                    $cliente = $credito->creditoClientes->first()->cliente; // Obtener el primer cliente relacionado
                    @endphp
                    <tr>
                        <td style="text-align: center">{{ $contador }}</td>
                        <td>{{ $cliente->nombre }}</td>
                        <td>{{ $cliente->documento_identidad }}</td>
                        <td>{{ $credito->creditoClientes->first()->monto_indivual }}</td>
                        <td>{{ $credito->cronograma->first()->monto }}</td>
                        <td>{{ $credito->tasa }}</td>
                        <td>{{ $credito->recurrencia }}</td>
                        <td>{{ $credito->tiempo }}</td>
                        <td>{{ $credito->user->name }}</td>
                    </tr>
                    @endforeach
                </tbody> -->

                <tbody>
                    @php $contador = 0; @endphp

                    @foreach ($creditos as $credito)
                    @php
                    $contador++;
                    $cliente = $credito->creditoClientes->first()->cliente; // Obtener el primer cliente relacionado
                    @endphp
                    <tr>
                        <td style="text-align: center">{{ $contador }}</td>
                        <td>{{ $cliente->nombre }}</td>
                        <td>{{ $cliente->documento_identidad }}</td>
                        <td>{{ $credito->creditoClientes->first()->monto_indivual }}</td>
                        <td>{{ $credito->cronograma->first()->monto }}</td>
                        <td>{{ $credito->tasa }}</td>
                        <td>{{ $credito->recurrencia }}</td>
                        <td>{{ $credito->tiempo }}</td>
                        <td>{{ $credito->user->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection