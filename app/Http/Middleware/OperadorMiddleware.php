<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class OperadorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request):
     */
    public function handle($request, $next) {
    if (Auth::check() && Auth::user()->id_rol == 2) {
        return $next($request);
    }
    return redirect('/')->with('error', 'No tienes permiso de Administrador.');
}
}
