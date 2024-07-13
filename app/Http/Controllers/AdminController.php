<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\credito;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(){
        $usuarios = User::all();
        return view('admin.index',['usuarios'=>$usuarios]);
    }

    public function aprobar(Request $request){
        $credito = credito::find($request->id);
        $credito->estado = 'aprobado';
        $credito->comentario_administrador = $request->comentarioadministrador;
        $credito->save();

        return response()->json([
            'redirect' => route('creditos.aprobar'),
            'mensaje' => 'El crédito ha sido aprobado correctamente',
            'icono' => 'success'
        ]);
    }

    public function rechazar(Request $request){
        $credito = credito::find($request->id);
        $credito->estado = 'rechazado';
        $credito->comentario_administrador = $request->comentarioadministrador;
        $credito->save();

        return response()->json([
            'redirect' => route('creditos.aprobar'),
            'mensaje' => 'El crédito ha sido rechazado correctamente',
            'icono' => 'success'
        ]);
    }

    public function observar(Request $request){
        $credito = credito::find($request->id);
        $credito->estado = 'observado';
        $credito->comentario_administrador = $request->comentarioadministrador;
        $credito->save();

        return response()->json([
            'redirect' => route('creditos.aprobar'),
            'mensaje' => 'El crédito ha sido observado correctamente',
            'icono' => 'success'
        ]);
    }

    public function guardar(Request $request){
        $credito = credito::find($request->id);
        if ($request->estado == 'rechazado por sistema') {
            $credito->estado = 'rechazado por sistema';
        } else {
            $credito->estado = 'revisado';
        }
        $credito->comentario_asesor = $request->comentario;
        $credito->save();
    
        return response()->json([
            'redirect' => route('creditos.index'),
            'mensaje' => 'El crédito ha sido ' . ($request->estado == 'rechazado por sistema' ? 'rechazado por el sistema' : 'revisado') . ' correctamente',
            'icono' => 'success'
        ]);
    }

}
