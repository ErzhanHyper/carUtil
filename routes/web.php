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

        Route::get('category', [\App\Http\Controllers\CategoryController::class, 'get']);
        Route::get('category/complect', [\App\Http\Controllers\CategoryController::class, 'getComplect']);

        Route::get('region', [\App\Http\Controllers\RegionController::class, 'get']);
        Route::get('fileType', [\App\Http\Controllers\FileTypeController::class, 'get']);
        Route::get('fileTypeAgro', [\App\Http\Controllers\FileTypeController::class, 'getAgro']);
        Route::get('log', [\App\Http\Controllers\LogController::class, 'get']);

        Route::post('booking/order', [\App\Http\Controllers\BookingOrderController::class, 'get']);
        Route::post('booking/order/delete', [\App\Http\Controllers\BookingOrderController::class, 'delete']);

        Route::prefix('document')->group(function () {
            Route::middleware(['isOperator'])->group(function () {
                Route::get('/order/{id}/statement', [\App\Http\Controllers\DocumentController::class, 'getStatement']);
                Route::get('/order/{id}/contract', [\App\Http\Controllers\DocumentController::class, 'getContract']);
                Route::get('/order/{id}/complect', [\App\Http\Controllers\DocumentController::class, 'getComplect']);
            });
            Route::middleware(['isModerator'])->group(function () {
                Route::get('/kap/{id}/reference', [\App\Http\Controllers\DocumentController::class, 'getKapReference']);
            });
            Route::get('/sell/{id}/application', [\App\Http\Controllers\DocumentController::class, 'getSellApplication']);
            Route::get('/transfer/{id}/contract', [\App\Http\Controllers\DocumentController::class, 'getTransferContract']);
            Route::get('/exchange/{id}/application', [\App\Http\Controllers\DocumentController::class, 'getExchangeApplication']);
        });

        Route::middleware(['isModerator'])->group(function () {
            Route::get('role', [\App\Http\Controllers\UserController::class, 'role']);

            Route::prefix('user')->group(function () {
                Route::get('/', [\App\Http\Controllers\UserController::class, 'get']);
                Route::get('/{id}', [\App\Http\Controllers\UserController::class, 'getById']);
                Route::post('/store', [\App\Http\Controllers\UserController::class, 'store']);
                Route::put('/{id}/update', [\App\Http\Controllers\UserController::class, 'update']);
            });
        });

        Route::prefix('preorder')->group(function () {
            Route::get('/', [\App\Http\Controllers\PreOrderController::class, 'get'])->name('preorder');
            Route::post('/', [\App\Http\Controllers\PreOrderController::class, 'store']);
            Route::delete('/{id}', [\App\Http\Controllers\PreOrderController::class, 'delete']);

            Route::get('/{id}', [\App\Http\Controllers\PreOrderController::class, 'getById']);
            Route::put('/{id}/send', [\App\Http\Controllers\PreOrderController::class, 'send']);
            Route::post('/{id}/booking', [\App\Http\Controllers\PreOrderController::class, 'booking']);

            Route::post('checkVehicle', [\App\Http\Controllers\PreOrderController::class, 'searchFromKap']);
            Route::post('checkVehicleHistory', [\App\Http\Controllers\PreOrderController::class, 'kapHistory']);

            Route::middleware(['isModerator'])->group(function () {
                Route::put('{id}/approve', [\App\Http\Controllers\PreOrderController::class, 'approve']);
                Route::put('{id}/decline', [\App\Http\Controllers\PreOrderController::class, 'decline']);
                Route::put('{id}/revision', [\App\Http\Controllers\PreOrderController::class, 'revision']);
            });
        });

        Route::prefix('order')->group(function () {
            Route::get('/', [\App\Http\Controllers\OrderController::class, 'get'])->name('order');
            Route::get('{id}/get', [\App\Http\Controllers\OrderController::class, 'getById']);
            Route::put('{id}/sign', [\App\Http\Controllers\OrderController::class, 'sign']);
            Route::put('{id}/send', [\App\Http\Controllers\OrderController::class, 'send']);

            Route::middleware(['isOperator'])->group(function () {
                Route::post('{id}/video', [\App\Http\Controllers\OrderController::class, 'video']);
            });

            Route::middleware(['isModerator'])->group(function () {
                Route::get('{id}/duplicates', [\App\Http\Controllers\OrderController::class, 'getDuplicatesById']);

                Route::put('{id}/executeRun', [\App\Http\Controllers\OrderController::class, 'executeRun']);
                Route::put('{id}/executeClose', [\App\Http\Controllers\OrderController::class, 'executeClose']);

                Route::put('{id}/approve', [\App\Http\Controllers\OrderController::class, 'approve']);
                Route::put('{id}/decline', [\App\Http\Controllers\OrderController::class, 'decline']);
                Route::put('{id}/revision', [\App\Http\Controllers\OrderController::class, 'revision']);
                Route::put('{id}/revisionVideo', [\App\Http\Controllers\OrderController::class, 'revisionVideo']);

                Route::post('/cert', [\App\Http\Controllers\OrderController::class, 'cert']);

                Route::get('{id}/certificate/download', [\App\Http\Controllers\OrderController::class, 'downloadCertByOrderId']);

            });
        });

        Route::prefix('certificate')->group(function () {
            Route::get('/', [\App\Http\Controllers\CertificateController::class, 'get'])->name('certificate');
            Route::get('{id}/file', [\App\Http\Controllers\DocumentController::class, 'getCertificate']);

            Route::middleware(['isModerator'])->group(function () {
                Route::get('{id}/check', [\App\Http\Controllers\CertificateController::class, 'checkById']);
            });
        });

        Route::prefix('notification')->group(function () {
            Route::get('/', [\App\Http\Controllers\NotificationController::class, 'get']);
        });

        Route::prefix('exchange')->group(function () {
            Route::match(['put', 'patch'], '/{id}', [\App\Http\Controllers\ExchangeController::class, 'update']);
            Route::get('/', [\App\Http\Controllers\ExchangeController::class, 'get']);
            Route::post('/', [\App\Http\Controllers\ExchangeController::class, 'store']);
            Route::get('{id}', [\App\Http\Controllers\ExchangeController::class, 'getById']);
            Route::delete('{id}', [\App\Http\Controllers\ExchangeController::class, 'delete']);
            Route::post('{id}/storeFile', [\App\Http\Controllers\ExchangeController::class, 'storeFile']);
            Route::post('{id}/getFile', [\App\Http\Controllers\ExchangeController::class, 'getFile']);
            Route::post('{id}/deleteFile', [\App\Http\Controllers\ExchangeController::class, 'deleteFile']);

            Route::middleware(['isModerator'])->group(function () {
                Route::match(['put', 'patch'], '/{id}/approve', [\App\Http\Controllers\ExchangeController::class, 'approve']);
                Route::match(['put', 'patch'], '/{id}/decline', [\App\Http\Controllers\ExchangeController::class, 'decline']);
                Route::match(['put', 'patch'], '/{id}/message', [\App\Http\Controllers\ExchangeController::class, 'message']);
            });
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
                Route::put('/{id}/delete', [\App\Http\Controllers\TransferDealController::class, 'delete']);
            });
        });

        Route::prefix('file')->group(function () {
            Route::post('/order/store', [\App\Http\Controllers\FileController::class, 'storeOrderFile']);
            Route::post('/order/get', [\App\Http\Controllers\FileController::class, 'getOrderFile']);
            Route::delete('{id}/order', [\App\Http\Controllers\FileController::class, 'deleteOrderFile']);
            Route::get('/order/{id}/generatePFS', [\App\Http\Controllers\FileController::class, 'generateOrderPFS']);

            Route::post('/preorder/store', [\App\Http\Controllers\FileController::class, 'storePreOrderFile']);
            Route::post('/preorder/get', [\App\Http\Controllers\FileController::class, 'getPreOrderFile']);
            Route::post('/preorder/delete', [\App\Http\Controllers\FileController::class, 'deletePreOrderFile']);

            Route::get('/{id}/order/doc', [\App\Http\Controllers\FileController::class, 'getFile']);
            Route::get('/{id}/order/image', [\App\Http\Controllers\FileController::class, 'getImage']);
            Route::get('/{id}/order/video', [\App\Http\Controllers\FileController::class, 'getVideo']);

            Route::get('/{id}/preorder/carFile', [\App\Http\Controllers\FileController::class, 'getCarFile']);
            Route::get('/{id}/preorder/carImage', [\App\Http\Controllers\FileController::class, 'getCarFileImage']);
            Route::get('/{id}/preorder/agroFile', [\App\Http\Controllers\FileController::class, 'getAgroFile']);
            Route::get('/{id}/preorder/agroImage', [\App\Http\Controllers\FileController::class, 'getAgroFileImage']);
            Route::get('/{id}/exchange/download', [\App\Http\Controllers\FileController::class, 'downloadExchangeFile']);
            Route::get('/{id}/sell/download', [\App\Http\Controllers\FileController::class, 'downloadSellFile']);


        });

        Route::prefix('factory')->group(function () {
            Route::get('/', [\App\Http\Controllers\FactoryController::class, 'get']);
            Route::middleware(['isModerator'])->group(function () {
                Route::post('/', [\App\Http\Controllers\FactoryController::class, 'store']);
                Route::get('/{id}', [\App\Http\Controllers\FactoryController::class, 'getById']);
                Route::match(['put', 'patch'], '/{id}', [\App\Http\Controllers\FactoryController::class, 'update']);
            });
        });

        Route::prefix('manufacture')->group(function () {
            Route::get('/', [\App\Http\Controllers\ManufactureController::class, 'get']);
            Route::middleware(['isModerator'])->group(function () {
                Route::post('/', [\App\Http\Controllers\ManufactureController::class, 'store']);
                Route::get('/{id}', [\App\Http\Controllers\ManufactureController::class, 'getById']);
                Route::match(['put', 'patch'], '/{id}', [\App\Http\Controllers\ManufactureController::class, 'update']);
//                Route::delete('/{id}', [\App\Http\Controllers\ManufactureController::class, 'delete']);
            });
        });

        Route::prefix('vehicle')->group(function () {
            Route::get('/', [\App\Http\Controllers\RefFactoryController::class, 'get']);
            Route::middleware(['isModerator'])->group(function () {
                Route::post('/', [\App\Http\Controllers\RefFactoryController::class, 'store']);
                Route::get('/{id}', [\App\Http\Controllers\RefFactoryController::class, 'getById']);
                Route::match(['put', 'patch'], '/{id}', [\App\Http\Controllers\RefFactoryController::class, 'update']);
//                Route::delete('/{id}', [\App\Http\Controllers\RefFactoryController::class, 'delete']);
            });
        });

        Route::prefix('sell')->group(function () {
            Route::get('/', [\App\Http\Controllers\SellController::class, 'get']);
            Route::get('/{id}', [\App\Http\Controllers\SellController::class, 'getById']);
            Route::get('/{id}/files', [\App\Http\Controllers\SellController::class, 'getFilesById']);
            Route::post('/', [\App\Http\Controllers\SellController::class, 'store']);
            Route::put('/{id}', [\App\Http\Controllers\SellController::class, 'update']);
            Route::put('/{id}/getClose', [\App\Http\Controllers\SellController::class, 'updateToSell']);
            Route::middleware(['isModerator'])->group(function () {
                Route::put('/{id}/approve', [\App\Http\Controllers\SellController::class, 'approve']);
                Route::put('/{id}/decline', [\App\Http\Controllers\SellController::class, 'decline']);
                Route::put('/{id}/message', [\App\Http\Controllers\SellController::class, 'message']);
                Route::put('/{id}/close', [\App\Http\Controllers\SellController::class, 'close']);
            });
        });

        Route::prefix('sellFile')->group(function () {
            Route::post('/', [\App\Http\Controllers\SellController::class, 'storeFile']);
            Route::delete('/{id}', [\App\Http\Controllers\SellController::class, 'deleteFile']);
        });

        Route::prefix('report')->group(function () {
            Route::get('/sell', [\App\Http\Controllers\ReportController::class, 'getSell']);
            Route::middleware(['isModerator'])->group(function () {
                Route::get('/exchange', [\App\Http\Controllers\ReportController::class, 'getExchange']);
                Route::get('/cert', [\App\Http\Controllers\ReportController::class, 'getCert']);
                Route::get('/action', [\App\Http\Controllers\ReportController::class, 'getAction']);
            });
        });
    });
});

Route::get('/{any}', function () {
    return view('app');
})->where("any", ".*");
