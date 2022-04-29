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
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('logout', [\App\Http\Controllers\AuthController::class,"Logout"]);
});
Route::post('createaccount',[\App\Http\Controllers\AuthController::class, 'createAccount']);
Route::post('loginaccount', [\App\Http\Controllers\AuthController::class, 'login']);

