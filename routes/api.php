<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ApiStatusController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Resource\CustomerController;
use App\Http\Controllers\Resource\VendorController;
use App\Http\Controllers\Resource\VendorCategoryController;

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


Route::prefix('customers')->group(function () {
    Route::get('/', [CustomerController::class, 'list_customers']);
    Route::get('/{id}', [CustomerController::class, 'show_customer']);
    Route::post('/', [CustomerController::class, 'register_customer']);
    Route::put('/{id}', [CustomerController::class, 'update_customer']);
    Route::delete('/{id}', [CustomerController::class, 'delete_customer']);
});

Route::prefix('vendors')->group(function () {
    Route::get('/', [VendorController::class, 'list_vendors']);
    Route::get('/{id}', [VendorController::class, 'show_vendor']);
    Route::post('/', [VendorController::class, 'register_vendor']);
    Route::put('/{id}', [VendorController::class, 'update_vendor']);
    Route::delete('/{id}', [VendorController::class, 'delete_vendor']);
});

Route::prefix('categories')->group(function () {
    Route::get('/', [VendorCategoryController::class, 'all_categories']); // Get all categories
    Route::get('/{id}', [VendorCategoryController::class, 'show_category']); // Get a specific category by ID
    Route::put('/{id}', [VendorCategoryController::class, 'update_category']); // Update a category by ID
    Route::delete('/{id}', [VendorCategoryController::class, 'delete_category']); // Delete a category by ID
    Route::post('/', [VendorCategoryController::class, 'create_category']); // Create a new category

});
