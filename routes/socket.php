<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SocketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->prefix('socket')->group(function () {
    Route::controller(SocketController::class)->group(function () {
        Route::post('/connected', 'connected');
        Route::get('/disconnected', 'disconnected');
    });


});
