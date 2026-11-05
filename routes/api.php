<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StripeController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// Orders
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/{order}', [OrderController::class, 'show']);

// // Stripe
// Route::get('/stripe/publishable-key', [StripeController::class, 'publishableKey']);
// Route::post('/stripe/checkout', [StripeController::class, 'checkout']);
// Route::post('/stripe/intent/create', [StripeController::class, 'intentCreate']);
// Route::post('/stripe/intent/confirm', [StripeController::class, 'intentConfirm']);
// Route::post('/stripe/verify', [StripeController::class, 'verify']);
// Route::post('/stripe/webhook', [StripeController::class, 'webhook']);
