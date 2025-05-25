<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiVersion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $version = $request->header('Accept-Version');
        
        if (!$version) {
            $version = config('app.api.version', 'v1');
        }

        $request->attributes->set('api_version', $version);
        
        return $next($request);
    }
} 