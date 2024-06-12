<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credito extends Model
{
    use HasFactory;

    protected $table = 'prestamos'; // Nombre de la tabla en la base de datos

    protected $fillable = [
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
    ];

    protected $dates = [
        'fecha_desembolso',
        'fecha_registro',
        'fecha_fin',
    ];


    public function clientes()
    {
        return $this->belongsToMany(cliente::class, 'Credito_Cliente', 'prestamo_id', 'cliente_id');
    }
}
