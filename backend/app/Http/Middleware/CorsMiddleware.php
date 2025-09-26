<?php

namespace App\Http\Middleware;


use Closure; use Illuminate\Http\Request;
class CorsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $res = $next($request);
        $res->headers->set('Access-Control-Allow-Origin', '*');
        $res->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $res->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        return $res;
    }
}