<?php


use App\Http\Controllers\MailController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

    Route::group(['prefix' => '/v1/auth'], function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/verify',[AuthController::class,'verifyAccount']);
    })->middleware(['auth:api']);

    Route::group(['prefix' => '/v1/products'], function () {
        Route::get(null,[ProductController::class,'getAllProduct']);
        Route::post(null,[ProductController::class,'searchProduct']);
    });
    Route::group(['prefix' => '/v1/orders'], function () {
        Route::post(null,[OrderController::class,'addOrders']);

    })->middleware(['auth:api']);

Route::group(['prefix' => '/v1/profiles'], function () {
    Route::get(null,[UserController::class,'getProfile']);
    Route::put(null,[UserController::class,'updateProfile']);
})->middleware(['permission:get_profile|update_profile']);

Route::group(['prefix' => '/v1/carts'], function () {
    Route::get(null,[\App\Http\Controllers\CartController::class,'getCart']);
    Route::post(null,[\App\Http\Controllers\CartController::class,'addCart']);
})->middleware(['permission:READ']);
Route::get('/v1/admin/sales',[OrderController::class,'salesOrders']);
Route::get('/v1/admin/status',[OrderController::class,'numberOfStatus']);

