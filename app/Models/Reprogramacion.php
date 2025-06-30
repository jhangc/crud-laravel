<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reprogramacion extends Model
{
    use SoftDeletes;

    protected $table = 'reprogramaciones';

    protected $fillable = [
        'usuario_id',
        'admin_id',
        'credito_id',
        'cuotas_pendientes', // Cantidad de cuotas pendientes que se reprograman
        'tasa_interes',
        'fecha_reprogramar',
        'capital_restante',
        'nuevo_numero_cuotas', // Cantidad de cuotas que se amplía
        'interes_restante',
        'estado',
        'observaciones',
        'comentario_admin',
    ];

    /**
     * Relación al usuario que solicita.
     */
    public function solicitante()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación al administrador que aprueba/rechaza.
     */
    public function administrador()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Relación al crédito.
     */
    public function credito()
    {
        return $this->belongsTo(credito::class, 'credito_id');
    }
}
