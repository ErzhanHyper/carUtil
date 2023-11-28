<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;

//Генерация документов в PDF формате без сохранения в хранилище
Route::middleware(['isModerator'])->group(function () {
    Route::get('/kap/{id}/reference', [DocumentController::class, 'getKapReference']);
});

Route::middleware(['isOperator'])->group(function () {
    Route::get('/order/{id}/statement', [DocumentController::class, 'getStatement']);
    Route::get('/order/{id}/contract', [DocumentController::class, 'getContract']);
    Route::get('/order/{id}/complect', [DocumentController::class, 'getComplect']);
});

Route::get('/sell/{id}/application', [DocumentController::class, 'getSellApplication']);
Route::get('/transfer/{id}/contract', [DocumentController::class, 'getTransferContract']);
Route::get('/exchange/{id}/application', [DocumentController::class, 'getExchangeApplication']);
