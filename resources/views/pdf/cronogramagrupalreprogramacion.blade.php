<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cronograma de Créditos Reprogramados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0.5cm 0.5cm;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        h1,
        h2,
        h3 {
            text-align: center;
            margin: 0;
        }

        .contenido {
            margin-top: 20px;
        }

        .firma {
            margin-top: 40px;
            text-align: center;
        }

        .page-break {
            page-break-before: always;
        }

        .firma-table,
        .firma-table tr,
        .firma-table td,
        .firma-table th {
            border: none !important;
            border-collapse: collapse;
        }
    </style>
</head>

<body>
    @if ($prestamo->categoria == 'grupal')
        <h2>Reprogramacion de Cronograma</h2>
        <table style="border: none !important; width: 100%; margin-bottom:0px">
            <tr>
                <td><b>Agencia:</b></td>
                <td>{{ $sucursal->nombre }}</td>
                <td><b>Tipo de Crédito:</b></td>
                <td>{{ $prestamo->tipo }}</td>
                <td><b>Producto:</b></td>
                <td>{{ $prestamo->producto }}</td>
            </tr>
            <tr>
                <td><b>Nombre del grupo:</b></td>
                <td>{{ $prestamo->nombre_prestamo }}</td>
                <td><b>Moneda:</b></td>
                <td>Soles</td>
                <td><b>Monto Reprogramado (S/.):</b></td>
                <td>{{ number_format(round($monto_desembolso, 1), 2, '.', ',') }}</td>
            </tr>
            <tr>
                <td><b>Fecha de Desembolso:</b></td>
                <td>{{ $fecha_desembolso->format('d-m-Y') }}</td>
                <td><b>Tasa (%):</b></td>
                <td>{{ $prestamo->tasa }}</td>
                <td><b>Periodo:</b></td>
                <td>{{ $prestamo->recurrencia }}</td>
            </tr>
            <tr>
                <td><b>Asesor de Crédito:</b></td>
                <td>{{ $responsable->name }}</td>
                <td><b>Código:</b></td>
                <td>{{ $correlativosGenerales->correlativo ?? 0 }}</td>
                <td></td>
                <td></td>
            </tr>
        </table>
        <br>
    @endif

    <!-- Cronograma General -->
    @if ($prestamo->categoria == 'grupal')
        <h3 style="text-align: center;">Cronograma Grupal</h3>
    @else
        <h3 style="text-align: center;">Cronograma Reprogramado</h3>
    @endif
    @if ($prestamo->categoria == 'grupal')
        <table class="principal contenido">
            <thead>
                <tr>
                    <th>N° CUOTA</th>
                    <th>Fecha de Pago</th>
                    <th>Detalle</th>
                    <th>Capital</th>
                    <th>Interés</th>
                    <th>Amortización</th>

                    <th>Total de Cuota (S/.)</th>

                </tr>
            </thead>
            @php
                $totalInteresGeneral = 0;
                $totalAmortizacionGeneral = 0;
                $totalCuotaGeneral = 0;
            @endphp
            <tbody>
                @foreach ($cuotas->whereNull('cliente_id')->where('numero', '>=', $numero_c) as $cuota)
<tr>
                        <td>{{ $cuota->numero - $numero_c + 1 }}</td>
                        <td>{{ $cuota->fecha }}</td>
                        <td>{{ $cuota->detalle ?? 'Cuota' }}</td>
                        <td>
                            {{ $cuota->pago_capital == null
                                ? number_format($cuota->saldo_deuda, 2)
                                : number_format($cuota->nuevo_saldo_deuda ?? 0, 2) }}
                        </td>
                        <td>
                            @if ($cuota->pago_capital == null)
{{ number_format($cuota->interes, 2) }}
                                @php $totalInteresGeneral += $cuota->interes; @endphp
@else
{{ number_format($cuota->intereses_capital ?? 0, 2) }}
                                @php $totalInteresGeneral += ($cuota->intereses_capital ?? 0); @endphp
@endif
                        </td>
                        <td>
                            @if ($cuota->pago_capital == null)
{{ number_format($cuota->amortizacion ?? 0, 2) }}
                                @php $totalAmortizacionGeneral += $cuota->amortizacion; @endphp
@else
{{ number_format($cuota->monto_capital ?? 0, 2) }}
                                @php $totalAmortizacionGeneral += ($cuota->monto_capital ?? 0); @endphp
@endif
                        </td>
                        <td>
                            @if ($cuota->pago_capital == null)
{{ number_format($cuota->monto, 2) }}
                                @php $totalCuotaGeneral += $cuota->monto; @endphp
@else
{{ number_format($cuota->intereses_capital + $cuota->monto_capital ?? 0, 2) }}
                                @php $totalCuotaGeneral += ($cuota->intereses_capital + $cuota->monto_capital) ?? 0; @endphp
@endif
                        </td>
                    </tr>
@endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                    <td style="text-align:right;"><b>Total</b></td>
                    <td>{{ number_format($totalInteresGeneral, 2) }}</td>
                    <td>{{ number_format($totalAmortizacionGeneral, 2) }}</td>
                    <td>{{ number_format($totalCuotaGeneral, 2) }}</td>
                </tr>
            </tfoot>
        </table>
@endif


    <!-- Cronograma Individual: cada uno en una nueva página -->
    @foreach ($prestamo->clientes as $cliente)
