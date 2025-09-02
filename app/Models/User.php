<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'direccion',
        'sucursal_id', 
        'telefono', // Agregado
        'estado', // Agregado
        'dni',               // nuevo
        'numero_cuenta',     // nuevo
        'fecha_nacimiento',  // nuevo
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function creditos()
    {
        return $this->hasMany(Credito::class, 'id_cliente'); // Asume que 'id_cliente' es la clave foránea en la tabla 'prestamos'
    }
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function ctsUsuario()
    {
        return $this->hasOne(CtsUsuario::class, 'user_id');
    }
}

