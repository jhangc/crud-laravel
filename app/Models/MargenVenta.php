<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MargenVenta extends Model
{
    use HasFactory;
    protected $table = 'margen_ventas';

    protected $fillable = [
        'actividad_economica',
        'giro_economico',
        'margen_utilidad',
    ];
}
