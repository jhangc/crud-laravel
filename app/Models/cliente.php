<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'sucursal_id',
        'nombre',
        'documento_identidad',
        'telefono',
        'email',
        'direccion',
        'activo',
        'direccion_laboral',
        'lugar_nacimiento',
        'fecha_nacimiento',
        'profesion',
        'estado_civil',
        'conyugue',
        'dni_conyugue',
        'foto',
        'dni_pdf',
        'actividad_economica',
        'sexo',
        'referencia',
        'aval',
        'dni_aval',
    ];
    

    protected $dates = ['fecha_nacimiento'];
    

}
