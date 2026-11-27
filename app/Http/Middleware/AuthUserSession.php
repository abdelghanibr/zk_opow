<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthUserSession
{
    public function handle(Request $request, Closure $next)
    {
        if (! session()->has('auth_user')) {
            return redirect()->route('login.form');
        }

        return $next($request);
    }
}
