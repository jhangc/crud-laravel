<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InicioDesembolso extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inicio_desembolsos';

    protected $fillable = [
        'user_id',
        'sucursal_id',
        'permiso_abierto',
        'estado',
        'documento_autorizacion',
    ];

    // Relaciones

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }
}
