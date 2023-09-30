<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
{
    /**
     * Check role of the login user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param array $roles
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!in_array(auth()->user()->role, $roles)) {
            abort(404);
        }

        return $next($request);
    }
}
