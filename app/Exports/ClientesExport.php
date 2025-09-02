<?php

namespace App\Exports;
use Carbon\Carbon;

use App\Models\Credito;
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
        $creditos = Credito::with([
            'clientes', 
            'creditoClientes.clientes', 
            'user.sucursal',
            'cronograma',
            'correlativoPagare',
            'garantia',
            'ingresos'
        ])
        ->withCount('creditoClientes as cliente_creditos_count')
        ->where('activo', 1)
        ->where('estado', 'pagado')
        ->where('producto', '!=', 'grupal')
        ->get();

        return $creditos;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'N°',
            'Tipo Doc',
            'N° Documento',
            'Nombre o Razón social',
            'Genero',
            'Código de agencia',
            'Agencia',
            'N° Pagaré',
            'Fecha de desembolso',
            'Fecha de vencimiento de cuota',
            'N° Cuotas',
            'Periocidad de cuotas',
            'Periodo de gracia',
            'Fecha de último pago',
            'N° Cuotas pagadas',
            'N° Cuotas pendientes',
            'Capital cancelado',
            'Interés cancelado',
            'Interés moratorio cancelado',
            'Destino del credito',
            'Producto',
            'Sub producto',
            'Monto original',
            'Saldo capital crédito',
            'Saldo capital normal',
            'Saldo capital vencido',
            'N° Días de atraso',
            'Riesgo individual',
            'Situacion contable',
            'Interés por cobrar',
            'Nombre del asesor de credito',
            'TEA',
            'Fecha de nacimiento',
            'Profesión/ocupacion',
            'Estado civil interno',
            'Dirección',
            'Distrito',
            'Provincia',
            'Departamento',
            'Monto Cuota',
            'Periocidad Pago',
            'Aprobada Con Excepcion',
            'Tiene Aval',
            'Datos Aval',
            'Tipo Garantia',
            'Monto Garantia',
            'Numero Creditos',
            'Numero celular'
        ];
    }

        /**
     * @param mixed $credito
     * @return array
     */
    public function map($credito): array
    {
        $contador = 0;
        $contador++;
        $cliente = $credito->creditoClientes->first()->clientes; // Obtener el primer cliente relacionado

        $cuotasPagadas = $credito->ingresos->count();
        $cuotasTotales = $credito->cronograma->count();
        $cuotasPendientes = $cuotasTotales - $cuotasPagadas;

        $pagadasCronogramaIds = $credito->ingresos->pluck('cronograma_id');
        $cronogramaPagadas = $credito->cronograma->whereIn('id', $pagadasCronogramaIds);
        $cronogramaPendientes = $credito->cronograma->whereNotIn('id', $pagadasCronogramaIds);

        $capitalCancelado = $cronogramaPagadas->sum('amortizacion');
        $interesCancelado = $cronogramaPagadas->last() ? $cronogramaPagadas->last()->interes : 0; 

        $interesporcobrar = $cronogramaPendientes->sum('interes'); 

        $interesMoratorioCancelado = $credito->ingresos->sum('monto_mora');

        $now = Carbon::now();

        $cronogramaPendientesNormal = $cronogramaPendientes->where('fecha', '>', $now);
        $cronogramaPendientesVencido = $cronogramaPendientes->where('fecha', '<=', $now); 

        $saldoCapitalNormal = $cronogramaPendientesNormal->last() ? $cronogramaPendientesNormal->last()->amortizacion : 0;
        $saldoCapitalVencido = $cronogramaPendientesVencido->last() ? $cronogramaPendientesVencido->last()->amortizacion : 0;
        $saldoCapitalCredito = $saldoCapitalNormal + $saldoCapitalVencido;

        // Obtener la fecha del último pago
        $ultimoPago = $credito->ingresos()->latest('fecha_pago')->first();
        $fechaUltimoPago = $ultimoPago ? $ultimoPago->fecha_pago : 'No hay pagos'; 

        // Obtener la fecha de vencimiento de la próxima cuota
        $ultimaCuotaPagada = $credito->ingresos()->latest('fecha_pago')->first();
        if ($ultimaCuotaPagada) {
            $proximaCuota = $credito->cronograma()->where('id', '>', $ultimaCuotaPagada->cronograma_id)->orderBy('fecha')->first();
            $fechaVencimientoProximaCuota = $proximaCuota ? $proximaCuota->fecha : 'No hay próxima cuota';
        } else {
            $primeraCuota = $credito->cronograma()->orderBy('fecha')->first();
            $fechaVencimientoProximaCuota = $primeraCuota ? $primeraCuota->fecha : 'No hay cuotas';
        }

        // Calcular los días de atraso
        $diasAtraso = 0;
        if ($fechaVencimientoProximaCuota < $now) {
            $diasAtraso = $now->diffInDays($fechaVencimientoProximaCuota);
        }

        return [
            $contador,
            'DNI',
            $cliente->documento_identidad,
            $cliente->nombre,
            $cliente->sexo,
            $credito->user->sucursal->id,
            $credito->user->sucursal->nombre,
            $credito->correlativoPagare ? $credito->correlativoPagare->correlativo : 'No tiene',
            $credito->fecha_desembolso,
            $fechaVencimientoProximaCuota,
            $credito->tiempo,
            $credito->recurrencia,
            $credito->periodo_gracia_dias,
            $fechaUltimoPago,
            $cuotasPagadas,
            $cuotasPendientes,
            $capitalCancelado,
            $interesCancelado,
            $interesMoratorioCancelado,
            $credito->destino,
            $credito->producto,
            $credito->subproducto,
            $credito->monto_total,
            $saldoCapitalCredito,
            $saldoCapitalNormal,
            $saldoCapitalVencido,
            $diasAtraso,
            'Normal',
            'Vigente',
            $interesporcobrar,
            $credito->user->name,
            $credito->tasa,
            $cliente->fecha_nacimiento,
            $cliente->profesion,
            $cliente->estado_civil,
            $cliente->direccion,
            $cliente->distrito->dis_nombre,
            $cliente->distrito->provincia->pro_nombre,
            $cliente->distrito->provincia->departamento->dep_nombre,
            $credito->cronograma->first()->monto,
            $credito->recurrencia,
            'Aprobada Con Excepcion',
            $cliente->aval ? 'Sí' : 'No',
            $cliente->aval,
            $credito->garantia ? $credito->garantia->descripcion : 'Sin garantía',
            $credito->garantia ? $credito->garantia->valor_mercado : '0',
            $credito->cliente_creditos_count,
            $cliente->telefono
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


