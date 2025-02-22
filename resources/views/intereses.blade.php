<!-- resources/views/reporte/intereses.blade.php -->
<table border="1">
    <thead>
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
                <td>{{ $fila->enero }}</td>
                <td>{{ $fila->febrero }}</td>
                <td>{{ $fila->marzo }}</td>
                <td>{{ $fila->abril }}</td>
                <td>{{ $fila->mayo }}</td>
                <td>{{ $fila->junio }}</td>
                <td>{{ $fila->julio }}</td>
                <td>{{ $fila->agosto }}</td>
                <td>{{ $fila->septiembre }}</td>
                <td>{{ $fila->octubre }}</td>
                <td>{{ $fila->noviembre }}</td>
                <td>{{ $fila->diciembre }}</td>
                <td>{{ $fila->total_interes }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="2"><strong>Total</strong></td>
            <td>{{ $totalesMeses['enero'] }}</td>
            <td>{{ $totalesMeses['febrero'] }}</td>
            <td>{{ $totalesMeses['marzo'] }}</td>
            <td>{{ $totalesMeses['abril'] }}</td>
            <td>{{ $totalesMeses['mayo'] }}</td>
            <td>{{ $totalesMeses['junio'] }}</td>
            <td>{{ $totalesMeses['julio'] }}</td>
            <td>{{ $totalesMeses['agosto'] }}</td>
            <td>{{ $totalesMeses['septiembre'] }}</td>
            <td>{{ $totalesMeses['octubre'] }}</td>
            <td>{{ $totalesMeses['noviembre'] }}</td>
            <td>{{ $totalesMeses['diciembre'] }}</td>
            <td>{{ $totalesMeses['total_interes'] }}</td>
        </tr>
    </tbody>
</table>