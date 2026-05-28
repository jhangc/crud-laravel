<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;

class Cronograma extends Model
{
    use HasFactory
    , SoftDeletes
    ;
    protected $table = 'cronograma'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'fecha',
        'monto',
        'numero',
        'id_prestamo',
        'cliente_id',
        'capital',
        'interes',
        'amortizacion',
        'saldo_deuda',
        'monto_capital',
        'intereses_capital',
        'pago_capital',
        'nuevo_saldo_deuda',
    ];

    protected $dates = [
        'fecha_vencimiento',
        // otros campos de fecha
    ];

    public function clientes()
    {
        return $this->belongsToMany(Cliente::class, 'cliente_id');
    }

    public function credito()
    {
        return $this->belongsToMany(Credito::class, 'id_prestamo');
    }

    public function ingresos()
    {
        return $this->hasMany(Ingreso::class, 'cronograma_id');
    }

    /**
     * Saldo y mora vigente de la cuota, derivados del historial de Ingresos.
     *
     * La mora corre sobre el saldo que va quedando: en cada abono se reinicia el
     * conteo a partir de ese día sobre el nuevo saldo. "Pagado a cuota" de cada
     * ingreso = monto - monto_mora. Para una cuota sin abonos coincide con la mora
     * clásica (monto * porMil/1000 * días vencidos).
     */
    public function saldoYMora(float $porMil = 1.5): array
    {
        $monto = round((float) $this->monto, 2);
        $vto   = \Carbon\Carbon::parse($this->fecha)->startOfDay();
        $hoy   = \Carbon\Carbon::now()->startOfDay();

        $ingresos = $this->ingresos()
            ->orderBy('fecha_pago')
            ->orderBy('id')
            ->get();

        $balance     = $monto;
        $accruedMora = 0.0;
        $paidMora    = 0.0;
        $cursor      = $vto->copy();

        foreach ($ingresos as $ing) {
            $payDate = \Carbon\Carbon::parse($ing->fecha_pago)->startOfDay();

            // Devengar mora desde el último corte hasta este abono, sobre el saldo vigente.
            if ($payDate->greaterThan($vto) && $balance > 0.009) {
                $desde = $cursor->greaterThan($vto) ? $cursor : $vto;
                $dias  = max(0, $desde->diffInDays($payDate));
                $accruedMora += ($balance * $porMil / 1000) * $dias;
            }

            $pagadoCuota = round((float) $ing->monto - (float) $ing->monto_mora, 2);
            if ($pagadoCuota > 0) {
                $balance = round($balance - $pagadoCuota, 2);
                if ($balance < 0) $balance = 0.0;
            }
            $paidMora += (float) $ing->monto_mora;

            if ($payDate->greaterThan($cursor)) $cursor = $payDate->copy();
        }

        // Mora devengada desde el último corte hasta hoy, sobre el saldo restante.
        $diasMora = 0;
        if ($hoy->greaterThan($vto) && $balance > 0.009) {
            $desde    = $cursor->greaterThan($vto) ? $cursor : $vto;
            $diasMora = max(0, $desde->diffInDays($hoy));
            $accruedMora += ($balance * $porMil / 1000) * $diasMora;
        }

        $moraVigente = round($accruedMora - $paidMora, 2);
        if ($moraVigente < 0) $moraVigente = 0.0;

        return [
            'saldo'       => round(max($balance, 0), 2),
            'mora'        => $moraVigente,
            'dias'        => $diasMora,
            'porcentaje'  => $porMil,
            'fecha_corte' => $cursor->toDateString(),
        ];
    }
}