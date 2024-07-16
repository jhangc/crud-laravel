<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sucursal extends Model
{
    use HasFactory;
    protected $table = 'sucursales';
    protected $fillable = ['nombre', 'direccion', 'telefono', 'email', 'activo'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function inicioOperaciones()
    {
        return $this->hasMany(InicioOperaciones::class);
    }
}
