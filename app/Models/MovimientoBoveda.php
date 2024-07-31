<?php
// app/Models/MovimientoBoveda.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoBoveda extends Model
{
    use HasFactory;

    protected $table = 'movimientos_boveda';

    protected $fillable = [
        'boveda_id',
        'user_id',
        'monto',
        'numero_documento',
        'serie_documento',
        'tipo',
        'motivo',
        'observacion',
        'archivo',
    ];

    public function boveda()
    {
        return $this->belongsTo(Boveda::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
