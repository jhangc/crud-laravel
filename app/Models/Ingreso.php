<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingreso extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaccion_id',
        'cliente_id',
        'prestamo_id',
        'cronograma_id',
        'numero_cuota',
        'monto_cuota',
        'fecha_pago',
        'hora_pago',
        'monto',
        'monto_mora',          // Nuevo campo
        'dias_mora',           // Nuevo campo
        'diferencia', 
        'porcentaje_mora',     // Nuevo campo
        'monto_total_pago_final', // Nuevo campo
        'sucursal_id',
        'interes_pago_capital',
        'pago_capital',
    ];

    public function transaccion()
    {
        return $this->belongsTo(CajaTransaccion::class);
    }

    public function cliente()
    {
        return $this->belongsTo(cliente::class);
    }

    public function prestamo()
    {
        return $this->belongsTo(credito::class);
    }

    public function cronograma()
    {
        return $this->belongsTo(Cronograma::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
