<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');


Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/checkUsername', 'checkUsername');
});


Route::middleware('auth:sanctum')->group(function () {
    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('/me', 'me');
        Route::get('/', 'all');
        Route::post('/', 'update');
    });


    Route::controller(WorkspaceController::class)->prefix('workspaces')->group(function () {
        Route::get('/', 'all');
        Route::post('/', 'create');
        Route::get('/{workspace}', 'get')->middleware('ownedWorkspace');
        Route::get('/{workspace}/join', 'get');
        Route::get('/{workspace}/rooms', 'rooms');
        Route::put('/{workspace}', 'update');

    });


    Route::controller(RoomController::class)->prefix('rooms')->group(function () {
//        Route::get('/', 'all');
//        Route::post('/', 'create');
        Route::get('/{workspace}/{room}/join', 'get');
        Route::get('/leave', 'leave');
//        Route::get('/{workspace}/join', 'get');
//        Route::get('/{workspace}/rooms', 'rooms');
//        Route::put('/{workspace}', 'update');

    });


    Route::controller(FileController::class)->prefix('files')->group(function () {
        Route::get('/', 'all');
        Route::post('/', 'upload');
        Route::delete('/{file}', 'delete');
    });


});
