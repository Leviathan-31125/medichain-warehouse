<?php

use App\Http\Controllers\auth\UserAuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TempDODTLController;
use App\Http\Controllers\TempDOMSTController;
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
    ->middleware('auth:api')->group(function () {
        Route::get('/', 'getAllTempDOMST');
        Route::get('/{fc_dono}', 'detailTempDOMST');
        Route::post('/', 'createTempDOMST');
        Route::put('/{fc_dono}', 'setDetailInfoTempDODTL');
        Route::put('/{fc_dono}/submit', 'submitTempDOMST');
        Route::put('/{fc_dono}/receiving', 'updateRecevingStatus');
    });

    Route::prefix('temp-do-dtl')->controller(TempDODTLController::class)->group(function () {
        Route::get('/{fc_dono}', 'getTempDODTLbyDONO');
        Route::post('/{fc_dono}', 'addTempDODTL');
        Route::delete('/{fc_dono}', 'removeTempDODTL');
        Route::put('/{fc_dono}', 'updateTempDODTL');
    });
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