<?php

use App\Http\Controllers\V1\Admin\AIRoleController;
use App\Http\Controllers\V1\User\AIRoleController as UserAIRoleController;
use App\Http\Controllers\V1\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\V1\User\AuthController as UserAuthController;
use App\Http\Controllers\V1\Admin\CategoryController;
use App\Http\Controllers\V1\Admin\MenuOptionController;
use App\Http\Controllers\V1\Admin\SingleTileController;
use App\Http\Controllers\V1\Admin\ThoughtsCategoryController;
use App\Http\Controllers\V1\User\SingleTileController as UserSingleTileController;
use App\Http\Controllers\V1\User\VideoTileController as UserVideoTileController;
use App\Http\Controllers\V1\User\VideoController as UserVideoController;
use App\Http\Controllers\V1\User\ChatController as UserChatController;
use App\Http\Controllers\V1\Admin\UserController;
use App\Http\Controllers\V1\Admin\VideoController;
use App\Http\Controllers\V1\Admin\VideoTileController;
use App\Http\Controllers\V1\User\MenuOptionController as UserMenuOptionController;
use App\Http\Controllers\V1\User\ThoughtsCategoryController as UserThoughtsCategoryController;
use App\Http\Controllers\V2\User\SingleTileController as V2UserSingleTileController;
use App\Http\Controllers\V2\User\VideoTileController as V2UserVideoTileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () { 

    Route::prefix('admin')->group(function () {  
        Route::post("/login",[AdminAuthController::class,'login']);
        Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
            Route::get('/users', [UserController::class, 'index']);
            Route::delete('/users/{id}', [UserController::class, 'destroy']);
            Route::get('/users/total', [UserController::class, 'getTotalUsers']);
            Route::post('/update-users/{id}', [UserController::class, 'update']);

            Route::post('/ai-roles', [AIRoleController::class, 'store']);
            Route::get('/ai-roles', [AIRoleController::class, 'index']);
            Route::delete('/ai-roles/{id}', [AIRoleController::class, 'destroy']);
            Route::post('/update-ai-roles/{id}', [AIRoleController::class, 'update']);

            Route::get('/languages', [AIRoleController::class, 'fetchLanguage']);
    
            Route::post('/single-tiles', [SingleTileController::class, 'store']);
            Route::get('/single-tiles', [SingleTileController::class, 'index']);
            Route::delete('/single-tiles/{id}', [SingleTileController::class, 'destroy']);
            Route::post('/update-single-tiles/{id}', [SingleTileController::class, 'update']);
    
            Route::post('/categories', [CategoryController::class, 'store']);
            Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
            Route::get('/categories', [CategoryController::class, 'index']);
            Route::post('/update-categories/{id}', [CategoryController::class, 'update']);

            Route::post('/thoughts-categories', [ThoughtsCategoryController::class, 'store']);
            Route::delete('/thoughts-categories/{id}', [ThoughtsCategoryController::class, 'destroy']);
            Route::get('/thoughts-categories', [ThoughtsCategoryController::class, 'index']);
            Route::post('/thoughts-update-categories/{id}', [ThoughtsCategoryController::class, 'update']);
    
            Route::post('/video-tiles', [VideoTileController::class, 'store']);
            Route::get('/video-tiles', [VideoTileController::class, 'index']);
            Route::delete('/video-tiles/{id}', [VideoTileController::class, 'destroy']);
            Route::post('/update-video-tiles/{id}', [VideoTileController::class, 'update']);

            Route::get('/videos', [VideoController::class, 'index']);
            Route::post('/videos', [VideoController::class, 'store']);
            Route::post('/update-videos/{id}', [VideoController::class, 'update']);
            Route::delete('/videos/{id}', [VideoController::class, 'destroy']);

            Route::apiResource('menu-options', MenuOptionController::class);
            Route::post('/menu-options/update/{id}', [MenuOptionController::class, 'update']);
        });
    });

    Route::prefix('user')->group(function () {  
        Route::post('/send-otp', [UserAuthController::class, 'sendOtp']);
        Route::post('/verify-otp', [UserAuthController::class, 'verifyOtp']);

        Route::post('/guest-login', [UserAuthController::class, 'guestLogin']);
        
        Route::get('/languages', [UserAIRoleController::class, 'fetchLanguage']);
        Route::get('/ai-roles', [UserAIRoleController::class, 'index']);

        Route::group(['middleware' => ['auth:sanctum']], function () { 
            Route::post('/send-otp-guest-user', [UserAuthController::class, 'sendOtpGuestUser']);
            Route::post('/verify-otp-guest-user', [UserAuthController::class, 'verifyOtpGuestUser']);
            Route::get('/me', [UserAuthController::class, 'me']);
            Route::get('/delete-account', [UserAuthController::class, 'deleteAccount']);
            Route::post('/user-name', [UserAuthController::class, 'storeUsername']);

            Route::post('/user-preferences', [UserAIRoleController::class, 'storeUserPreferences']);

            Route::get('/single-tiles', [UserSingleTileController::class, 'index']);

            Route::get('/video-tiles', [UserVideoTileController::class, 'index']);

            Route::get('/videos/{categoryId}', [UserVideoController::class, 'index']);
            Route::get('/categories', [UserVideoController::class, 'fetchCategories']);

            Route::post('/chat', [UserChatController::class, 'store']);
            Route::get('/chat/{aiRoleId}', [UserChatController::class, 'index']);
            Route::delete('/chat', [UserChatController::class, 'destroy']);
            Route::post('/chat/reaction/{message_id}', [UserChatController::class, 'updateReactionByMessageId']);

            Route::get('/thoughts-categories', [UserThoughtsCategoryController::class, 'index']);

            Route::get('/menu-options', [UserMenuOptionController::class, 'index']);
        });
    });
});
Route::prefix('v2')->group(function () { 
    Route::prefix('user')->group(function () {  
        Route::group(['middleware' => ['auth:sanctum']], function () { 
            Route::get('/single-tiles', [V2UserSingleTileController::class, 'index']);
            Route::get('/video-tiles', [V2UserVideoTileController::class, 'index']);
        });
    });
});
