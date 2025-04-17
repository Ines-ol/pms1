<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if ($request->user()->ROLE !== $role) {
            return response()->json(['message' => 'Unauthorized for this role'], 403);
        }

        return $next($request);
    }
}
