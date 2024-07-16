<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Caja extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'cajas';
    protected $fillable = ['nombre', 'sucursal_id'];

    public function transacciones()
    {
        return $this->hasMany(CajaTransaccion::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}