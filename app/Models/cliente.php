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
        'distrito_id',
        'activo',
        'direccion_laboral',
        'lugar_nacimiento',
        'fecha_nacimiento',
        'profesion',
        'estado_civil',
        'conyugue',
        'dni_conyugue',
        'direccion_conyugue',
        'foto',
        'dni_pdf',
        'actividad_economica',
        'sexo',
        'referencia',
        'aval',
        'numero_dni_aval',
        'dni_aval',
        'direccion_aval',
    ];
    

    protected $dates = ['fecha_nacimiento'];
    
    public function creditos()
    {
        return $this->belongsToMany(credito::class, 'Credito_Cliente', 'cliente_id', 'prestamo_id');
    }

    public function distrito()
    {
        return $this->belongsTo(Distrito::class, 'distrito_id', 'dis_id');
    }

    public function totalcreditos()
    {
        return $this->hasManyThrough(credito::class, CreditoCliente::class, 'cliente_id', 'id', 'id', 'prestamo_id');
    }

}
