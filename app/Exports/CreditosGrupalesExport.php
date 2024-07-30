<?php

namespace App\Exports;

use App\Models\credito;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class CreditosGrupalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $user = auth()->user();
        $roles = $user->roles->pluck('name');

        if ($roles->contains('Administrador')) {
            $creditos = credito::with([
                'clientes',
                'creditoClientes.clientes',
                'user.sucursal',
                'cronograma' => function($query) {
                    $query->whereNull('cliente_id'); // Filtro para cuotas generales
                },
                'garantia',
                'correlativos' => function ($query) {
                    $query->whereNull('id_cliente');
                },
                'ingresos'
            ])
            ->where('activo', 1)
            ->where('estado', 'pagado')
            ->where('producto', 'grupal')
            ->get();
        } else {
            $creditos = credito::with([
                'clientes',
                'creditoClientes.clientes',
                'user.sucursal',
                'cronograma' => function($query) {
                    $query->whereNull('cliente_id'); // Filtro para cuotas generales
                },
                'garantia',
                'correlativos' => function ($query) {
                    $query->whereNull('id_cliente');
                },
                'ingresos'
            ])
            ->where('activo', 1)
            ->where('estado', 'pagado')
            ->where('producto', 'grupal')
            ->where('user_id', $user->id) // Filtrar por el usuario autenticado
            ->get();
        }

        return $creditos;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'N°',
            'Nombre del Grupo',
            'Código de agencia',
            'Agencia',
            'Código del crédito Grupal',
            'Codigo de Prestamo',
            'Fecha de desembolso',
            'Fecha de vencimiento',
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
            'Destino del Credito',
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
            'Nombre de asesor de credito',
            'TEA',
            'Tipo de cronograma',
            'Monto Cuota',
            'Monto Garantia',
            'N° Integrantes'
        ];
    }

    /**
     * @param mixed $credito
     * @return array
     */
    public function map($credito): array
    {
        static $contador = 0;
        $contador++;
        $cliente = $credito->creditoClientes->first()->clientes; // Obtener el primer cliente relacionado

        $cuotasPagadas = $credito->ingresos()->whereHas('cronograma', function($query) {
            $query->whereNull('cliente_id');
        })->count();

        $cuotasTotales = $credito->cronograma()->whereNull('cliente_id')->count();
        $cuotasPendientes = $cuotasTotales - $cuotasPagadas;

        $pagadasCronogramaIds = $credito->ingresos()->whereHas('cronograma', function($query) {
            $query->whereNull('cliente_id');
        })->pluck('cronograma_id');

        $cronogramaPagadas = $credito->cronograma()->whereIn('id', $pagadasCronogramaIds)->whereNull('cliente_id')->get();
        $cronogramaPendientes = $credito->cronograma()->whereNotIn('id', $pagadasCronogramaIds)->whereNull('cliente_id')->get();

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

        $ultimoPago = $credito->ingresos()->latest('fecha_pago')->first();
        $fechaUltimoPago = $ultimoPago ? $ultimoPago->fecha_pago : 'No hay pagos';

        $ultimaCuotaPagada = $credito->ingresos()->latest('fecha_pago')->first();
        if ($ultimaCuotaPagada) {
            $proximaCuota = $credito->cronograma()
                ->where('cliente_id', null)
                ->where('id', '>', $ultimaCuotaPagada->cronograma_id)
                ->orderBy('fecha')
                ->first();
            $fechaVencimientoProximaCuota = $proximaCuota ? $proximaCuota->fecha : 'No hay próxima cuota';
        } else {
            $primeraCuota = $credito->cronograma()
                ->where('cliente_id', null)
                ->orderBy('fecha')
                ->first();
            $fechaVencimientoProximaCuota = $primeraCuota ? $primeraCuota->fecha : 'No hay cuotas';
        }

        $diasAtraso = 0;
        if ($fechaVencimientoProximaCuota < $now) {
            $diasAtraso = $now->diffInDays($fechaVencimientoProximaCuota);
        }

        return [
            $contador,
            $credito->nombre_prestamo,
            $credito->user->sucursal->id,
            $credito->user->sucursal->nombre,
            $credito->correlativos->isNotEmpty() ? $credito->correlativos->first()->correlativo : 'No asignado',
            $credito->id,
            $credito->fecha_desembolso,
            $credito->fecha_fin,
            $ultimaCuotaPagada ? $ultimaCuotaPagada->cronograma_id : 'No hay cuotas',
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
            $credito->recurrencia,
            $credito->cronograma->where('cliente_id', null)->first() ? $credito->cronograma->where('cliente_id', null)->first()->monto : 'No hay cuotas',
            $credito->garantia ? $credito->garantia->valor_mercado : '0',
            $credito->cantidad_integrantes
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
            1    => ['font' => ['bold' => true], 'alignment' => ['horizontal' => 'center']],
            'A1:AW1' => ['borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]],
        ];
    }
}

