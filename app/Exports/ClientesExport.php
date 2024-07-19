<?php

namespace App\Exports;

use App\Models\cliente;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientesExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Obtiene todos los clientes activos
        return cliente::where('activo', 1)->get([
            'sucursal_id',
            'nombre',
            'documento_identidad',
            'telefono',
            'email',
            'direccion',
            'distrito_id',
            'activo',
            'direccion_laboral',
            'lugar_nacimiento',
            'fecha_nacimiento',
            'profesion',
            'estado_civil',
            'conyugue',
            'dni_conyugue',
            'direccion_conyugue',
            'actividad_economica',
            'sexo',
            'referencia',
            'aval',
            'numero_dni_aval',
            'direccion_aval'
        ]);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Sucursal ID',
            'Nombre',
            'DNI',
            'Teléfono',
            'Email',
            'Dirección',
            'Distrito ID',
            'Activo',
            'Dirección Laboral',
            'Lugar de Nacimiento',
            'Fecha de Nacimiento',
            'Profesión',
            'Estado Civil',
            'Conyugue',
            'DNI Conyugue',
            'Dirección Conyugue',
            'Actividad Económica',
            'Sexo',
            'Referencia',
            'Aval',
            'Número DNI Aval',
            'Dirección Aval'
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
            'A1:U1' => ['borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]],
        ];
    }
}


