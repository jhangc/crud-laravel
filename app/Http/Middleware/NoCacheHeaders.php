<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Evita que el navegador cachee las páginas (incluido el back/forward cache).
 *
 * Sin esto, tras cerrar sesión el navegador podía restaurar la página de login
 * desde su caché con un token CSRF viejo; al intentar loguear, ese token ya no
 * coincidía con la sesión nueva y salía el error "419 / sesión expirada".
 * Además, en un sistema financiero conviene que el botón "atrás" no muestre
 * páginas con datos sensibles después del logout.
 */
class NoCacheHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

        return $response;
    }
}
