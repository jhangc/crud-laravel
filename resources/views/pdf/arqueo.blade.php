<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Arqueo de Caja</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        h1, h2, h3 {
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Arqueo de Caja</h1>
    <h3>Fecha: {{ \Carbon\Carbon::parse($transaccion->updated_at)->format('d/m/Y H:i:s') }}</h3>
    <h3>Usuario: {{ $usuario->name }}</h3>

    <h2>Billetes</h2>
    <table>
        <thead>
            <tr>
                <th>Denominación</th>
                <th>Cantidad</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datosCierre['billetes'] as $denominacion => $cantidad)
                <tr>
                    <td>S/. {{ $denominacion }}</td>
                    <td>{{ $cantidad }}</td>
                    <td>S/. {{ number_format($denominacion * $cantidad, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Monedas</h2>
    <table>
        <thead>
            <tr>
                <th>Denominación</th>
                <th>Cantidad</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datosCierre['monedas'] as $denominacion => $cantidad)
                <tr>
                    <td>S/. {{ number_format($denominacion, 2) }}</td>
                    <td>{{ $cantidad }}</td>
                    <td>S/. {{ number_format($denominacion * $cantidad, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Depósitos</h2>
    <p>Total Depósitos: S/. {{ number_format($datosCierre['depositos'], 2) }}</p>

    <h2>Ingresos</h2>
    <table>
        <thead>
            <tr>
                <th>Hora de Pago</th>
                <th>Monto</th>
                <th>Cliente</th>
                <th>Usuario</th>
                <th>Cuota</th>
            </tr>
        </thead>
        <tbody>
            @php $totalIngresos = 0; @endphp
            @foreach ($ingresos as $ingreso)
                <tr>
                    <td>{{ $ingreso->hora_pago }}</td>
                    <td>S/. {{ number_format(floatval($ingreso->monto), 2) }}</td>
                    <td>{{ $ingreso->cliente->nombre }}</td>
                    <td>{{ $ingreso->transaccion->user->name }}</td>
                    <td>{{ $ingreso->numero_cuota }}</td>
                </tr>
                @php $totalIngresos += floatval($ingreso->monto); @endphp
            @endforeach
        </tbody>
    </table>
    <h3>Total de Ingresos: S/. {{ number_format($totalIngresos, 2) }}</h3>

    <h2>Egresos</h2>
    <table>
        <thead>
            <tr>
                <th>Hora de Egreso</th>
                <th>Monto</th>
                <th>Clientes</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            @php $totalEgresos = 0; @endphp
            @foreach ($egresosConClientes as $egreso)
                <tr>
                    <td>{{ $egreso['hora_egreso'] }}</td>
                    <td>S/. {{ number_format(floatval($egreso['monto']), 2) }}</td>
                    <td>{{ implode(', ', $egreso['clientes']) }}</td>
                    <td>{{ $egreso['usuario'] }}</td>
                </tr>
                @php $totalEgresos += floatval($egreso['monto']); @endphp
            @endforeach
        </tbody>
    </table>
    <h3>Total de Egresos: S/. {{ number_format($totalEgresos, 2) }}</h3>

    <h2>Gastos</h2>
    <table>
        <thead>
            <tr>
                <th>Hora de Gasto</th>
                <th>Monto</th>
                <th>Número de Documento</th>
                <th>Usuario</th>
            </tr>
        </thead>
        <tbody>
            @php $totalGastos = 0; @endphp
            @foreach ($gastosConDetalles as $gasto)
                <tr>
                    <td>{{ $gasto['hora_gasto'] }}</td>
                    <td>S/. {{ number_format(floatval($gasto['monto']), 2) }}</td>
                    <td>{{ $gasto['numero_documento'] }}</td>
                    <td>{{ $gasto['usuario'] }}</td>
                </tr>
                @php $totalGastos += floatval($gasto['monto']); @endphp
            @endforeach
        </tbody>
    </table>
    <h3>Total de Gastos: S/. {{ number_format($totalGastos, 2) }}</h3>
</body>
</html>
