<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CajaTransaccion extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'caja_transacciones';
    protected $fillable = [
        'caja_id',
        'user_id',
        'sucursal_id',
        'monto_apertura',
        'json_apertura',
        'json_cierre',
        'hora_apertura',
        'hora_cierre',
        'fecha_apertura',
        'fecha_cierre',
        'monto_cierre',
        'cantidad_ingresos',
        'cantidad_egresos'
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
