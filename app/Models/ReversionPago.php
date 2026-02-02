<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReversionPago extends Model
{
    protected $table = 'reversiones_pagos';

    protected $fillable = [
        'ingreso_id',
        'prestamo_id',
        'user_id',
        'monto',
        'motivo',
        'detalles',
    ];

    public function ingreso(): BelongsTo
    {
        return $this->belongsTo(Ingreso::class);
    }

    public function prestamo(): BelongsTo
    {
        return $this->belongsTo(Credito::class, 'prestamo_id', 'id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
