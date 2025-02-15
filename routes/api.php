<?php

use App\Http\Controllers\JWTAuthController;
use App\Http\Controllers\TranslationController;

Route::post('register-user', [JWTAuthController::class, 'register']);
Route::post('login', [JWTAuthController::class, 'login']);

Route::middleware(['jwt.auth'])->group(function () {
    Route::get('get-current-user', [JWTAuthController::class, 'getUser']);
    Route::post('logout', [JWTAuthController::class, 'logout']);



    // blogs routes

    Route::prefix('translations')->group(function () {
        Route::post('/', [TranslationController::class, 'store']);
        Route::post('/{translation}', [TranslationController::class, 'update']);
        Route::get('/', [TranslationController::class, 'show']);
        Route::get('/search', [TranslationController::class, 'search']);
    });

});
