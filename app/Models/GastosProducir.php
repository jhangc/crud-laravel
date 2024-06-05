<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GastosProducir extends Model
{
    use HasFactory;
    protected $table = 'gastos_producir'; 
    protected $fillable = [
        'descripcion_gasto',
        'precio_unitario',
        'cantidad',
        'total_gasto',
        'id_prestamo'
    ];
}
