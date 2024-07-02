<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activos extends Model
{
    use HasFactory;

    protected $table = 'activos'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'prestamo_id',
        'cuentas_por_cobrar',
        'saldo_en_caja_bancos',
        'adelanto_a_proveedores',
        'otros',
    ];

    public function prestamo()
    {
        return $this->belongsTo(credito::class, 'prestamo_id');
    }
}
