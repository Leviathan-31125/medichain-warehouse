<?php

use App\Http\Controllers\auth\UserAuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\DOMSTController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\InvStoreController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TempDODTLController;
use App\Http\Controllers\TempDOMSTController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('stock')->group(function () {
    Route::get('/', [StockController::class, 'getAllStock']);
    Route::get('/{fc_barcode}', [StockController::class, 'getDetailStock']);
    Route::put('/{fc_barcode}', [StockController::class, 'updateStock']);
    Route::post('/', [StockController::class, 'createStock']);
    Route::delete('/{fc_barcode}', [StockController::class, 'deleteStock']);
});

Route::prefix('brand')->group(function () {
    Route::get('/', [BrandController::class, 'getAllBrands']);
    Route::get('/{fc_brandcode}', [BrandController::class, 'getDetailBrand']);
    Route::put('/{fc_brandcode}', [BrandController::class, 'updateBrand']);
    Route::post('/', [BrandController::class, 'createBrands']);
    Route::delete('/{fc_brandcode}', [BrandController::class, 'deleteBrand']);
});

Route::prefix('delivery-order')->group(function() {
    Route::prefix('temp-do-mst')->controller(TempDOMSTController::class)
    ->group(function () {
        Route::get('/', 'getAllTempDOMST');
        Route::get('/{fc_dono}', 'detailTempDOMST');
        Route::get('/so/{fc_dono}', 'getDetailTempDOSOMST');
        Route::get('/do/{fc_dono}', 'checkActiveDO');
        Route::post('/', 'createTempDOMST');
        Route::put('/{fc_dono}', 'setDetailInfoTempDODTL');
        Route::put('/{fc_dono}/submit', 'submitTempDOMST');
        Route::put('/{fc_dono}/cancel', 'cancelTempDOMST');
    });

    Route::prefix('temp-do-dtl')->controller(TempDODTLController::class)->group(function () {
        Route::get('/{fc_dono}', 'getTempDODTLbyDONO');
        Route::post('/{fc_dono}', 'addTempDODTL');
        Route::delete('/{fc_dono}', 'removeTempDODTL');
        Route::put('/{fc_dono}', 'updateTempDODTL');
    });

    Route::prefix('domst')->controller(DOMSTController::class)->group(function(){
        Route::get('/', 'getAllDOMST');
        Route::put('/{fc_dono}/receiving', 'updateRecevingStatus');
    });

    Route::prefix('sales-order')->controller(SalesOrderController::class)->group(function() {
        Route::get('/','getAllSOMST');
    });
});

Route::prefix('warehouse')->controller(WarehouseController::class)->group(function(){
    Route::get('/', 'getAllWarehouse');
    Route::get('/{fc_warehousecode}', 'getDetailWarehouse');
    Route::get('/invstore/list', 'getWarehouseWithInvStore');
    Route::post('/', 'createWarehouse');
    Route::put('/{fc_warehousecode}', 'updateWarehouse');
    Route::delete('/{fc_warehousecode}', 'deletedWarehouse');
});

Route::controller(UserAuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::get('/session', 'getSession')->middleware('serviceAuth');
    Route::get('/details', 'details')->middleware('auth:api');
    Route::put('/logout', 'logOut')->middleware('auth:api');
});

Route::controller(GeneralController::class)->prefix('general')->group(function(){
    Route::get('/get-type-stock', 'getTypeStock');
    Route::get('/get-form-stock', 'getFormStock');
    Route::get('/get-name-pack', 'getNamePack');
});

Route::controller(InvStoreController::class)->prefix('invstore')->group(function(){
    Route::get('/', 'getAllInvStore');
    Route::get('/{fc_barcode}', 'getDetailInvStore');
    Route::get('/warehouse/{fc_warehousecode}', 'getInvStoreByWarehouse');
    Route::get('/stock/{fc_barcode}', 'getInvStoreByIntBarcode');
    Route::get('/inquiry/all', 'getAllTrackStock');
});