<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsStrictAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->guest(route('login'));
        }

        $user = auth()->user();
        if ($user->isAdmin()) {
            return $next($request);
        }

        $role = strtolower(trim($user->role ?? ''));
        if (in_array($role, ['admin', 'superadmin', 'super_admin', 'super admin', 'manager'])) {
            return $next($request);
        }

        abort(403, 'Unauthorized access. Full Administrator privileges required.');
    }
}
