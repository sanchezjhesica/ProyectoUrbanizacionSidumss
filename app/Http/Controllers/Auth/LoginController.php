<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter; // <-- Importante
use Illuminate\Support\Str;                 // <-- Importante

class LoginController extends Controller
{
    public function showLoginForm() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 1. Clave única combinando el email y la IP del usuario
        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());
        
        $maxAttempts = 3;       // Número de intentos permitidos (puedes cambiarlo)
        $decaySeconds = 600;    // 10 minutos en segundos (10 * 60)

        // 2. Comprobar si superó el límite de intentos
        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);

            return back()->withErrors([
                'email' => "Demasiados intentos fallidos. Su cuenta ha sido bloqueada temporalmente. Intente nuevamente en {$minutes} minuto(s).",
            ])->onlyInput('email');
        }

        // 3. Intentar autenticar al usuario
        if (Auth::attempt($credentials)) {
            // Si el inicio de sesión es exitoso, reiniciamos el contador de intentos
            RateLimiter::clear($throttleKey);

            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->id_rol == 1) {
                return redirect()->intended('admin/dashboard');
            } elseif ($user->id_rol == 2) {
                return redirect()->intended('operador/dashboard');
            } elseif ($user->id_rol == 3) {
                return redirect()->intended('propietario/dashboard');
            }
        }

        // 4. Si falla la contraseña, sumamos un intento fallido con el temporizador de 10 min
        RateLimiter::hit($throttleKey, $decaySeconds);

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}