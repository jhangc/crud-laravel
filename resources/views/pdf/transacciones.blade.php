<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transacciones de Caja</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 20px;
        }
        .content table {
            width: 100%;
            border-collapse: collapse;
        }
        .content th, .content td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .content th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Transacciones de Caja</h2>
        <p>Fecha: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
    </div>
    <div class="content">
        <h3>Monto Inicial de la Caja</h3>
        <p>S/.{{ number_format($ultimaTransaccion->monto_apertura, 2) }}</p>

        <h3>Ingresos</h3>
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
                @foreach($ingresos as $ingreso)
                <tr>
                    <td>{{ $ingreso->hora_pago }}</td>
                    <td>{{ number_format($ingreso->monto, 2) }}</td>
                    <td>{{ $ingreso->cliente->nombre }}</td>
                    <td>{{ $ingreso->transaccion->user->name }}</td>
                    <td>{{ $ingreso->numero_cuota }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p>Total de Ingresos: S/.{{ number_format($ingresos->sum('monto'), 2) }}</p>

        <h3>Egresos</h3>
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
                @foreach($egresosConClientes as $egreso)
                <tr>
                    <td>{{ $egreso['hora_egreso'] }}</td>
                    <td>{{ $egreso['monto'] }}</td>
                    <td>{{ implode(', ', $egreso['clientes']) }}</td>
                    <td>{{ $egreso['usuario'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p>Total de Egresos: S/.{{ number_format($egresos->sum('monto'), 2) }}</p>

        <h3>Gastos</h3>
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
                @foreach($gastosConDetalles as $gasto)
                <tr>
                    <td>{{ $gasto['hora_gasto'] }}</td>
                    <td>{{ $gasto['monto'] }}</td>
                    <td>{{ $gasto['numero_documento'] }}</td>
                    <td>{{ $gasto['usuario'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p>Total de Gastos: S/.{{ number_format($gastosConDetalles->sum('monto'), 2) }}</p>

        <h3>Datos de Cierre</h3>
        <table>
            <tr>
                <th>Saldo Final Esperado</th>
                <td>S/.{{ $saldoFinalEsperado }}</td>
            </tr>
            <tr>
                <th>Saldo Final Real</th>
                <td>S/.{{ $saldoFinalReal }}</td>
            </tr>
            <tr>
                <th>Desajuste</th>
                <td>S/.{{ $desajuste }}</td>
            </tr>
            <tr>
                <th>Mensaje de Desajuste</th>
                <td>
                    @if ($desajuste == 0)
                        No hay desajuste.
                    @elseif ($desajuste > 0)
                        Sobró dinero en la caja.
                    @else
                      Falta dinero en la caja.
                    @endif
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
