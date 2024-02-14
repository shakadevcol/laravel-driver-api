<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RefreshTokenController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Frontend\Profile\UserProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [RegisterController::class, 'index']);
Route::post('login', [LoginController::class, 'index']);
Route::post('refresh-token', [RefreshTokenController::class, 'index'])
                ->middleware('checkRefreshToken:sanctum');
Route::post('logout', [LogoutController::class, 'index']);

Route::middleware('auth:sanctum')->group(function() {
    Route::get('profile', [UserProfileController::class, 'index']);
});
