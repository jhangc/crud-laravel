<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\credito;
use App\Models\Cronograma;

class CronogramaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // public function vercuota($id){
    // Obtener todas las cuotas asociadas al crédito desde la tabla cronograma
    public function vercuota($id){
    // Obtener todas las cuotas asociadas al crédito desde la tabla cronograma
    $cuotas = Cronograma::where('id_prestamo', $id)->get();

    // Retornar la vista parcial con las cuotas
    return view('admin.creditos.cuotas', compact('cuotas'));
}

    // $cuotas = Cronograma::where('id_prestamo', $id)->get();

    // // Retornar las cuotas a la vista
    // // Pasar los clientes activos a la vista
    // // return view('admin.creditos.cuotas', ['cuotas' => $cuotas]);
    // return view('admin.creditos.cuotas', compact('cuotas'));
    // //
    
    // }
}
