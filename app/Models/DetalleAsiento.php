<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetalleAsiento extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asiento_contable_id', 'cuenta_id', 'monto', 'tipo'
    ];

    public function asiento()
    {
        return $this->belongsTo(AsientoContable::class);
    }

    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class);
    }
}
