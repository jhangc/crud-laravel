<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cronograma extends Model
{
    use HasFactory;
    protected $table = 'cronograma'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'fecha',
        'monto',
        'numero',
        'id_prestamo',
        'cliente_id'
    ];
    public function clientes()
    {
        return $this->belongsToMany(cliente::class,'cliente_id');
    }
}
