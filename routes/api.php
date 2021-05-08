<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ClientsController;
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
});
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function () {
    return 'test2';
});
