<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gasto extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'gastos';
    protected $fillable = [
        'caja_transaccion_id',
        'user_id',
        'monto_gasto',
        'numero_doc',
        'serie_doc',
        'numero_documento_responsable',
        'nombre_responsable',
        'archivo'
    ];

    public function cajaTransaccion()
    {
        return $this->belongsTo(CajaTransaccion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
