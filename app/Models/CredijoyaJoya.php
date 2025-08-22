<?php
// app/Models/CredijoyaJoya.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CredijoyaJoya extends Model
{
    protected $table = 'credijoya_joyas';
    protected $fillable = [
        'prestamo_id','kilate','precio_gramo','peso_bruto','peso_neto',
        'piezas','descripcion','valor_tasacion','devuelta','codigo'
    ];

    public function credito()
    {
        return $this->belongsTo(Credito::class, 'prestamo_id');
    }
}
