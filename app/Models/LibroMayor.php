<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibroMayor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cuenta_id', 'periodo', 'saldo_inicial', 'total_debe', 'total_haber', 'saldo_final'
    ];

    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class);
    }
}
