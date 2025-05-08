<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CtsUsuario extends Model
{
    use HasFactory;

    protected $table = 'cts_usuarios';

    protected $fillable = [
        'user_id',
        'numero_cuenta',
        'saldo_disponible',
        'fecha_ultimo_deposito',
    ];

    // Relaciones

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function depositos()
    {
        return $this->hasMany(DepositoCts::class, 'cts_usuario_id');
    }

}
