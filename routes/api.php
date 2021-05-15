<?php

use App\Http\Controllers\API\AccountsController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ClientsController;
use App\Http\Controllers\API\DemandsController;
use App\Http\Controllers\API\InteractionsController;
use App\Http\Controllers\API\PaymentPromisesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

Route::post('/register',[AuthController::class,'register']);
Route::post('/authenticate',[AuthController::class,'authenticate']);
Route::group(['middleware'=>'jwt.auth'],function(){
    Route::prefix('clients')->group(function () {
        Route::post('/', [ClientsController::class,'create']);
    });
    Route::prefix('accounts')->group(function () {
        Route::post('/', [AccountsController::class,'create']);
    });
    Route::prefix('demands')->group(function () {
        Route::post('/', [DemandsController::class,'create']);
    });
    Route::prefix('interactions')->group(function () {
        Route::post('/', [InteractionsController::class,'create']);
    });
    Route::prefix('payment-promise')->group(function () {
        Route::post('/', [PaymentPromisesController::class,'create']);
    });
});
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function () {
    return 'test2';
});
