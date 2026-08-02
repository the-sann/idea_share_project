<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\PromptGenerationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user', [UserController::class, 'update']);

    Route::prefix('v1')->group(function () {
        Route::apiResource('posts', PostController::class);
        Route::apiResource('prompt-generation', PromptGenerationController::class)->only(['index', 'store']);
        Route::apiResource('categories', CategoryController::class)->only(['index']);
    });
});


require __DIR__ . '/auth.php';
