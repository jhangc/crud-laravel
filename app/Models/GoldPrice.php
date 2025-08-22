<?php
// app/Models/GoldPrice.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoldPrice extends Model
{
    use HasFactory;

    protected $fillable = ['kilate', 'precio', 'fecha'];

    protected $casts = [
        'kilate' => 'integer',
        'precio' => 'decimal:2',
        'fecha'  => 'date',
    ];
}
