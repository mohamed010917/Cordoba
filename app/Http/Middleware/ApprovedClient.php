<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApprovedClient
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user?->is_approved) {
            return redirect()
                ->route('pending-approval')
                ->with('error', 'Your account is still pending approval.');
        }

        return $next($request);
    }
}
