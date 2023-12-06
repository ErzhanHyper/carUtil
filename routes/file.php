<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;

Route::prefix('file')->group(function () {
    Route::post('/order/store', [FileController::class, 'storeOrderFile']);
    Route::post('/order/get', [FileController::class, 'getOrderFile']);
    Route::delete('{id}/order', [FileController::class, 'deleteOrderFile']);
    Route::get('/order/{id}/generatePFS', [FileController::class, 'generateOrderPFS']);

    Route::post('/preorder/store', [FileController::class, 'storePreOrderFile']);
    Route::post('/preorder/get', [FileController::class, 'getPreOrderFile']);
    Route::post('/preorder/delete', [FileController::class, 'deletePreOrderFile']);

    Route::get('/{id}/order/doc', [FileController::class, 'getFile']);
    Route::get('/{id}/order/image', [FileController::class, 'getImage']);
    Route::get('/{id}/order/video', [FileController::class, 'getVideo']);

    Route::get('/{id}/preorder/carFile', [FileController::class, 'getCarFile']);
    Route::get('/{id}/preorder/carImage', [FileController::class, 'getCarFileImage']);
    Route::get('/{id}/preorder/agroFile', [FileController::class, 'getAgroFile']);
    Route::get('/{id}/preorder/agroImage', [FileController::class, 'getAgroFileImage']);
    Route::get('/{id}/exchange/download', [FileController::class, 'downloadExchangeFile']);
    Route::get('/{id}/sell/download', [FileController::class, 'downloadSellFile']);
});
