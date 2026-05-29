<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - TokoKu API
|--------------------------------------------------------------------------
|
| Base URL: http://localhost:8000/api
| Format Respons: JSON
| Header wajib: Accept: application/json
|
*/

// ============================================================
// ROUTE PUBLIK (Tanpa Autentikasi)
// ============================================================

// Autentikasi
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// Kategori (read-only publik)
Route::get('/categories',      [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// Produk (read-only publik)
Route::get('/products',      [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// ============================================================
// ROUTE TERPROTEKSI (Wajib Login dengan Sanctum)
// ============================================================

Route::middleware('auth:sanctum')->group(function () {

    // Autentikasi - profil & logout
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/profile', [AuthController::class, 'profile']);

    // Kategori - operasi CUD (Create, Update, Delete)
    Route::post('/categories',        [CategoryController::class, 'store']);
    Route::put('/categories/{id}',    [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // Produk - operasi CUD + toggle
    Route::post('/products',                  [ProductController::class, 'store']);
    Route::put('/products/{id}',              [ProductController::class, 'update']);
    Route::patch('/products/{id}/toggle',     [ProductController::class, 'toggle']);
    Route::delete('/products/{id}',           [ProductController::class, 'destroy']);

    // Pesanan (Order) - semua endpoint memerlukan login
    Route::get('/orders',               [OrderController::class, 'index']);
    Route::post('/orders',              [OrderController::class, 'store']);
    Route::get('/orders/{id}',          [OrderController::class, 'show']);
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
});
