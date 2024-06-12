<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyeccionesVentas extends Model
{
    use HasFactory;

    protected $fillable = [
        'descripcion_producto',
        'unidad_medida',
        'frecuencia_compra',
        'unidades_compradas',
        'unidades_vendidas',
        'stock_verificado',
        'precio_compra',
        'precio_venta',
        'id_prestamo',
        'estado',
        'proporcion_ventas', // Añadir este campo
    ];
}
