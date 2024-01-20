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
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('token');

        $jwtToken = new JWTToken();
        $tokenResult = $jwtToken->verifyToken($token);

        if ($tokenResult === "unauthorized") {
            return redirect('/user-login');
        } else {
            $request->headers->set('email', $tokenResult);
            return $next($request);
        }
    }
    
}
