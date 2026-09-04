<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\PromptGenerationController;
use App\Http\Controllers\Api\V1\PublicProfileController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\UserController;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user', [UserController::class, 'update']);
    Route::prefix('v1')->group(function () {
        Route::apiResource('posts', PostController::class)->except(['show']);
        Route::apiResource('categories', CategoryController::class)->only(['index']);
        Route::get('/category/{category}', [PostController::class, 'category']);
        Route::post('/follow/{user:username}', [FollowerController::class, 'followUnfollow']);
    });
});

Route::prefix('v1')->group(function () {
    Route::get('/@{user:username}', [PublicProfileController::class, 'show']);
    Route::get('/@{username}/{post:slug}', [PostController::class, 'show']);
});


require __DIR__ . '/auth.php';
