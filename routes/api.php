<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ApiStatusController;
use App\Http\Controllers\Auth\VerificationController;

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





Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//Status
Route::get('status', [ApiStatusController::class, 'status']);

Route::get('/auth/google', 'Auth\LoginController@redirectToGoogle');
Route::get('/auth/google/callback', 'Auth\LoginController@handleGoogleCallback');

Route::get('/auth/microsoft', 'Auth\LoginController@redirectToMicrosoft');
Route::get('/auth/microsoft/callback', 'Auth\LoginController@handleMicrosoftCallback');

// User registration route
Route::post('register', [RegisterController::class, 'register']);

// Verify user's email address
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
     ->middleware(['signed'])->name('verification.verify');


// Resend the email verification notification
Route::post('/email/resend', [VerificationController::class, 'resend'])
     ->middleware(['throttle:6,1'])->name('verification.resend');


// Login route
Route::post('/login', [LoginController::class, 'login']);
