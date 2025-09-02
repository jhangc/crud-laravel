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
}