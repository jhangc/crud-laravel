<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditoCliente extends Model
{
    protected $table = 'credito_cliente';

    protected $fillable = [
        'prestamo_id',
        'cliente_id',
        'monto_indivual',
    ];
    
    public $timestamps = false;

    public function prestamos()
    {
        return $this->belongsToMany(Credito::class, 'prestamo_id', 'cliente_id');
    }

    public function clientes()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id','id');
    }
    //no mover  metodos  , crear  nuevos  , puedes joder  otras funciones
    
}

