<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provincia extends Model
{
    use SoftDeletes;

    protected $table = 'provincias';
    protected $primaryKey = 'pro_id';
    protected $fillable = ['pro_nombre', 'dep_id'];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'dep_id', 'dep_id');
    }
}
