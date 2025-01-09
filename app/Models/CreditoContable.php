<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditoContable extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'prestamo_id', 'fecha_otorgamiento', 'monto_credito', 'descripcion', 'estado'
    ];

    public function prestamo()
    {
        return $this->belongsTo(Credito::class);
    }
}
