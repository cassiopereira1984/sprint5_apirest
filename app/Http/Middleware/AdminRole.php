<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Traits\HasRoles;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; 
use App\Models\User;

class AdminRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!Auth::user()->hasRole('admin')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return $next($request);
    }
}