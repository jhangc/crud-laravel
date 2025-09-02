<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentasDiarias extends Model
{
    use HasFactory;

    protected $table = 'ventas_diarias'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'prestamo_id',
        'dia',
        'cantidad_maxima',
        'cantidad_minima',
        'promedio',
    ];

    public function prestamo()
    {
        return $this->belongsTo(Credito::class, 'prestamo_id');
    }
}
