<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Cliente;
class FileController extends Controller
{
    public function getFoto($filename)
    {
        $cliente = Cliente::findOrFail($filename);

        $path =$cliente->foto;

        if (!Storage::exists($path)) {
            abort(404);
        }

        $file = Storage::get($path);
        $type = Storage::mimeType($path);

        return response($file, 200)->header('Content-Type', $type);
    }

    public function getPdf($filename)
    {
        $cliente = Cliente::findOrFail($filename);

        $path =$cliente->dni_pdf;


        if (!Storage::exists($path)) {
            abort(404);
        }

        $file = Storage::get($path);
        $type = Storage::mimeType($path);

        return response($file, 200)->header('Content-Type', $type);
    }
}
