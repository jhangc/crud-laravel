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
    ];
}

