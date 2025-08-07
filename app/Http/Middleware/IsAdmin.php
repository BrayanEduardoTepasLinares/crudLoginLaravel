<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica si el usuario está autenticado y si el nombre de su rol es 'admin'
        if (auth()->check() && auth()->user()->rol && auth()->user()->rol->name === 'admin') {
            return $next($request);
        }

        // Si no es un usuario con rol 'admin', aborta la petición
        abort(403, 'Acceso no autorizado.');
    }
}
