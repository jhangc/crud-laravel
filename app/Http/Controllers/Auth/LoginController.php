<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    protected $estadoBloqueado = false;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function attemptLogin(Request $request)
    {
        $user = User::where($this->username(), $request->input($this->username()))->first();

        if ($user && (string) $user->estado === '0') {
            $this->estadoBloqueado = true;
            return false;
        }

        return $this->guard()->attempt(
            $this->credentials($request),
            $request->boolean('remember')
        );
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        if ($this->estadoBloqueado) {
            throw ValidationException::withMessages([
                $this->username() => ['Este usuario está inactivo y no puede iniciar sesión.'],
            ]);
        }

        return parent::sendFailedLoginResponse($request);
    }
}
