<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAdminRole
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && (Auth::user()->role === 'ADMIN' || Auth::user()->role === 'SUPERADMIN')) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
