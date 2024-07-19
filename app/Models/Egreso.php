<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Egreso extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaccion_id',
        'prestamo_id',
        'fecha_egreso',
        'hora_egreso',
        'monto',
        'sucursal_id',
    ];

    public function transaccion()
    {
        return $this->belongsTo(CajaTransaccion::class);
    }

    public function prestamo()
    {
        return $this->belongsTo(credito::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
