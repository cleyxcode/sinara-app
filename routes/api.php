<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QuestionApiController;
use App\Http\Controllers\Api\ArticleApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ResponseApiController;

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