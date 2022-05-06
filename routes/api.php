<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->group(function() {
    Route::post('logout', [\App\Http\Controllers\AuthController::class,"Logout"]);
    Route::put('update', [\App\Http\Controllers\AuthController::class, 'update']);
    Route::post('item_store',[\App\Http\Controllers\ItemController::class, 'store']);
    Route::get('items', [\App\Http\Controllers\ItemController::class,'index']);
    Route::put('item/{id}',[\App\Http\Controllers\ItemController::class, 'update']);
    Route::delete("item/{id}",[\App\Http\Controllers\ItemController::class, 'destroy']);
    Route::post('trade', [\App\Http\Controllers\TradeController::class, 'store']);
    Route::get('trade', [\App\Http\Controllers\TradeController::class, 'index']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::get('/item/{id}', [\App\Http\Controllers\ItemController::class, 'show']);
Route::get("all_items", [\App\Http\Controllers\ItemController::class, 'allItems']);
Route::post('createaccount',[\App\Http\Controllers\AuthController::class, 'createAccount']);
Route::post('loginaccount', [\App\Http\Controllers\AuthController::class, 'login']);

