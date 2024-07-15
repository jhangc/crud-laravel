<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InicioOperaciones extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'permiso_abierto'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
