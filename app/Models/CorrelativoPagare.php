<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorrelativoPagare extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'correlativo_pagare';

    protected $fillable = [
        'serie',
        'numero',
        'id_prestamo',
        'correlativo'
    ];

    public function credito()
    {
        return $this->belongsTo(credito::class, 'id_prestamo');
    }

    public static function generateCorrelativo($id_prestamo)
    {
        $currentYear = now()->format('y'); // Get last two digits of the current year
        
        // Check if the prestamo already has a correlativo
        $existingCorrelativo = self::where('id_prestamo', $id_prestamo)->first();
        if ($existingCorrelativo) {
            return $existingCorrelativo;
        }

        // Get the last record to determine the next numero
        $lastRecord = self::orderBy('numero', 'desc')->first();
        $nextNumero = $lastRecord ? $lastRecord->numero + 1 : 1;

        // Format the next numero to 4 digits with leading zeros
        $formattedNumero = str_pad($nextNumero, 4, '0', STR_PAD_LEFT);
        $correlativo = $formattedNumero . '-' . $currentYear;

        // Create a new correlativo_pagare record
        $newCorrelativo = self::create([
            'serie' => "0001",
            'numero' => $nextNumero,
            'id_prestamo' => $id_prestamo,
            'correlativo' => $correlativo,
        ]);

        return $newCorrelativo;
    }
}
