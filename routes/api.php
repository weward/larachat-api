<?php

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

Route::namespace('Admin')->group(function() {
    Route::post('/register', [RegisterController::class, 'register'])->name('api.register');
    Route::get('/verify/{id}/{hash}', [RegisterController::class, 'verify'])->name('api.verify');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'handle'])->name('api.forgot-password');
    Route::get('/reset-password/{id}/{hash}', [ForgotPasswordController::class, 'resetLink'])->name('api.reset-password');
});