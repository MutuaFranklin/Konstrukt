<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ApiStatusController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Resource\CustomerController;
use App\Http\Controllers\Resource\VendorController;
use App\Http\Controllers\Resource\VendorCategoryController;
use App\Http\Controllers\Resource\ProductController;
use App\Http\Controllers\Resource\OrderController;
use App\Http\Controllers\Auth\SocialAuthenticationController;


use Illuminate\Support\Facades\Route;

// Status
Route::get('status', [ApiStatusController::class, 'status']);

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

// Google and Microsoft Authentication Routes
Route::group(['prefix' => 'auth'], function () {
    Route::get('/google', [SocialAuthenticationController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/google/callback', [SocialAuthenticationController::class, 'handleGoogleCallback']);

    Route::get('/microsoft', [SocialAuthenticationController::class, 'redirectToMicrosoft'])->name('microsoft.login');
    Route::get('/microsoft/callback', [SocialAuthenticationController::class, 'handleMicrosoftCallback']);
});

Route::group(['middleware' => 'jwt.auth'], function () {
    // Customers
    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'list_customers']);
        Route::get('/{id}', [CustomerController::class, 'show_customer']);
        Route::post('/', [CustomerController::class, 'register_customer']);
        Route::put('/{id}', [CustomerController::class, 'update_customer']);
        Route::delete('/{id}', [CustomerController::class, 'delete_customer']);
    });

    // Vendors
    Route::prefix('vendors')->group(function () {
        Route::get('/', [VendorController::class, 'list_vendors']);
        Route::get('/{id}', [VendorController::class, 'show_vendor']);
        Route::post('/', [VendorController::class, 'register_vendor']);
        Route::put('/{id}', [VendorController::class, 'update_vendor']);
        Route::delete('/{id}', [VendorController::class, 'delete_vendor']);
    });

    // Vendor Categories
    Route::prefix('categories')->group(function () {
        Route::get('/', [VendorCategoryController::class, 'all_categories']);
        Route::get('/{id}', [VendorCategoryController::class, 'show_category']);
        Route::put('/{id}', [VendorCategoryController::class, 'update_category']);
        Route::delete('/{id}', [VendorCategoryController::class, 'delete_category']);
        Route::post('/', [VendorCategoryController::class, 'create_category']);
    });

    // Product Routes
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'list_products']);
        Route::post('/', [ProductController::class, 'create_new_product']);
        Route::get('/{id}', [ProductController::class, 'show_product']);
        Route::put('/{id}', [ProductController::class, 'update_product']);
        Route::delete('/{id}', [ProductController::class, 'delete_product']);
    });
    //Orders
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'all_orders']);
        Route::post('/', [OrderController::class, 'create_new_order']);
        Route::get('/{id}', [OrderController::class, 'show_order']);
        Route::put('/{id}', [OrderController::class, 'update_order']);
        Route::delete('/{id}', [OrderController::class, 'delete_order']);
    });
});

// Swagger documentation route
Route::get('/documentation', function () {
    return redirect('/api/documentation');
});
