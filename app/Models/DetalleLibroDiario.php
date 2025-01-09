<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleLibroDiario extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'libro_diario_id', 'cuenta_id', 'monto', 'tipo'
    ];

    public function libroDiario()
    {
        return $this->belongsTo(LibroDiario::class);
    }

    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class);
    }
}
