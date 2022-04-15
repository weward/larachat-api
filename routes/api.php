<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Admin\ForgotPasswordController;
use App\Http\Controllers\Admin\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Embedded Application
 * This is the point where <iFrames> connect
 */
Route::post('/embed/app-settings', [EmbedController::class, 'embedAppSettings'])->name('api.settings.embed-app');

/**
 * Authenticated Routes
 */
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/logout',[ AuthController::class, 'logout'])->name('api.logout');
});

Route::namespace('Admin')->group(function() {
    Route::prefix('billing')->group(function() {
        Route::get('/get-stripe', [BillingController::class, 'index'])->name('api.stripe.index');
        Route::post('/setup-payment-method', [BillingController::class, 'setupPaymentMethod'])->name('api.stripe.setup-payment-method');
    });

    Route::post('/register', [RegisterController::class, 'register'])->name('api.register');
    Route::get('/verify/{id}/{hash}', [RegisterController::class, 'verify'])->name('api.verify');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'handle'])->name('api.forgot-password');
    Route::get('/reset-password/{id}/{hash}', [ForgotPasswordController::class, 'resetLink'])->name('api.reset-password');
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');
    Route::get('/resend-verification-email/{id}', [RegisterController::class, 'resendVerificationEmail'])->name('api.resend-verification-email');
});

// Broadcast::routes(['middleware' => 'auth:api']); 
Route::get('test', 'Admin\ChatController@test');