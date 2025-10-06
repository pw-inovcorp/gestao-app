<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!$request->user()->isActive()) {
            abort(403, 'Conta inativa.');
        }

        if ($request->user()->role !== $role && !$request->user()->isAdmin()) {
            abort(403, 'Sem permissão.');
        }

        return $next($request);
    }
}
