<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoAgricola extends Model
{
    use HasFactory;

    protected $table = 'productos_agricolas';

    protected $fillable = [
        'id_prestamo',
        'nombre_actividad',
        'unidad_medida_siembra',
        'hectareas',
        'cantidad_cultivar',
        'unidad_medida_venta',
        'rendimiento_unidad_siembra',
        'ciclo_productivo_meses',
        'mes_inicio',
    ];

    public function prestamo()
    {
        return $this->belongsTo(Credito::class, 'id_prestamo');
    }
}
