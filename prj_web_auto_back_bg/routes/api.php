<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AdController;
use App\Http\Controllers\API\CartItemController;
use App\Http\Controllers\API\ContactController;
use App\Http\Controllers\API\FavoriteController;
use App\Http\Controllers\API\PhotoController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Routes publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/ads', [AdController::class, 'index']);
Route::get('/ads/featured', [AdController::class, 'featured']);
Route::get('/ads/{ad}', [AdController::class, 'show']);
Route::post('/contact', [ContactController::class, 'store']);

// Routes protégées par authentification
Route::middleware('auth:sanctum')->group(function () {
    // Routes utilisateur
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    
    // Routes pour les annonces
    Route::post('/ads', [AdController::class, 'store']);
    Route::put('/ads/{ad}', [AdController::class, 'update']);
    Route::delete('/ads/{ad}', [AdController::class, 'destroy']);
    
    // Routes pour les photos
    Route::post('/ads/{ad}/photos', [PhotoController::class, 'store']);
    Route::delete('/photos/{photo}', [PhotoController::class, 'destroy']);
    
    // Routes pour les favoris
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{favorite}', [FavoriteController::class, 'destroy']);
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggle']);
    
    // Routes pour le panier
    Route::get('/cart-items', [CartItemController::class, 'index']);
    Route::post('/cart-items', [CartItemController::class, 'store']);
    Route::delete('/cart-items/{cartItem}', [CartItemController::class, 'destroy']);
    Route::delete('/cart-items', [CartItemController::class, 'clear']);
    
    // Routes pour les transactions
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show']);
    Route::post('/checkout', [TransactionController::class, 'checkout']);
    
    // Routes administrateur
    Route::middleware('admin')->group(function () {
        // Gestion des utilisateurs
        Route::apiResource('/users', UserController::class);
        
        // Gestion des contacts
        Route::get('/contacts', [ContactController::class, 'index']);
        Route::get('/contacts/{contact}', [ContactController::class, 'show']);
        Route::put('/contacts/{contact}', [ContactController::class, 'update']);
        Route::delete('/contacts/{contact}', [ContactController::class, 'destroy']);
        
        // Dashboard admin
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
        Route::get('/dashboard/export', [DashboardController::class, 'export']);
    });
});