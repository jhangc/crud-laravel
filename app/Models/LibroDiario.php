<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class LibroDiario extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fecha', 'descripcion', 'total_debe', 'total_haber', 'estado'
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleLibroDiario::class);
    }
}