@if ($prestamo->categoria == 'grupal')
<div class="page-break"></div>
@endif
        <h3 style="text-align: center;">Cronograma
            @if ($prestamo->categoria == 'grupal')
Individual
@endif
            de: {{ $cliente->nombre }}
        </h3>

        <table style="border: none !important; width: 100%; margin-bottom:0px">
            <tr>
                <td><b>Agencia:</b></td>
                <td>{{ $sucursal->nombre }}</td>
                <td><b>Tipo de Crédito:</b></td>
                <td>{{ $prestamo->tipo }}</td>
                <td><b>Producto:</b></td>
                <td>{{ $prestamo->producto }}</td>
            </tr>
            <tr>
                <td><b>Nombre del grupo:</b></td>
                <td>
                    @if ($prestamo->categoria == 'grupal')
{{ $prestamo->nombre_prestamo }}
@else
Crédito Individual
@endif
                </td>
                <td><b>Moneda:</b></td>
                <td>Soles</td>
                @if ($prestamo->categoria != 'grupal')
<td><b>Monto Reprogramado (S/.):</b></td>
                    <td>{{ number_format(round($monto_desembolso, 1), 2, '.', ',') }}</td>
@else
<td><b>Monto Reprogramado (S/.):</b></td>
                    <td>
    {{ number_format($desembolsoProporcional[$cliente->id] ?? 0, 2, '.', ',') }}
  </td>
@endif


            </tr>
            <tr>
                <td><b>Fecha de Desembolso:</b></td>
                <td>{{ $fecha_desembolso->format('d-m-Y') }}</td>
                <td><b>Tasa (%):</b></td>
                <td>{{ $prestamo->tasa }}</td>
                <td><b>Periodo:</b></td>
                <td>{{ $prestamo->recurrencia }}</td>
            </tr>
            <tr>
                <td><b>Asesor de Crédito:</b></td>
                <td>{{ $responsable->name }}</td>
                <td>
                    @if ($prestamo->categoria == 'grupal')
<b>Código:
@endif
                    </b>
                </td>
                <td>
                    @if ($prestamo->categoria == 'grupal')
@foreach ($correlativosIntegrantes as $correlativo)
@if ($correlativo->id_cliente == $cliente->id)
{{ $correlativo->correlativo ?? 0 }}
@endif
@endforeach
@endif
                </td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <table class="principal contenido">
            <thead>
                <tr>
                    <th>N° CUOTA</th>
                    <th>Fecha de Pago</th>
                    <th>Detalle</th>
                    <th>Capital</th>
                    <th>Interés</th>
                    <th>Amortización</th>

                    <th>Total de Cuota (S/.)</th>
                </tr>
            </thead>
            @php
                $totalInteresInd = 0;
                $totalAmortizacionInd = 0;
                $totalCuotaInd = 0;
            @endphp
            <tbody>
                @foreach ($cuotas->where('cliente_id', $cliente->id)->where('numero', '>=', $numero_c) as $cuota)
                    <tr>
                        <td>{{ $cuota->numero - $numero_c + 1 }}</td>
                        <td>{{ $cuota->fecha }}</td>
                        <td>{{ $cuota->detalle ?? 'Cuota' }}</td>
                        <td>
                            {{ $cuota->pago_capital == null
                                ? number_format($cuota->saldo_deuda, 2)
                                : number_format($cuota->nuevo_saldo_deuda ?? 0, 2) }}
                        </td>
                        <td>
                            @if ($cuota->pago_capital == null)
                                {{ number_format($cuota->interes, 2) }}
                                @php $totalInteresInd += $cuota->interes; @endphp
                            @else
                                {{ number_format($cuota->intereses_capital ?? 0, 2) }}
                                @php $totalInteresInd += ($cuota->intereses_capital ?? 0); @endphp
                            @endif
                        </td>
                        <td>
                            @if ($cuota->pago_capital == null)
                                {{ number_format($cuota->amortizacion ?? 0, 2) }}
                                @php $totalAmortizacionInd += $cuota->amortizacion; @endphp
                            @else
                                {{ number_format($cuota->monto_capital ?? 0, 2) }}
                                @php $totalAmortizacionInd += ($cuota->monto_capital ?? 0); @endphp
                            @endif
                        </td>

                        <td>
                            @if ($cuota->pago_capital == null)
                                {{ number_format($cuota->monto, 2) }}
                                @php $totalCuotaInd += $cuota->monto; @endphp
                            @else
                                {{ number_format($cuota->intereses_capital + $cuota->monto_capital ?? 0, 2) }}
                                @php $totalCuotaInd += ($cuota->intereses_capital + $cuota->monto_capital) ?? 0; @endphp
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                    <td style="text-align:right;"><b>Total</b></td>
                    <td>{{ number_format($totalInteresInd, 2) }}</td>
                    <td>{{ number_format($totalAmortizacionInd, 2) }}</td>

                    <td>{{ number_format($totalCuotaInd, 2) }}</td>
                </tr>
            </tfoot>
        </table>
        <!-- Sección de firmas para cada cronograma individual -->
        <div class="firma">
            <table class="firma-table" style="width: 100%;">
                <tr>
                    <td style="width:50%; text-align:center;">__________________________
                        <br>Auxiliar de Operaciones <br>
                        {{ Auth::user()->name }}
                    </td>
                    <td style="width:50%; text-align:center;">
                        __________________________<br>Cliente<br>{{ $cliente->nombre }}</td>
                </tr>
            </table>
        </div>
    @endforeach

</body>

</html>
