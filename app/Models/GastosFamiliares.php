<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GastosFamiliares extends Model
{
    use HasFactory;

    protected $fillable = [
        'descripcion',
        'precio_unitario',
        'id_prestamo',
        'cantidad',
    ];
}

