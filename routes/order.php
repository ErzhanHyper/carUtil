<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('order')->group(function () {
    Route::get('/', [OrderController::class, 'get'])->name('order');
    Route::get('{id}/get', [OrderController::class, 'getById']);
    Route::put('{id}/sign', [OrderController::class, 'sign']);
    Route::put('{id}/send', [OrderController::class, 'send']);

    Route::middleware(['isOperator'])->group(function () {
        Route::post('{id}/video', [OrderController::class, 'video']);
    });

    Route::middleware(['isModerator'])->group(function () {
        Route::get('{id}/duplicates', [OrderController::class, 'getDuplicatesById']);

        Route::put('{id}/executeRun', [OrderController::class, 'executeRun']);
        Route::put('{id}/executeClose', [OrderController::class, 'executeClose']);

        Route::put('{id}/approve', [OrderController::class, 'approve']);
        Route::put('{id}/decline', [OrderController::class, 'decline']);
        Route::put('{id}/revision', [OrderController::class, 'revision']);
        Route::put('{id}/revisionVideo', [OrderController::class, 'revisionVideo']);

        Route::post('/cert', [OrderController::class, 'cert']);
    });
});
