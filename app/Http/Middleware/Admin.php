<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! \App\Models\Admin::isLogin()) {
            return redirect()->route('LoginPageAdmin');
        }

        return $next($request);
    }
}
