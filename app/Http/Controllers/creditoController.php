<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class creditoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.creditos.index');
    }

    public function viewaprobar()
    {
        return view('admin.creditos.aprobar');
    }

    public function viewsupervisar()
    {
        return view('admin.creditos.supervisar');
    }

    public function viewarqueo()
    {
        return view('admin.caja.arqueo');
    }

    public function viewhabilitarcaja()
    {
        return view('admin.caja.habilitar');
    }

    public function viewpagarcredito()
    {
        return view('admin.caja.pagarcredito');
    }

    public function viewpagares()
    {
        return view('admin.caja.pagares');
    }


    public function viewcargarcompromiso()
    {
        return view('admin.cobranza.cargarcompromiso');
    }

    public function viewcarta()
    {
        return view('admin.cobranza.carta');
    }

    public function viewgenerarcompromiso()
    {
        return view('admin.cobranza.generarcompromiso');
    }

    public function viewgenerarnotificacion()
    {
        return view('admin.cobranza.generarnotificacion');
    }

    public function viewegresos()
    {
        return view('admin.transacciones.egresos');
    }

    public function viewingresos()
    {
        return view('admin.transacciones.ingresos');
    }

    public function viewprestamosactivos()
    {
        return view('admin.reportes.prestamosactivos');
    }

    public function viewprestamosvencidos()
    {
        return view('admin.reportes.prestamosvencidos');
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
}
