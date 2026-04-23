<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLegacyRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->session()->get('legacy_user');

        if (! is_array($user)) {
            return redirect()->route('login');
        }

        if (empty($roles)) {
            abort(403, 'Role akses belum ditentukan.');
        }

        if (! in_array($user['level'] ?? '', $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
