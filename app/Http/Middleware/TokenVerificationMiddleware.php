<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helper\JWTToken;

class TokenVerificationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next)
    {
        $token = $request->cookie('token');
        $tokenResult = JWTToken::verifyToken($token);

        if ($tokenResult === "unauthorized") {
            return response()->json([
                'status' => 'error',
                'message' => "unauthorized",
            ], 401);
        } else {
            $request->headers->set('email', $tokenResult);
            return $next($request);
        }
    }

    
}
