<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GastoProducir extends Model
{
    use HasFactory;

    protected $table = 'gasto_producir';

    protected $fillable = [
        'nombre_actividad',
        'cantidad_terreno',
        'produccion_total',
        'precio_kg',
    ];
}
