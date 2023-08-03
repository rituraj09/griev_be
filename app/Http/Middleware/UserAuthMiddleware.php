<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response; 
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class UserAuthMiddleware extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$guards)
    {
        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $next($request);
            }
        }

        return response('Unauthorized.', 401);
    }
}
