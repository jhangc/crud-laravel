<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PagoContable extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ingreso_id', 'fecha_pago', 'monto_pago', 'monto_interes', 'monto_capital', 'estado'
    ];

    public function ingreso()
    {
        return $this->belongsTo(Ingreso::class);
    }
}