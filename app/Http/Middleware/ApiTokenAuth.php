<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenAuth
{
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = substr($header, 7);
        $hashedToken = hash('sha256', $token);

        $user = User::where('api_token', $hashedToken)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        auth()->setUser($user);

        return $next($request);
    }
}
