<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CronogramaGrupal extends Model
{
    use HasFactory;

    protected $table = 'cronograma_grupal';

    protected $fillable = [
        'prestamo_id',
        'fecha',
        'monto',
        'numero',
    ];

    public function prestamo()
    {
        return $this->belongsTo(credito::class);
    }
}