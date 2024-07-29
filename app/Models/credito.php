<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credito extends Model
{
    use HasFactory;

    protected $table = 'prestamos'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'user_id',
        'tipo',
        'producto',
        'subproducto',
        'destino',
        'id_cliente',
        'recurrencia',
        'tasa',
        'tiempo',
        'monto_total',
        'fecha_desembolso',
        'periodo_gracia_dias',
        'fecha_registro',
        'fecha_fin',
        'descripcion_negocio',
        'nombre_prestamo',
        'cantidad_integrantes',
        'estado',
        'categoria',
        'foto_grupal',
        'activo',
        'porcentaje_credito',
        'comentario_asesor',
        'comentario_administrador',
    ];

    protected $dates = [
        'fecha_desembolso',
        'fecha_registro',
        'fecha_fin',
    ];


    public function clientes()
    {
        return $this->belongsToMany(cliente::class, 'credito_cliente', 'prestamo_id', 'cliente_id');
    }

    public function creditoClientes()
    {
        return $this->hasMany(CreditoCliente::class, 'prestamo_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id','id'); // Relación con el usuario que creó el préstamo
    }

    public function cronograma()
    {
        return $this->hasMany(Cronograma::class, 'id_prestamo', 'id');
    }

}
