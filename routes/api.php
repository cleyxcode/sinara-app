<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QuestionApiController;
use App\Http\Controllers\Api\ArticleApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ResponseApiController;
use App\Http\Controllers\Api\FacilityIvaApiController;
use App\Http\Controllers\Api\IvaTestApiController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Password Reset Routes
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/verify-reset-token', [AuthController::class, 'verifyResetToken']);
});

Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/update-password', [AuthController::class, 'updatePassword']);
});

Route::prefix('questions')->group(function () {
    Route::get('/', [QuestionApiController::class, 'index']);
    Route::get('/categories', [QuestionApiController::class, 'getCategories']);
    Route::get('/grouped', [QuestionApiController::class, 'getGroupedByCategory']);
    Route::get('/stats', [QuestionApiController::class, 'getStats']);
    Route::get('/category/{category}', [QuestionApiController::class, 'getByCategory']);
    Route::get('/{id}', [QuestionApiController::class, 'show']);
});

Route::middleware('auth:sanctum')->prefix('responses')->group(function () {
    Route::post('/submit', [ResponseApiController::class, 'submitResponses']);
    Route::get('/history', [ResponseApiController::class, 'getUserHistory']);
    Route::get('/{id}', [ResponseApiController::class, 'getResponseDetail']);
});

Route::prefix('articles')->group(function () {
    Route::get('/', [ArticleApiController::class, 'index']);
    Route::get('/latest', [ArticleApiController::class, 'getLatest']);
    Route::get('/search', [ArticleApiController::class, 'search']);
    Route::get('/stats', [ArticleApiController::class, 'getStats']);
    Route::get('/{id}', [ArticleApiController::class, 'show']);
});

// Facility IVA Routes
Route::prefix('facilities')->group(function () {
    // Public routes - tidak perlu auth karena untuk informasi umum
    Route::get('/', [FacilityIvaApiController::class, 'index']);
    Route::get('/nearby', [FacilityIvaApiController::class, 'getNearby']);
    Route::get('/locations', [FacilityIvaApiController::class, 'getLocations']);
    Route::get('/search', [FacilityIvaApiController::class, 'search']);
    Route::get('/stats', [FacilityIvaApiController::class, 'getStats']);
    Route::get('/location/{location}', [FacilityIvaApiController::class, 'getByLocation']);
    Route::get('/{id}', [FacilityIvaApiController::class, 'show']);
    
    // Route khusus untuk saran setelah skrining
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/suggestions/after-screening', [FacilityIvaApiController::class, 'getSuggestionAfterScreening']);
    });
});

// IVA Test Results Routes
Route::prefix('iva-tests')->group(function () {
    // Public route untuk mendapatkan jenis pemeriksaan (tidak perlu auth)
    Route::get('/examination-types', [IvaTestApiController::class, 'getExaminationTypes']);
    
    // Protected routes - memerlukan authentication
    Route::middleware('auth:sanctum')->group(function () {
        // Submit hasil test IVA
        Route::post('/submit-result', [IvaTestApiController::class, 'submitResult']);
        
        // Mendapatkan riwayat test user yang sedang login
        Route::get('/my-history', [IvaTestApiController::class, 'getUserHistory']);
        
        // Mendapatkan detail hasil test tertentu
        Route::get('/result/{id}', [IvaTestApiController::class, 'getResultDetail']);
        
        // Update hasil test (hanya dalam 24 jam setelah submit)
        Route::put('/result/{id}', [IvaTestApiController::class, 'updateResult']);
        
        // Delete hasil test (hanya dalam 1 jam setelah submit)
        Route::delete('/result/{id}', [IvaTestApiController::class, 'deleteResult']);
    });
});