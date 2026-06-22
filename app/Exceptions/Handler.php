<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // En vez de mostrar la pantalla "419 Page Expired", si el token CSRF venció
        // (sesión expirada) se redirige al login con un mensaje claro y la sesión limpia,
        // para que el usuario simplemente vuelva a ingresar. Las peticiones AJAX reciben 419.
        $this->renderable(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesión expirada. Recarga la página e inténtalo de nuevo.'], 419);
            }

            return redirect()->route('login')
                ->withInput($request->except('password', 'password_confirmation', '_token'))
                ->with('status', 'Tu sesión expiró. Por favor, inicia sesión nuevamente.');
        });
    }
}
