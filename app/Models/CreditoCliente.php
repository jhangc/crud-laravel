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
        return $this->belongsToMany(credito::class, 'prestamo_id', 'cliente_id');
    }

    public function cliente()
    {
        return $this->belongsTo(cliente::class, 'cliente_id','id');
    }
}

