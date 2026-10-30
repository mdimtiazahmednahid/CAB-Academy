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
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        \App\Support\DatabaseSchemaRepair::ensureSchema();

        if (!auth()->check()) {
            return redirect()->guest(route('login'));
        }

        $user = auth()->user();
        if ($user->isAdmin() || $user->isInstructor()) {
            return $next($request);
        }

        $role = strtolower(trim($user->role ?? ''));
        if (in_array($role, ['admin', 'superadmin', 'super_admin', 'super admin', 'manager', 'instructor'])) {
            return $next($request);
        }

        abort(403, 'Unauthorized access. Administrator or Instructor privileges required.');
    }
}
