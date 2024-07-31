<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngresoExtra extends Model
{
    use HasFactory;
    protected $table = 'ingresos_extras'; 

    protected $fillable = [
        'user_id',
        'monto',
        'motivo',
        'numero_documento',
        'caja_transaccion_id',
        'serie_documento',
        'observaciones',
        'archivo'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cajaTransaccion()
    {
        return $this->belongsTo(CajaTransaccion::class);
    }
}
