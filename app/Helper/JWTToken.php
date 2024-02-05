<?php

namespace App\Helper;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTToken {

    public function createToken($userEmail) {
        $key =env('JWT_KEY');

        $payload = [
            'iss'=>'laravel-token',
            'iat'=>time(),
            'exp'=>time()+60*60,
            'userEmail'=>$userEmail,
        ];

        return JWT::encode($payload, $key, 'HS256');
    }

    public function createPasswordResetToken ($userEmail) {
        $key =env('JWT_KEY');
        $payload=[
            'iss'=>'laravel-token',
            'iat'=>time(),
            'exp'=>time()+60*20,
            'userEmail'=>$userEmail,
        ];
        return JWT::encode($payload,$key,'HS256');
    }
    // public static function verifyToken($token, $isPasswordResetToken = false)
    // {
    //     try {
    //         if ($token !== null) {
    //             $key =env('JWT_KEY');

    //             $decoded = JWT::decode($token, new Key($key,'HS256'));

    //             $currentTimestamp = time();
    //             if (isset($decoded->exp) && $decoded->exp < $currentTimestamp) {
    //                 return 'unauthorized';
    //             }

    //             return $decoded->userEmail;
    //         } else {
    //             return $token;
              
    //         }
    //     } catch (Exception $e) {
    //         return 'unauthorized'. $e->getMessage();
    //     }
    // }
    public static function verifyToken($token, $isPasswordResetToken = false)
    {
        try {
            if ($token !== null && $token !== '') {
                $key = env('JWT_KEY');
    
                $decoded = JWT::decode($token, new Key($key, 'HS256'));
    
                $currentTimestamp = time();
    
                // Check token expiration
                if (isset($decoded->exp) && $decoded->exp < $currentTimestamp) {
                    return 'unauthorized';
                }
    
                // Additional checks for password reset token
                if ($isPasswordResetToken) {
                    // Check if the token has the expected claim for password reset
                    if (!isset($decoded->userEmail) || !isset($decoded->iss) || $decoded->iss !== 'laravel-token') {
                        return 'unauthorized';
                    }
                }
    
                return $decoded->userEmail;
            } else {
                return 'unauthorized';
            }
        } catch (Exception $e) {
            return 'unauthorized';
        }
    }
    

    
}