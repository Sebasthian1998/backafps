<?php

use App\Http\Controllers\API\KitController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(UserController::class)->prefix('user')->group(function(){
    Route::get('/','index');
    Route::post('/','store');
    Route::get('/{idUser}','show');
    Route::put('/{idUser}','update');
    Route::delete('/{idUser}','destroy');
});

Route::controller(KitController::class)->prefix('kit')->group(function(){
    Route::get('/','index');
    Route::post('/','store');
    Route::get('/{idKit}','show');
    Route::put('/{idKit}','update');
    Route::delete('/{idKit}','destroy');
});
