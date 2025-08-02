<?php

use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\StockTransferController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    // Authentication routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);


    Route::middleware(['auth:sanctum'])->group(function () {
        // Auth user routes
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);

        Route::group(['prefix' => 'stock_transfers'], function () {
            Route::get('index', [StockTransferController::class, 'index']);
            Route::get('statusFilter', [StockTransferController::class, 'statusFilter']);
            Route::post('store', [StockTransferController::class, 'store']);
            Route::post('{stockTransfer}/change_status', [StockTransferController::class, 'changeStatus']);
            Route::get('{stockTransfer}/info_details', [StockTransferController::class, 'infoDetails']);
            Route::post('{stockTransfer}/cancel_or_return', [StockTransferController::class, 'cancelOrReturn']);
        });
    });

});
