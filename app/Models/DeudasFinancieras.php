<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeudasFinancieras extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_entidad',
        'saldo_capital',
        'cuota',
        'tiempo_restante',
        'prestamo_id',
        'estado',
    ];
}

