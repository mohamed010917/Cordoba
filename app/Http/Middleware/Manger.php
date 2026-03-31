<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Manger
{
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check() && Auth::user()->hasRole('admin')) {
            return $next($request);
        }
        if (! Auth::check() || Auth::user()->role !== 'manager' || ! Auth::user()->hasRole('manager')) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
