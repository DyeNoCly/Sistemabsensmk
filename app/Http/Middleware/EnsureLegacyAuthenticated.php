<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLegacyAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('legacy_user')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
