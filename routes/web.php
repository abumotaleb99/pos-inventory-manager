<?php

use App\Http\Middleware\TokenVerificationMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// User Authentication Routes
Route::get('/user-register', [UserController::class, 'showUserRegistrationPage']);
Route::post('/user-register', [UserController::class, 'registerUser']);
Route::get('/user-login', [UserController::class, 'showUserLoginPage']);
Route::post('user-login', [UserController::class, 'userLogin']);
Route::get('/forget-password', [UserController::class, 'showForgetPasswordPage']);
Route::post('/send-otp', [UserController::class, 'sendOTP']);
Route::get('/verify-otp', [UserController::class, 'showVerifyOTPPage']);
Route::post('/verify-otp', [UserController::class, 'verifyOTP']);
Route::get('/reset-password', [UserController::class, 'showResetPasswordPage'])->middleware([TokenVerificationMiddleware::class]);
Route::post('/reset-password', [UserController::class, 'resetPassword'])->middleware([TokenVerificationMiddleware::class]);

Route::get('/dashboard',[DashboardController::class,'DashboardPage']);
