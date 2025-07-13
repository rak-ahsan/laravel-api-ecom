<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RedxController;
use App\Http\Controllers\Admin\PathaoController;
use App\Http\Controllers\Admin\SteadFastController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Pathao route
    Route::prefix('pathaos')->group(function () {
        Route::controller(PathaoController::class)->group(function () {
            Route::get('/show',                   'show');
            Route::get('/cities',                 'getCity');
            Route::get('/zones/{cityId}',         'getZone');
            Route::get('/areas/{zoneId}',         'getArea');
            Route::get('/stores',                 'getStore');
            Route::post('/stores',                'createStore');
            Route::get('/orders/shipped/{id}',    'orderShipped');
            Route::post('/orders/shipped',        'createOrder');
            Route::post('/cost/calculate',        'costCalculation');
            Route::post('/update/env-credential', 'updateEnvCredential');
        });
    });

    // Stead fast route
    Route::prefix('stead-fasts')->group(function () {
        Route::controller(SteadFastController::class)->group(function () {
            Route::get('/show',                      'show');
            Route::post("/create-order",             'createOrder');
            Route::get("/delivery-status/{orderId}", 'getDeliveryStatus');
            Route::get("/current-balance",           'getCurrentBalance');
            Route::post('/update/env-credential',    'updateEnvCredential');
        });
    });

    // Redx route
    Route::prefix('redxes')->group(function () {
        Route::controller(RedxController::class)->group(function () {
            Route::get('/show',                   'show');
            Route::get('/areas',                  'getArea');
            Route::post('/pickup-stores',         'createPickupStore');
            Route::get('/pickup-stores',          'getPickupStore');
            Route::get('/pickup-stores/details',  'getPickupStoreDetail');
            Route::get('/orders/track/{id}',      'parcelTrack');
            Route::post('/orders/parcel',         'parcelCreate');
            Route::get('/parcel/{id}',            'parcelDetail');
            Route::post('/update/env-credential', 'updateEnvCredential');
        });
    });
});
