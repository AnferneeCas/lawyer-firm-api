<?php

use App\Http\Controllers\API\AccountsController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ClientsController;
use App\Http\Controllers\API\DemandsController;
use App\Http\Controllers\API\FileUploadController;
use App\Http\Controllers\API\InteractionsController;
use App\Http\Controllers\API\PaymentPromisesController;
use App\Models\Interaction;
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
        Route::get('/{id}', [ClientsController::class,'get']);
        Route::get('/', [ClientsController::class,'getAll']);
        Route::put('/{id}', [ClientsController::class,'update']);
        Route::delete('/{id}', [ClientsController::class,'delete']);

        Route::prefix('/{id}/interactions')->group(function(){
            Route::get('/',[InteractionsController::class,'getAllInteractionByClient']);
        });
    });
    Route::prefix('accounts')->group(function () {
        Route::post('/', [AccountsController::class,'create']);
        Route::get('/{id}', [AccountsController::class,'get']);
        Route::put('/{id}', [AccountsController::class,'update']);
        Route::delete('/{id}', [AccountsController::class,'delete']);
    });
    Route::prefix('demands')->group(function () {
        Route::post('/', [DemandsController::class,'create']);
        Route::get('/{id}', [DemandsController::class,'get']);
        Route::put('/{id}', [DemandsController::class,'update']);
        Route::delete('/{id}', [DemandsController::class,'delete']);
    });
    Route::prefix('interactions')->group(function () {
        Route::post('/', [InteractionsController::class,'create']);
        Route::get('/{id}', [InteractionsController::class,'get']);
        Route::put('/{id}', [InteractionsController::class,'update']);
        Route::delete('/{id}', [InteractionsController::class,'delete']);
    });
    Route::prefix('payment-promise')->group(function () {
        Route::post('/', [PaymentPromisesController::class,'create']);
        Route::get('/{id}', [PaymentPromisesController::class,'get']);
        Route::put('/{id}', [PaymentPromisesController::class,'update']);
        Route::delete('/{id}', [PaymentPromisesController::class,'delete']);
    });

    Route::post('/upload-file',[FileUploadController::class,'masterDocumentUpload']);
    Route::post('/download-master',[FileUploadController::class,'downloadMasterDocument']);
});
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function () {
    return 'test2';
});
