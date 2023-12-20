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
        Route::get('/verify',[AuthController::class,'verifyAccount']);
    })->middleware(['auth:api']);
Route::post('/v1/changePermission',[AuthController::class,'addPermissions'])
    ->middleware(['permission:add_permission']);

    Route::group(['prefix' => '/v1/products'], function () {
        Route::get(null,[ProductController::class,'getAllProducts']);
        Route::get('/{id}',[ProductController::class,'getProduct']);
    });
Route::post('/v1/products',[\App\Http\Controllers\ProductController::class,'addProduct'])->middleware('permission:add_product');


Route::group(['prefix' => '/v1/orders'], function () {
        Route::post(null,[OrderController::class,'addOrders']);
        Route::get(null,[OrderController::class,'getOrders']);
        Route::get('/{code}',[OrderController::class,'getOrder']);
//        Route::post()
    })->middleware(['auth:api']);

Route::get('/v1/verifyOrder',[OrderController::class,'verifyOrder']);
Route::post('/v1/changeStatusOrder',[OrderController::class,'changeStatusOrder'])
    ->middleware(['permission:change_status_order']);
Route::post('/v1/cancelOrder',[OrderController::class,'cancelOrder'])
    ->middleware(['permission:cancel_order']);

Route::group(['prefix' => '/v1/profiles'], function () {
    Route::get(null,[UserController::class,'getProfile']);
    Route::put(null,[UserController::class,'updateProfile']);
})->middleware(['permission:get_profile|update_profile']);

Route::group(['prefix' => '/v1/carts'], function () {
    Route::get(null,[\App\Http\Controllers\CartController::class,'getCart']);
    Route::post(null,[\App\Http\Controllers\CartController::class,'addCart']);
})->middleware(['permission:READ']);

Route::group(['prefix' => '/v1/reviews'], function () {
    Route::post(null,[\App\Http\Controllers\ReviewController::class,'addReview']);
    Route::get(null,[\App\Http\Controllers\ReviewController::class,'productReview']);
    Route::put(null,[\App\Http\Controllers\ReviewController::class,'editReview']);
    Route::delete(null,[\App\Http\Controllers\ReviewController::class,'deleteReview']);
})->middleware(['permission:review']);

