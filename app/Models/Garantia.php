<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Garantia extends Model
{
    use HasFactory;

    protected $fillable = [
        'descripcion',
        'valor_mercado',
        'valor_realizacion',
        'valor_gravamen',
        'documento_pdf',
        'estado',
        'id_prestamo',
    ];
}

