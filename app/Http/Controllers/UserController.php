<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Helper\JWTToken;
use Mail;
use Rule;
use App\Mail\OTPMail;
use Validator;

class UserController extends Controller
{
    function showUserRegistrationPage () {
        return view('pages.auth.user-register');
    }

    function showUserLoginPage() {
        return view('pages.auth.user-login');
    }

    function showForgotPasswordPage() {
        return view('pages.auth.send-otp');
    }

    function showVerifyOTPPage() {
        return view('pages.auth.verify-otp-page');
    }

    function showResetPasswordPage() {
        return view('pages.auth.reset-password-page');
    }

    function showUserProfilePage() {
        return view('pages.dashboard.user-profile-page');
    }


    public function registerUser(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email|unique:users,email|max:255',
                'phone' => 'required|string|max:15',
                'password' => 'required|string|min:6',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User registration failed due to validation errors.',
                    'errors' => $validator->errors(),
                ], 422);
            }
            
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
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }
    
            $user = User::where('email', $request->input('email'))->first();
    
            if ($user && Hash::check($request->input('password'), $user->password)) {
                $jwtToken = new JWTToken();
                $token = $jwtToken->createToken($request->input('email'), $user->id);
    
                return response()->json([
                    'status' => 'success',
                    'message' => 'User login successful',
                    'token' => $token,
                ], 200)->cookie('token', $token, time()+60*24*30);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid credentials',
                ], 401);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Login failed',
            ], 500);
        }
    }

    public function sendOTP(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid email. Please provide a registered email.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $email = $request->input('email');
            $otp = rand(1000, 9999);

            $user = User::where('email', $email)->first();
            $userName = $user->first_name; 

            Mail::to($email)->send(new OTPMail($otp, $userName));

            // Update OTP Code in User Table
            $user->update(['otp' => $otp]);

            return response()->json([
                'status' => 'success',
                'message' => 'A 4-digit OTP has been sent to your email.',
            ], 200);
       
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send OTP email. Please try again later.',
            ], 500);
        }
    }

    public static function verifyOTP(Request $request)
    {
        $email = $request->input('email');
        $otp = $request->input('otp');

        $user = User::where('email', $email)
            ->where('otp', $otp)
            ->first();

        if (!empty($user)) {
            // Update Database OTP
            $user->update(['otp' => '0']);

            // Generate Password Reset Token
            $jwtToken = new JWTToken();
            $token = $jwtToken->createPasswordResetToken($email);

            return response()->json([
                'status' => 'success',
                'message' => 'OTP verification successful.',
                'token' => $token,
            ], 200)->cookie('token', $token, 60*24*30);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP. Please enter the correct OTP.',
            ], 401); // 422 for Unprocessable Entity
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required|min:8',
            ]);


            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422); // 422 Unprocessable Entity
            }

            $email = $request->header('email');
            $password = $request->input('password');

            // Hash the new password
            $hashedPassword = Hash::make($password);

            // Update user's password
            $affectedRows = User::where('email', $email)->update(['password' => $hashedPassword]);

            if ($affectedRows > 0) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Password reset successful.',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Password update failed. No rows were affected.',
                ], 500); // 500 Internal Server Error
            }

        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reset password. Please try again.',
            ], 400); // 400 Bad Request
        }
    }

    function logout(){
        return redirect('/user-login')->cookie('token','',-1);
    }

    function getUserProfile(Request $request){
        try {
            $email = $request->header('email');
            $user = User::where('email', '=', $email)->firstOrFail();
            
            return response()->json([
                'status' => 'success',
                'message' => 'User profile fetched successfully',
                'data' => $user
            ], 200);

        } catch (Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch user profile. Please try again.',
            ], 400); // 400 Bad Request
        }
    }

    function updateUserProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'password' => 'required|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422); // 422 Unprocessable Entity
            }

            $email = $request->header('email');
            $firstName = $request->input('first_name');
            $lastName = $request->input('last_name');
            $phone = $request->input('phone');
            $password = $request->input('password');

            // Hash the new password
            $hashedPassword = Hash::make($password);

            // Update user's profile
            User::where('email', '=', $email)->update([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'password' => $hashedPassword
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
            ], 200);

        } catch (Exception $exception) {
            // Log the exception or handle it as needed
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update profile. Please try again.',
            ], 500); // 500 Internal Server Error
        }
    }



}
