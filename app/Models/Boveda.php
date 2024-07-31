<?php

// app/Models/Boveda.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Boveda extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'boveda';

    protected $fillable = [
        'sucursal_id',
        'monto_inicio',
        'fecha_inicio',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoBoveda::class);
    }
}
