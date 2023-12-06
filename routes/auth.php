<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('loginMobile', [AuthController::class, 'loginMobile']);

Route::middleware(['checkToken'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('user', [AuthController::class, 'get']);
    Route::post('profile/update', [AuthController::class, 'update']);
});
