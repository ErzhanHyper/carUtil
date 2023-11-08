<?php

use App\Services\AuthService;
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
        Route::post('profile/update', [\App\Http\Controllers\AuthController::class, 'update']);

        Route::post('role', [\App\Http\Controllers\UserController::class, 'role']);

        Route::middleware(['isModerator'])->group(function () {
            Route::prefix('user')->group(function () {
                Route::post('/all', [\App\Http\Controllers\UserController::class, 'get']);
                Route::post('/{id}/get', [\App\Http\Controllers\UserController::class, 'getById']);
                Route::post('/store', [\App\Http\Controllers\UserController::class, 'store']);
                Route::post('/{id}/update', [\App\Http\Controllers\UserController::class, 'update']);
            });
        });

        Route::post('validUser', [\App\Http\Controllers\AuthController::class, 'validUser']);

        Route::prefix('preorder')->group(function () {
            Route::get('/', [\App\Http\Controllers\PreOrderController::class, 'get'])->name('preorder');
            Route::post('store', [\App\Http\Controllers\PreOrderController::class, 'store']);
            Route::post('delete', [\App\Http\Controllers\PreOrderController::class, 'delete']);

            Route::get('{id}/get', [\App\Http\Controllers\PreOrderController::class, 'getById']);
            Route::post('{id}/send', [\App\Http\Controllers\PreOrderController::class, 'send']);
            Route::post('{id}/booking', [\App\Http\Controllers\PreOrderController::class, 'booking']);

            Route::post('checkVehicle', [\App\Http\Controllers\PreOrderController::class, 'searchFromKap']);

            Route::middleware(['isModerator'])->group(function () {
                Route::post('{id}/approve', [\App\Http\Controllers\PreOrderController::class, 'approve']);
                Route::post('{id}/decline', [\App\Http\Controllers\PreOrderController::class, 'decline']);
                Route::post('{id}/revision', [\App\Http\Controllers\PreOrderController::class, 'revision']);
            });
        });

        Route::prefix('order')->group(function () {
            Route::get('/', [\App\Http\Controllers\OrderController::class, 'get'])->name('order');
            Route::get('{id}/get', [\App\Http\Controllers\OrderController::class, 'getById']);
            Route::post('/sign', [\App\Http\Controllers\OrderController::class, 'sign']);

            Route::middleware(['isModerator'])->group(function () {
                Route::post('/cert', [\App\Http\Controllers\OrderController::class, 'cert']);
                Route::post('/approve', [\App\Http\Controllers\OrderController::class, 'approve']);
                Route::post('/decline', [\App\Http\Controllers\OrderController::class, 'decline']);
                Route::post('/revision', [\App\Http\Controllers\OrderController::class, 'revision']);
            });
        });

        Route::prefix('certificate')->group(function () {
            Route::get('/', [\App\Http\Controllers\CertificateController::class, 'get'])->name('certificate');
            Route::get('{id}/get', [\App\Http\Controllers\CertificateController::class, 'generate']);
        });

        Route::prefix('exchange')->group(function () {
            Route::get('/', [\App\Http\Controllers\ExchangeController::class, 'get']);
            Route::post('/', [\App\Http\Controllers\ExchangeController::class, 'store']);
            Route::get('/{id}', [\App\Http\Controllers\ExchangeController::class, 'getById']);
            Route::match(['put', 'patch'], '/{id}', [\App\Http\Controllers\ExchangeController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\ExchangeController::class, 'delete']);

            Route::middleware(['isModerator'])->group(function () {
                Route::match(['put', 'patch'], '/{id}/approve', [\App\Http\Controllers\ExchangeController::class, 'approve']);
                Route::match(['put', 'patch'], '/{id}/decline', [\App\Http\Controllers\ExchangeController::class, 'decline']);
            });

            Route::post('/storeFile', [\App\Http\Controllers\ExchangeController::class, 'storeFile']);
            Route::post('/getFile', [\App\Http\Controllers\ExchangeController::class, 'getFile']);
            Route::post('/deleteFile', [\App\Http\Controllers\ExchangeController::class, 'deleteFile']);
        });

        Route::prefix('transfer')->group(function () {
            Route::get('/orderCurrent', [\App\Http\Controllers\TransferOrderController::class, 'getCurrent']);
            Route::prefix('order')->group(function () {
                Route::get('/', [\App\Http\Controllers\TransferOrderController::class, 'get']);
                Route::get('/{id}', [\App\Http\Controllers\TransferOrderController::class, 'getById']);
                Route::post('/', [\App\Http\Controllers\TransferOrderController::class, 'store']);
                Route::delete('/{id}', [\App\Http\Controllers\TransferOrderController::class, 'delete']);
                Route::put('/{id}/sign', [\App\Http\Controllers\TransferOrderController::class, 'sign']);
            });

            Route::prefix('deal')->group(function () {
                Route::get('/', [\App\Http\Controllers\TransferDealController::class, 'get']);
                Route::post('/', [\App\Http\Controllers\TransferDealController::class, 'store']);
                Route::put('/{id}/accept', [\App\Http\Controllers\TransferDealController::class, 'accept']);
                Route::put('/{id}/close', [\App\Http\Controllers\TransferDealController::class, 'close']);
            });
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
            Route::get('/order/{id}/statement', [\App\Http\Controllers\DocumentController::class, 'getStatement']);
            Route::get('/order/{id}/contract', [\App\Http\Controllers\DocumentController::class, 'getContract']);
            Route::get('/exchange/{id}/application', [\App\Http\Controllers\DocumentController::class, 'getExchangeApplication']);
            Route::get('/transfer/{id}/contract', [\App\Http\Controllers\DocumentController::class, 'getTransferContract']);
            Route::get('/sell/{id}/application', [\App\Http\Controllers\DocumentController::class, 'getSellApplication']);
        });

        Route::get('region', [\App\Http\Controllers\RegionController::class, 'get'])->name('region');

        Route::get('fileType', [\App\Http\Controllers\FileTypeController::class, 'get']);
        Route::get('fileTypeAgro', [\App\Http\Controllers\FileTypeController::class, 'getAgro']);

        Route::prefix('factory')->group(function () {
            Route::get('/', [\App\Http\Controllers\FactoryController::class, 'get']);
            Route::middleware(['isModerator'])->group(function () {
                Route::post('/', [\App\Http\Controllers\FactoryController::class, 'store']);
                Route::get('/{id}', [\App\Http\Controllers\FactoryController::class, 'getById']);
                Route::match(['put', 'patch'], '/{id}', [\App\Http\Controllers\FactoryController::class, 'update']);
//                Route::delete('/{id}', [\App\Http\Controllers\FactoryController::class, 'delete']);
            });
        });

        Route::prefix('manufacture')->group(function () {
            Route::get('/', [\App\Http\Controllers\ManufactureController::class, 'get']);
            Route::middleware(['isModerator'])->group(function () {
                Route::post('/', [\App\Http\Controllers\ManufactureController::class, 'store']);
                Route::get('/{id}', [\App\Http\Controllers\ManufactureController::class, 'getById']);
                Route::match(['put', 'patch'], '/{id}', [\App\Http\Controllers\ManufactureController::class, 'update']);
                Route::delete('/{id}', [\App\Http\Controllers\ManufactureController::class, 'delete']);
            });
        });

        Route::prefix('vehicle')->group(function () {
            Route::get('/', [\App\Http\Controllers\RefFactoryController::class, 'get']);
            Route::middleware(['isModerator'])->group(function () {
                Route::post('/', [\App\Http\Controllers\RefFactoryController::class, 'store']);
                Route::get('/{id}', [\App\Http\Controllers\RefFactoryController::class, 'getById']);
                Route::match(['put', 'patch'], '/{id}', [\App\Http\Controllers\RefFactoryController::class, 'update']);
                Route::delete('/{id}', [\App\Http\Controllers\RefFactoryController::class, 'delete']);
            });
        });

        Route::prefix('sell')->group(function () {
            Route::get('/', [\App\Http\Controllers\SellController::class, 'get']);
            Route::get('/{id}', [\App\Http\Controllers\SellController::class, 'getById']);
            Route::get('/{id}/files', [\App\Http\Controllers\SellController::class, 'getFilesById']);
            Route::post('/', [\App\Http\Controllers\SellController::class, 'store']);
            Route::put('/{id}', [\App\Http\Controllers\SellController::class, 'update']);
        });

        Route::prefix('sellFile')->group(function () {
            Route::post('/', [\App\Http\Controllers\SellController::class, 'storeFile']);
            Route::delete('/{id}', [\App\Http\Controllers\SellController::class, 'deleteFile']);
        });

        Route::prefix('report')->group(function () {
            Route::get('/cert', [\App\Http\Controllers\ReportController::class, 'getCert']);
        });

        Route::post('booking/order', [\App\Http\Controllers\BookingOrderController::class, 'get']);

        Route::get('client', [\App\Http\Controllers\ClientController::class, 'get'])->name('client');
        Route::get('client/{id}', [\App\Http\Controllers\ClientController::class, 'showById']);

        Route::get('report', [\App\Http\Controllers\ReportController::class, 'index'])->name('report');
        Route::get('category', [\App\Http\Controllers\CategoryController::class, 'get']);


    });
});

Route::get('/{any}', function () {
    return view('app');
})->where("any", ".*");
