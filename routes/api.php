<?php

use App\Http\Controllers\V1\User\BannerController;
use App\Http\Controllers\V1\User\ServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () { 

    Route::prefix('user')->group(function () {  
        // Route::post('/send-otp', [UserAuthController::class, 'sendOtp']);
        Route::resource('service', ServiceController::class);
        Route::resource('banner', BannerController::class);

        Route::group(['middleware' => ['auth:sanctum']], function () { 
     
        });
    });
});