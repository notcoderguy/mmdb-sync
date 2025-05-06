<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanRegister
{
    public function handle(Request $request, Closure $next): Response
    {
        if (User::count() > 0) {
            return redirect()->route('login')->with('error', 'Registration is disabled');
        }

        return $next($request);
    }
}
