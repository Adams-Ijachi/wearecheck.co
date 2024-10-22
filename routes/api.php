<?php

use App\Http\Controllers\GetWalletBalanceController;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\LoginController;
use App\Http\Controllers\Transactions\CreateUserTransactionController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function (){


    Route::group(['prefix' => 'auth'], function (){
            Route::post('login', LoginController::class);
            Route::post('register', RegisterController::class);

    });

    Route::group(['prefix' => 'transactions', 'middleware' => 'auth:sanctum'], function (){
        Route::post('/', CreateUserTransactionController::class);
    });

    Route::group(['prefix' => 'wallet', 'middleware' => 'auth:sanctum'], function (){
        Route::get('balance', GetWalletBalanceController::class);
    });


});


