<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class RegistrarActividadSesion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            Cache::put(
                'user-last-activity-' . Auth::id(),
                now(),
                now()->addMinutes((int) config('session.lifetime', 120))
            );
        }

        return $next($request);
    }
}
