<?php
namespace App\Http\Controllers;

use App\Models\Cliente;

class FileController extends Controller
{
    public function getFoto($filename)
    {
        $cliente = Cliente::findOrFail($filename);

        return $this->serveStorageFile($cliente->foto);
    }

    public function getPdf($filename)
    {
        $cliente = Cliente::findOrFail($filename);

        return $this->serveStorageFile($cliente->dni_pdf);
    }

    /**
     * Sirve un archivo del disco 'local' (storage/app) SIN usar el facade Storage,
     * para no depender de la extensión PHP `fileinfo` (que en el servidor puede no
     * estar habilitada y provocaba el error: Class "finfo" not found).
     * El tipo MIME se deduce por la extensión del archivo.
     */
    private function serveStorageFile(?string $path)
    {
        if (! $path) {
            abort(404);
        }

        $relative = ltrim(str_replace('\\', '/', $path), '/');
        $full     = storage_path('app/' . $relative);

        // Respaldo: por si la ruta guardada apunta directo a /public.
        if (! is_file($full)) {
            $full = public_path($relative);
        }

        if (! is_file($full)) {
            abort(404);
        }

        return response(file_get_contents($full), 200)
            ->header('Content-Type', $this->mimeFromExtension($full));
    }

    /** Devuelve el MIME según la extensión, sin depender de la extensión fileinfo. */
    private function mimeFromExtension(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
            'bmp'  => 'image/bmp',
            'svg'  => 'image/svg+xml',
            'pdf'  => 'application/pdf',
        ][$ext] ?? 'application/octet-stream';
    }
}
