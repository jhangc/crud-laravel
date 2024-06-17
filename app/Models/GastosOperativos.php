<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GastosOperativos extends Model
{
    use HasFactory;

    protected $fillable = [
        'descripcion',
        'precio_unitario',
        'cantidad',
        'id_prestamo',
        'acciones',
        'unidad',
        'mes1',
        'mes2',
        'mes3',
        'mes4',
        'mes5',
        'mes6',
        'mes7',
        'mes8',
        'mes9',
        'mes10',
        'mes11',
        'mes12'
    ];
}

