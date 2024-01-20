<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Helper\JWTToken;
use Mail;
use App\Mail\OTPMail;

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

    public function userLogin(Request $request) {
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

    public function sendOTP(Request $request) {
        $email = $request->input('email');
        $otp = rand(1000, 9999);

        $user = User::where('email', '=', $email)->first();

        if ($user) {
            $userName = $user->first_name; 

            Mail::to($email)->send(new OTPMail($otp, $userName));

            // Update OTP Code in User Table
            $user->update(['otp' => $otp]);

            return response()->json([
                'status' => 'success',
                'message' => 'A 4-digit OTP has been sent to your email.',
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid email. Please provide a registered email.',
            ], 401);
        }
    }
    
    public function verifyOTP(Request $request) {
        $email = $request->input('email');
        $otp = $request->input('otp');
    
        $user = User::where('email', '=', $email)
            ->where('otp', '=', $otp)
            ->first();
    
        if ($user) {
            // Update Database OTP
            User::where('email', '=', $email)->update(['otp' => '0']);
    
            // Generate Password Reset Token
            $jwtToken = new JWTToken();
            $token = $jwtToken->createPasswordResetToken($request->input('email'));
    
            return response()->json([
                'status' => 'success',
                'message' => 'OTP verification successful.',
                'token' => $token,
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP. Please enter the correct OTP.',
            ], 200);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $email = $request->header('email');
            $password = $request->input('password');

            // Hash the new password
            $hashedPassword = Hash::make($password);

            User::where('email', '=', $email)->update(['password' => $hashedPassword]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Password reset successful.',
            ], 200);

        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reset password. Please try again.',
            ], 200);
        }
    }


}
