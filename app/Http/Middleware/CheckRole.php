<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user() || !$request->user()->hasRole($role)) {
            return response()->json(
                [
                'message' => 'Unauthorized. Insufficient permissions.'
                ],
                403
            );
        }

        return $next($request);
    }
}
