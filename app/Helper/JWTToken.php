<?php

namespace App\Helper;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTToken {

    public function createToken($userEmail) {
        $key = env('JWT_KEY');
        $payload = [
            'iss'=>'laravel-token',
            'iat'=>time(),
            'exp'=>time()+60*60,
            'user_email'=>$userEmail,
        ];

        return JWT::encode($payload, $key, 'HS256');
    }

    public function verifyToken($token) {
        try {
            if($token == null) {
                return 'unauthorized';
            }else {
                $key = env('JWT_KEY');
                $decoded = JWT::decode($token, new Key($key, 'RS256'));
            }

        }catch (Exception $e) {
            return 'unauthorized';
        }
    }
}