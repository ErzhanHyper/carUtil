<?php

use App\Http\Controllers\BookingOrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CheckupController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\FactoryController;
use App\Http\Controllers\FileTypeController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ManufactureController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PreOrderController;
use App\Http\Controllers\RefFactoryController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\TransferDealController;
use App\Http\Controllers\TransferOrderController;
use App\Http\Controllers\UserController;
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

// Auth Routes
Route::prefix('app')->group(function () {
    require __DIR__ . '/auth.php';
});

Route::prefix('app')->group(function () {
    Route::middleware(['checkToken'])->group(function () {

        // Document Routes
        require __DIR__ . '/document.php';
        // File Routes
        require __DIR__ . '/file.php';
        // Order Routes
        require __DIR__ . '/order.php';

        Route::get('category', [CategoryController::class, 'get']);
        Route::get('category/complect', [CategoryController::class, 'getComplect']);

        Route::get('region', [RegionController::class, 'get']);
        Route::get('fileType', [FileTypeController::class, 'get']);
        Route::get('fileTypeAgro', [FileTypeController::class, 'getAgro']);
        Route::get('log', [LogController::class, 'get']);

        Route::post('booking/store', [BookingOrderController::class, 'store']);
        Route::post('booking/order', [BookingOrderController::class, 'get']);
        Route::post('booking/order/delete', [BookingOrderController::class, 'delete']);

        Route::prefix('checkup')->group(function () {
            Route::middleware(['isModerator'])->group(function () {
                Route::get('{id}/getCert', [CheckupController::class, 'getCertById']);
                Route::get('{id}/downloadCert', [CheckupController::class, 'downloadCertByOrderId']);
            });
        });

        Route::middleware(['isModerator'])->group(function () {
            Route::get('role', [UserController::class, 'role']);

            Route::prefix('user')->group(function () {
                Route::get('/', [UserController::class, 'get']);
                Route::get('/{id}', [UserController::class, 'getById']);
                Route::post('/store', [UserController::class, 'store']);
                Route::put('/{id}/update', [UserController::class, 'update']);
            });
        });

        Route::prefix('preorder')->group(function () {
            Route::get('/', [PreOrderController::class, 'get'])->name('preorder');
            Route::post('/', [PreOrderController::class, 'store']);
            Route::delete('/{id}', [PreOrderController::class, 'delete']);

            Route::get('/{id}', [PreOrderController::class, 'getById']);
            Route::put('/{id}/send', [PreOrderController::class, 'send']);

            Route::post('checkVehicle', [PreOrderController::class, 'searchFromKap']);
            Route::post('checkVehicleHistory', [PreOrderController::class, 'kapHistory']);

            Route::middleware(['isModerator'])->group(function () {
                Route::put('{id}/approve', [PreOrderController::class, 'approve']);
                Route::put('{id}/decline', [PreOrderController::class, 'decline']);
                Route::put('{id}/revision', [PreOrderController::class, 'revision']);
            });
        });

        Route::prefix('certificate')->group(function () {
            Route::get('/', [CertificateController::class, 'get'])->name('certificate');
            Route::get('{id}/file', [DocumentController::class, 'getCertificate']);
        });

        Route::prefix('notification')->group(function () {
            Route::get('/', [NotificationController::class, 'get']);
        });

        Route::prefix('exchange')->group(function () {
            Route::match(['put', 'patch'], '/{id}', [ExchangeController::class, 'update']);
            Route::get('/', [ExchangeController::class, 'get']);
            Route::post('/', [ExchangeController::class, 'store']);
            Route::get('{id}', [ExchangeController::class, 'getById']);
            Route::delete('{id}', [ExchangeController::class, 'delete']);
            Route::post('{id}/storeFile', [ExchangeController::class, 'storeFile']);
            Route::post('{id}/getFile', [ExchangeController::class, 'getFile']);
            Route::post('{id}/deleteFile', [ExchangeController::class, 'deleteFile']);

            Route::middleware(['isModerator'])->group(function () {
                Route::match(['put', 'patch'], '/{id}/approve', [ExchangeController::class, 'approve']);
                Route::match(['put', 'patch'], '/{id}/decline', [ExchangeController::class, 'decline']);
                Route::match(['put', 'patch'], '/{id}/message', [ExchangeController::class, 'message']);
            });
        });

        Route::prefix('transfer')->group(function () {
            Route::get('/orderCurrent', [TransferOrderController::class, 'getCurrent']);
            Route::prefix('order')->group(function () {
                Route::get('/', [TransferOrderController::class, 'get']);
                Route::get('/{id}', [TransferOrderController::class, 'getById']);
                Route::post('/', [TransferOrderController::class, 'store']);
                Route::delete('/{id}', [TransferOrderController::class, 'delete']);
                Route::put('/{id}/sign', [TransferOrderController::class, 'sign']);
            });

            Route::prefix('deal')->group(function () {
                Route::get('/', [TransferDealController::class, 'get']);
                Route::post('/', [TransferDealController::class, 'store']);
                Route::put('/{id}/accept', [TransferDealController::class, 'accept']);
                Route::put('/{id}/close', [TransferDealController::class, 'close']);
                Route::put('/{id}/delete', [TransferDealController::class, 'delete']);
            });
        });

        Route::prefix('factory')->group(function () {
            Route::get('/', [FactoryController::class, 'get']);
            Route::middleware(['isModerator'])->group(function () {
                Route::post('/', [FactoryController::class, 'store']);
                Route::get('/{id}', [FactoryController::class, 'getById']);
                Route::match(['put', 'patch'], '/{id}', [FactoryController::class, 'update']);
            });
        });

        Route::prefix('manufacture')->group(function () {
            Route::get('/', [ManufactureController::class, 'get']);
            Route::middleware(['isModerator'])->group(function () {
                Route::post('/', [ManufactureController::class, 'store']);
                Route::get('/{id}', [ManufactureController::class, 'getById']);
                Route::match(['put', 'patch'], '/{id}', [ManufactureController::class, 'update']);
//                Route::delete('/{id}', [\App\Http\Controllers\ManufactureController::class, 'delete']);
            });
        });

        Route::prefix('vehicle')->group(function () {
            Route::get('/', [RefFactoryController::class, 'get']);
            Route::middleware(['isModerator'])->group(function () {
                Route::post('/', [RefFactoryController::class, 'store']);
                Route::get('/{id}', [RefFactoryController::class, 'getById']);
                Route::match(['put', 'patch'], '/{id}', [RefFactoryController::class, 'update']);
//                Route::delete('/{id}', [\App\Http\Controllers\RefFactoryController::class, 'delete']);
            });
        });

        Route::prefix('sell')->group(function () {
            Route::get('/', [SellController::class, 'get']);
            Route::get('/{id}', [SellController::class, 'getById']);
            Route::get('/{id}/files', [SellController::class, 'getFilesById']);
            Route::post('/', [SellController::class, 'store']);
            Route::put('/{id}', [SellController::class, 'update']);
            Route::put('/{id}/getClose', [SellController::class, 'updateToSell']);
            Route::middleware(['isModerator'])->group(function () {
                Route::put('/{id}/approve', [SellController::class, 'approve']);
                Route::put('/{id}/decline', [SellController::class, 'decline']);
                Route::put('/{id}/message', [SellController::class, 'message']);
                Route::put('/{id}/close', [SellController::class, 'close']);
            });
        });

        Route::prefix('sellFile')->group(function () {
            Route::post('/', [SellController::class, 'storeFile']);
            Route::delete('/{id}', [SellController::class, 'deleteFile']);
        });

        Route::prefix('report')->group(function () {
            Route::get('/sell', [ReportController::class, 'getSell']);
            Route::middleware(['isModerator'])->group(function () {
                Route::get('/exchange', [ReportController::class, 'getExchange']);
                Route::get('/cert', [ReportController::class, 'getCert']);
                Route::get('/action', [ReportController::class, 'getAction']);
            });
        });
    });
});

Route::get('/{any}', function () {
    return view('app');
})->where("any", ".*");
