<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        foreach ($roles as $role) {
            if (auth()->user()->hasRole($role)) {
                return $next($request);
            }
        }

        $user = auth()->user();

        if ($user->hasRole('admin')) return redirect('/admin');
        if ($user->hasRole('ogretmen')) return redirect('/ogretmen');
        if ($user->hasRole('veli')) return redirect('/veli');

        return redirect('/');
    }
}
