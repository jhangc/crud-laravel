<?php

namespace App\Exports;

use App\Models\credito;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class PrestamosExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Obtiene todos los préstamos activos con el nombre del cliente
        return DB::table('prestamos')
            ->where('prestamos.activo', 1)
            ->whereDate('fecha_fin', '<', now()->toDateString())
            ->leftJoin('credito_cliente', 'prestamos.id', '=', 'credito_cliente.prestamo_id')
            ->leftJoin('clientes', 'credito_cliente.cliente_id', '=', 'clientes.id')
            ->select(
                'prestamos.id',
                'prestamos.tipo',
                'prestamos.producto',
                'prestamos.subproducto',
                'prestamos.destino',
                DB::raw('GROUP_CONCAT(clientes.nombre SEPARATOR ", ") as cliente_nombre'),
                'prestamos.recurrencia',
                'prestamos.tasa',
                'prestamos.tiempo',
                'prestamos.monto_total',
                'prestamos.fecha_desembolso',
                'prestamos.periodo_gracia_dias',
                'prestamos.fecha_registro',
                'prestamos.fecha_fin',
                'prestamos.descripcion_negocio',
                'prestamos.nombre_prestamo',
                'prestamos.cantidad_integrantes',
                'prestamos.estado',
                'prestamos.categoria',
                'prestamos.activo as prestamo_activo',
                'prestamos.porcentaje_credito',
                'prestamos.comentario_asesor',
                'prestamos.comentario_administrador'
            )
            ->groupBy(
                'prestamos.id',
                'prestamos.tipo',
                'prestamos.producto',
                'prestamos.subproducto',
                'prestamos.destino',
                'prestamos.recurrencia',
                'prestamos.tasa',
                'prestamos.tiempo',
                'prestamos.monto_total',
                'prestamos.fecha_desembolso',
                'prestamos.periodo_gracia_dias',
                'prestamos.fecha_registro',
                'prestamos.fecha_fin',
                'prestamos.descripcion_negocio',
                'prestamos.nombre_prestamo',
                'prestamos.cantidad_integrantes',
                'prestamos.categoria',
                'prestamos.activo',
                'prestamos.porcentaje_credito',
                'prestamos.comentario_asesor',
                'prestamos.comentario_administrador'
            )
            ->get();
    
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Tipo',
            'Producto',
            'Subproducto',
            'Destino',
            'Nombre Cliente',
            'Recurrencia',
            'Tasa',
            'Tiempo',
            'Monto Total',
            'Fecha Desembolso',
            'Periodo Gracia Dias',
            'Fecha Registro',
            'Fecha Fin',
            'Descripcion Negocio',
            'Nombre Prestamo',
            'Cantidad Integrantes',
            'Categoria',
            'Foto Grupal',
            'Activo',
            'Porcentaje Credito',
            'Comentario Asesor',
            'Comentario Administrador',
        ];
    }

    /**
     * Apply styles to the sheet.
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold and center text
            1    => ['font' => ['bold' => true], 'alignment' => ['horizontal' => 'center']],

            // Apply borders to the entire sheet
            'A1:W1' => ['borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]],
        ];
    }
}
