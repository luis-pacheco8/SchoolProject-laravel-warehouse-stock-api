<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LocationController;


Route::post('/login', [AuthController::class, 'login']);

// Rotas privadas
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {

   
    Route::post('/logout', [AuthController::class, 'logout']);

   
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/alerts/low-stock', [ProductController::class, 'lowStock']);

   
    Route::post('/movements', [MovementController::class, 'store']);
    Route::get('/movements', [MovementController::class, 'index']);

    
    Route::get('/locations', [LocationController::class, 'index']);
    Route::get('/locations/{id}/products', [LocationController::class, 'products']);

    
    Route::get('/reports/rotation', [MovementController::class, 'rotation']);

   
    Route::middleware('role:admin')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::post('/products/{id}/adjust', [ProductController::class, 'adjust']);
    });

});