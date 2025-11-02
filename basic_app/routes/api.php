<?php
// ...existing code...

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CompanyInfoController;
use App\Http\Controllers\Api\CompanyBranchController;
use App\Http\Middleware\JWTAuthMiddleware;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FilterApiController;


Route::prefix('auth')->group(function () {
    Route::post('register', [AuthApiController::class, 'register']);
    Route::post('login', [AuthApiController::class, 'login']);
    Route::post('forgot-password', [AuthApiController::class, 'forgotPassword']);
    Route::post('resend-forgot-password', [AuthApiController::class, 'resendForgotPassword']);
    Route::post('resend-verify_email', [AuthApiController::class, 'resendVerificationEmail']);
});

Route::middleware([JWTAuthMiddleware::class])->group(function () {
    // Authenticated routes
    Route::get('me', [AuthApiController::class, 'me']);
    Route::post('logout',
    [AuthApiController::class, 'logout']);
    Route::post('user/profile', [AuthApiController::class, 'updateProfile']);
    Route::post('update-settings', [AuthApiController::class, 'updateSettings']);
    Route::post('change-password', [AuthApiController::class, 'changePassword']);
    Route::get('company-info', [CompanyInfoController::class, 'index']);
    Route::get('company-info/first/stream', [CompanyInfoController::class, 'streamFirst']);
    Route::get('company-info/first/long-poll', [CompanyInfoController::class, 'longPollFirst']);
    Route::get('company-branch', [CompanyBranchController::class, 'index']);
    Route::get('user', function (Request $request) {
        return response()->json($request->user());
    });
    Route::get('categories',[CategoryController::class,'index']);
    Route::post('category',[CategoryController::class,'show']);
    Route::get('filter',[FilterApiController::class,'index']);
        Route::get('filter',[FilterApiController::class,'filter']);


});
 Broadcast::routes();

// ...existing code...
