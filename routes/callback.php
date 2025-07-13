<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\CallbackController;


// Route::match(['get', 'post'], '/payment/ssl/success', [CallbackController::class, 'callback']);

Route::match(['get', 'post'], '/payment/ssl/{type?}', [CallbackController::class, 'callback']);

