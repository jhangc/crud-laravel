<!-- resources/views/reporte/intereses.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Intereses</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados para impresión -->
    <style>
        @media print {
            .no-print {
                display: none;
            }
            .print-title {
                text-align: center;
                font-size: 1.5em;
                margin-bottom: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #000;
                padding: 8px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <!-- Título del reporte -->
        <h1 class="text-center mb-4 print-title">Reporte de Intereses - Año 2025</h1>

        <!-- Botón para imprimir -->
        <div class="text-end mb-3 no-print">
            <button class="btn btn-primary" onclick="window.print()">Imprimir Reporte</button>
        </div>

        <!-- Tabla de reporte -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID Préstamo</th>
                        <th>Nombre del Préstamo</th>
                        <th>Enero</th>
                        <th>Febrero</th>
                        <th>Marzo</th>
                        <th>Abril</th>
                        <th>Mayo</th>
                        <th>Junio</th>
                        <th>Julio</th>
                        <th>Agosto</th>
                        <th>Septiembre</th>
                        <th>Octubre</th>
                        <th>Noviembre</th>
                        <th>Diciembre</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reporte as $fila)
                        <tr>
                            <td>{{ $fila->id_prestamo }}</td>
                            <td>{{ $fila->nombre_prestamo }}</td>
                            <td>{{ number_format($fila->enero, 2) }}</td>
                            <td>{{ number_format($fila->febrero, 2) }}</td>
                            <td>{{ number_format($fila->marzo, 2) }}</td>
                            <td>{{ number_format($fila->abril, 2) }}</td>
                            <td>{{ number_format($fila->mayo, 2) }}</td>
                            <td>{{ number_format($fila->junio, 2) }}</td>
                            <td>{{ number_format($fila->julio, 2) }}</td>
                            <td>{{ number_format($fila->agosto, 2) }}</td>
                            <td>{{ number_format($fila->septiembre, 2) }}</td>
                            <td>{{ number_format($fila->octubre, 2) }}</td>
                            <td>{{ number_format($fila->noviembre, 2) }}</td>
                            <td>{{ number_format($fila->diciembre, 2) }}</td>
                            <td>{{ number_format($fila->total_interes, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="table-info">
                        <td colspan="2"><strong>Total</strong></td>
                        <td>{{ number_format($totalesMeses['enero'], 2) }}</td>
                        <td>{{ number_format($totalesMeses['febrero'], 2) }}</td>
                        <td>{{ number_format($totalesMeses['marzo'], 2) }}</td>
                        <td>{{ number_format($totalesMeses['abril'], 2) }}</td>
                        <td>{{ number_format($totalesMeses['mayo'], 2) }}</td>
                        <td>{{ number_format($totalesMeses['junio'], 2) }}</td>
                        <td>{{ number_format($totalesMeses['julio'], 2) }}</td>
                        <td>{{ number_format($totalesMeses['agosto'], 2) }}</td>
                        <td>{{ number_format($totalesMeses['septiembre'], 2) }}</td>
                        <td>{{ number_format($totalesMeses['octubre'], 2) }}</td>
                        <td>{{ number_format($totalesMeses['noviembre'], 2) }}</td>
                        <td>{{ number_format($totalesMeses['diciembre'], 2) }}</td>
                        <td>{{ number_format($totalesMeses['total_interes'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS (opcional, solo si necesitas funcionalidades de Bootstrap) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>