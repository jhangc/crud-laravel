<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


// Model: Cuenta
class Cuenta extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'codigo', 'nombre', 'tipo', 'nivel', 'estado'
    ];
}
