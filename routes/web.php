<?php

use App\Http\Middleware\TokenVerificationMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;

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
Route::get('/forgot-password', [UserController::class, 'showForgotPasswordPage']);
Route::post('/send-otp', [UserController::class, 'sendOTP']);
Route::get('/verify-otp', [UserController::class, 'showVerifyOTPPage']);
Route::post('/verify-otp', [UserController::class, 'verifyOTP']);
Route::get('/reset-password', [UserController::class, 'showResetPasswordPage'])->middleware([TokenVerificationMiddleware::class]);
Route::post('/reset-password', [UserController::class, 'resetPassword'])->middleware([TokenVerificationMiddleware::class]);
Route::get('/logout',[UserController::class,'logout']);

Route::get('/user-profile', [UserController::class, 'showUserProfilePage'])->middleware([TokenVerificationMiddleware::class]);
Route::get('/profile', [UserController::class, 'getUserProfile'])->middleware([TokenVerificationMiddleware::class]);
Route::post('/user-update', [UserController::class, 'updateUserProfile'])->middleware([TokenVerificationMiddleware::class]);

Route::get('/dashboard',[DashboardController::class,'DashboardPage'])->middleware([TokenVerificationMiddleware::class]);

// Category API
Route::get("/category-page",[CategoryController::class,'categoryPage'])->middleware([TokenVerificationMiddleware::class]);
Route::post("/create-category",[CategoryController::class,'createCategory'])->middleware([TokenVerificationMiddleware::class]);
Route::get("/category-list",[CategoryController::class,'categoryList'])->middleware([TokenVerificationMiddleware::class]);
Route::post("/category-by-id",[CategoryController::class,'getCategoryById'])->middleware([TokenVerificationMiddleware::class]);
Route::post("/update-category",[CategoryController::class,'updateCategory'])->middleware([TokenVerificationMiddleware::class]);
Route::post("/delete-category",[CategoryController::class,'deleteCategory'])->middleware([TokenVerificationMiddleware::class]);

// Customer API
Route::get('/customer-page',[CustomerController::class,'customerPage'])->middleware([TokenVerificationMiddleware::class]);
Route::post("/create-customer",[CustomerController::class,'CustomerCreate'])->middleware([TokenVerificationMiddleware::class]);
Route::get("/list-customer",[CustomerController::class,'CustomerList'])->middleware([TokenVerificationMiddleware::class]);
Route::post("/delete-customer",[CustomerController::class,'CustomerDelete'])->middleware([TokenVerificationMiddleware::class]);
Route::post("/update-customer",[CustomerController::class,'CustomerUpdate'])->middleware([TokenVerificationMiddleware::class]);
Route::post("/customer-by-id",[CustomerController::class,'CustomerByID'])->middleware([TokenVerificationMiddleware::class]);

// Product API
Route::get('/product-page',[ProductController::class,'productPage'])->middleware([TokenVerificationMiddleware::class]);
Route::post("/create-product",[ProductController::class,'createProduct'])->middleware([TokenVerificationMiddleware::class]);
Route::post("/delete-product",[ProductController::class,'deleteProduct'])->middleware([TokenVerificationMiddleware::class]);
Route::post("/update-product",[ProductController::class,'updateProduct'])->middleware([TokenVerificationMiddleware::class]);
Route::get("/list-product",[ProductController::class,'ProductList'])->middleware([TokenVerificationMiddleware::class]);
Route::post("/product-by-id",[ProductController::class,'ProductByID'])->middleware([TokenVerificationMiddleware::class]);