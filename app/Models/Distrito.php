<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Distrito extends Model
{
    use SoftDeletes;

    protected $table = 'distritos';
    protected $primaryKey = 'dis_id';
    protected $fillable = ['dis_nombre', 'pro_id'];

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'pro_id', 'pro_id');
    }
}
