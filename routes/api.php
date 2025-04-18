<?php

use App\Http\Controllers\API\KitController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\PrometeoController;
use App\Http\Controllers\RedisController;
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
    Route::get('/quantity/{idUser}','addQuantity');
});

Route::controller(KitController::class)->prefix('kit')->group(function(){
    Route::get('/','index');
    Route::post('/','store');
    Route::get('/{idKit}','show');
    Route::put('/{idKit}','update');
    Route::delete('/{idKit}','destroy');
});


Route::controller(PrometeoController::class)->prefix('prometeo')->group(function(){
    Route::get('/','SolicitudPrometeo');
    Route::get('/job','LlamarCola');
});

Route::controller(RedisController::class)->group(function(){
    Route::get('/redis/create', 'create');
Route::get('/redis/read', 'read');
Route::get('/redis/update', 'update');
Route::get('/redis/delete', 'delete');
});


