<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositoCts extends Model
{
    use HasFactory;

    protected $table = 'deposito_cts';

    protected $fillable = [
        'cts_usuario_id',
        'monto',
        'fecha_deposito',
        'realizado_por',
        'caja_transaccion_id',
        'tipo_transaccion',
        'estado',
    ];

    // Relaciones

    public function ctsUsuario()
    {
        return $this->belongsTo(CtsUsuario::class, 'cts_usuario_id');
    }

    public function realizadoPor()
    {
        return $this->belongsTo(User::class, 'realizado_por');
    }

    public function cajaTransaccion()
    {
        return $this->belongsTo(CajaTransaccion::class, 'caja_transaccion_id');
    }
}
