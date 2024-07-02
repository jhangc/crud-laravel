<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentasMensuales extends Model
{
   
    use HasFactory;

    protected $table = 'ventas_mensuales';

    protected $fillable = [
        'mes',
        'porcentaje',
        'id_prestamo'
    ];

    public function prestamo()
    {
        return $this->belongsTo(credito::class, 'id_prestamo');
    }
}
