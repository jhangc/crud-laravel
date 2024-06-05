<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boleta extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_boleta',
        'monto_boleta',
        'descuento_boleta',
        'total_boleta',
        'id_prestamo'
    ];
    
}
