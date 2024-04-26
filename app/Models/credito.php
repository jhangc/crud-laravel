<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class credito extends Model
{
    use HasFactory;

    protected $table = 'credito'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'cliente_id',
        'tipo_credito',
        'monto',
        'tasa_interes',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'activo'
    ];
}
