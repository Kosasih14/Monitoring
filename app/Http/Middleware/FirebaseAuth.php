<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FirebaseAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('firebase_user')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
