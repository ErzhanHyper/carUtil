<?php

use App\Services\AuthenticationService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::prefix('app')->group(function () {
    Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);
    Route::post('loginMobile', [\App\Http\Controllers\AuthController::class, 'loginMobile']);

    Route::middleware(['checkToken'])->group(function () {
        Route::post('logout', [\App\Http\Controllers\AuthController::class, 'logout']);

        Route::post('user', [\App\Http\Controllers\AuthController::class, 'get']);

        Route::prefix('preorder')->group(function () {
            Route::get('/', [\App\Http\Controllers\PreOrderController::class, 'get'])->name('preorder');
            Route::post('store', [\App\Http\Controllers\PreOrderController::class, 'store']);
            Route::post('delete', [\App\Http\Controllers\PreOrderController::class, 'delete']);

            Route::get('{id}/get', [\App\Http\Controllers\PreOrderController::class, 'getById']);
            Route::post('{id}/send', [\App\Http\Controllers\PreOrderController::class, 'send']);
            Route::post('{id}/moderator/approve', [\App\Http\Controllers\PreOrderController::class, 'moderatorApprove']);
            Route::post('{id}/booking', [\App\Http\Controllers\PreOrderController::class, 'booking']);
            Route::post('checkVehicle', [\App\Http\Controllers\PreOrderController::class, 'searchFromKap']);
        });

        Route::prefix('order')->group(function () {
            Route::get('/', [\App\Http\Controllers\OrderController::class, 'get'])->name('order');
            Route::get('{id}/get', [\App\Http\Controllers\OrderController::class, 'getById']);
            Route::post('/sign', [\App\Http\Controllers\OrderController::class, 'sign']);
            Route::post('/approve', [\App\Http\Controllers\OrderController::class, 'approve']);
            Route::post('/cert', [\App\Http\Controllers\OrderController::class, 'cert']);
            Route::post('/generateCert', [\App\Http\Controllers\OrderController::class, 'generateCert']);
        });

        Route::prefix('certificate')->group(function () {
            Route::get('/', [\App\Http\Controllers\CertificateController::class, 'get'])->name('certificate');
        });

        Route::prefix('transfer')->group(function () {
            Route::get('/order', [\App\Http\Controllers\TransferOrderController::class, 'get']);
            Route::get('/order/{id}', [\App\Http\Controllers\TransferOrderController::class, 'getById']);
            Route::get('/orderCurrent', [\App\Http\Controllers\TransferOrderController::class, 'getCurrent']);

            Route::post('/order/store', [\App\Http\Controllers\TransferOrderController::class, 'store']);
            Route::post('/order/close', [\App\Http\Controllers\TransferOrderController::class, 'close']);
            Route::post('/order/sign', [\App\Http\Controllers\TransferOrderController::class, 'sign']);
            Route::post('/order/pfs', [\App\Http\Controllers\TransferOrderController::class, 'pfs']);

            Route::post('/deal', [\App\Http\Controllers\TransferDealController::class, 'get']);
            Route::post('/deal/store', [\App\Http\Controllers\TransferDealController::class, 'store']);
            Route::post('/deal/accept/{id}', [\App\Http\Controllers\TransferDealController::class, 'accept']);
            Route::post('/deal/close/{id}', [\App\Http\Controllers\TransferDealController::class, 'close']);
        });

        Route::prefix('file')->group(function () {
            Route::post('/order/store', [\App\Http\Controllers\FileController::class, 'storeOrderFile']);
            Route::post('/order/get', [\App\Http\Controllers\FileController::class, 'getOrderFile']);
            Route::post('/order/delete', [\App\Http\Controllers\FileController::class, 'deleteOrderFile']);
            Route::get('/order/{id}/generatePFS', [\App\Http\Controllers\FileController::class, 'generateOrderPFS']);

            Route::post('/preorder/store', [\App\Http\Controllers\FileController::class, 'storePreOrderFile']);
            Route::post('/preorder/get', [\App\Http\Controllers\FileController::class, 'getPreOrderFile']);
            Route::post('/preorder/delete', [\App\Http\Controllers\FileController::class, 'deletePreOrderFile']);
        });

        Route::prefix('document')->group(function () {
            Route::post('/order/statement', [\App\Http\Controllers\DocumentController::class, 'getStatement']);
        });

        Route::get('region', [\App\Http\Controllers\RegionController::class, 'get'])->name('region');

        Route::get('fileType', [\App\Http\Controllers\FileTypeController::class, 'get']);

        Route::get('factory', [\App\Http\Controllers\FactoryController::class, 'get']);

        Route::post('booking/order', [\App\Http\Controllers\BookingOrderController::class, 'get']);

        Route::get('client', [\App\Http\Controllers\ClientController::class, 'get'])->name('client');
        Route::get('client/{id}', [\App\Http\Controllers\ClientController::class, 'showById']);

        Route::get('report', [\App\Http\Controllers\ReportController::class, 'index'])->name('report');
    });
});


Route::get('/{any}', function () {
    return view('app');
})->where("any", ".*");
