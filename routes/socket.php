<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->prefix('socket')->group(function () {
    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('/me', 'me');
    });

});
