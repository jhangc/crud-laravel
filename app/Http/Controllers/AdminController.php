<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Credito;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(){
        $usuarios = User::all();
        return view('admin.index',['usuarios'=>$usuarios]);
    }

    public function aprobar(Request $request){
        $credito = Credito::find($request->id);
        $credito->estado = 'Revisado';
        $credito->comentario_asesor = $request->comentario;
        $credito->save();

        return response()->json([
            'redirect' => route('creditos.index'),
            'mensaje' => 'El crédito ha sido aprobado correctamente',
            'icono' => 'success'
        ]);
    }

    public function rechazar(Request $request){
        $credito = Credito::find($request->id);
        $credito->estado = 'Pendiente';
        $credito->comentario_asesor = $request->comentario;
        $credito->save();

        return response()->json([
            'redirect' => route('creditos.index'),
            'mensaje' => 'El crédito ha sido rechazado correctamente',
            'icono' => 'success'
        ]);
    }

    public function guardar(Request $request){
        $credito = Credito::find($request->id);
        $credito->estado = 'revisado';
        $credito->comentario_asesor = $request->comentario;
        $credito->save();

        return response()->json([
            'redirect' => route('creditos.index'),
            'mensaje' => 'El crédito ha sido revisado correctamente',
            'icono' => 'success'
        ]);
    }

}
