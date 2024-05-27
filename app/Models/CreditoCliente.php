<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditoCliente extends Model
{
    protected $table = 'credito_cliente';

    public $timestamps = false;

    public function prestamos()
    {
        return $this->belongsToMany(credito::class, 'prestamo_id', 'cliente_id');
    }

    public function clientes()
    {
        return $this->belongsToMany(cliente::class, 'cliente_id', 'prestamo_id');
    }
}

