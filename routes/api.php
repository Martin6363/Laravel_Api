<?php

use App\Http\Controllers\Api\v1\PostController;
use App\Http\Controllers\Api\v1\AuthUserController;
use App\Http\Controllers\Api\v1\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::controller(AuthUserController::class)->group(function() {
    Route::post('v1/register', 'register');
    Route::post('v1/login', 'login')->name('login');
});

Route::prefix('v1')->as('v1')->group(function () {
    ##User Routes
    Route::controller(UserController::class)->group(function() {
        Route::get('users', 'index');
        Route::get('users/{id}', 'show');
        Route::put('users/{id}', 'update');
    });
    Route::middleware('auth:sanctum')->group(function () {
        ##Post Routes
        Route::apiResource('posts', PostController::class)->except(['index']);
        ##Auth Routes
        Route::post('logout', [AuthUserController::class, 'logout']);
    });
    ##Post Routes
    Route::get('posts', [PostController::class, 'index']);
});


Route::middleware('auth:sanctum')->get('v1/user', function () {
    return Auth::user();
});