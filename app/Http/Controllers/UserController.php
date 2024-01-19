<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Helper\JWTToken;

class UserController extends Controller
{
    public function userRegistration(Request $request) {
        try {
            User::create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'password' => Hash::make($request->input('password')),
            ]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'User registration successful',
            ], 201);

        }catch(Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User registration failed.',
            ], 422);
        }
        
    }

    public function userLogin(Request $request)
    {
        $user = User::where('email', $request->input('email'))->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            $jwtToken = new JWTToken();
            $token = $jwtToken->createToken($request->input('email'));

            return response()->json([
                'status' => 'success',
                'message' => 'User login successful.',
                'token' => $token,
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials.',
            ], 401);
        }
    }
}
