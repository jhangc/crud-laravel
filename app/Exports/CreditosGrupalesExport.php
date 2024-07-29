<?php

namespace App\Exports;

use App\Models\Credito;
use Maatwebsite\Excel\Concerns\FromCollection;

class CreditosGrupalesExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Credito::all();
    }
}
