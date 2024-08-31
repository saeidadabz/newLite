<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostmanExportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkspaceController;
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
    Route::apiResource('notifications', NotificationController::class);

    Route::apiResource('calendars', CalendarController::class);
    Route::prefix('calendars')->controller(CalendarController::class)->group(function () {
        Route::get('{calendar}/schedules', 'schedules');
    });


    Route::controller(ScheduleController::class)->prefix('schedules')->group(function () {
        Route::post('/', 'create');
        Route::put('/{schedule}', 'update');
        Route::delete('/{schedule}', 'delete');

    });

    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('/me', 'me');
        Route::get('/activities', 'activities');
        Route::get('/directs', 'directs');
        Route::get('/jobs', 'directs');
        Route::get('/schedules', 'schedules');
        Route::get('/', 'all');
        Route::post('/', 'update');
        Route::post('/updateCoordinates', 'updateCoordinates');
        Route::post('/updateVideoCoordinates', 'updateVideoCoordinates');
        Route::post('/updateScreenshareCoordinates', 'updateScrennshareCoordinates');
        Route::get('/toggleMegaphone', 'toggleMegaphone');
        Route::post('/search', 'search');
        Route::put('/update', 'update');
    });

    Route::controller(WorkspaceController::class)->prefix('workspaces')->group(function () {
        Route::get('/', 'all');
        Route::post('/', 'create');
        //        Route::get('/{workspace}', 'get')->middleware('ownedWorkspace');
        Route::get('/{workspace}', 'get');
        Route::get('/{workspace}/join', 'join');
        Route::get('/{workspace}/rooms', 'rooms');
        Route::get('/{workspace}/jobs', 'jobs');
        Route::get('/{workspace}/users', 'users');
        Route::get('/{workspace}/leaderboard', 'leaderboard');
        Route::get('/{workspace}/calendars', 'calendars');
        Route::get('/{workspace}/tags', 'tags');
        Route::post('/{workspace}/addRole', 'addRole');
        Route::post('/{workspace}/addTag', 'addTag');
        Route::put('/{workspace}', 'update');

    });

    Route::controller(RoomController::class)->prefix('rooms')->group(function () {
        Route::post('/', 'create');
        Route::get('/{room}/', 'get');
        Route::put('/{room}/', 'update');
        Route::get('/{room}/join', 'join');
        Route::get('/{room}/messages', 'messages');
        Route::get('/leave', 'leave');

    });

    Route::controller(InviteController::class)->prefix('invites')->group(function () {
        Route::post('/', 'invite');
        Route::get('/{code}/', 'get');
        Route::get('/{code}/join', 'join');
        Route::get('/{code}/decline', 'decline');

    });

    Route::controller(FileController::class)->prefix('files')->group(function () {
        Route::get('/', 'all');
        Route::post('/', 'upload');
        Route::delete('/{file}', 'delete');
    });

    Route::controller(MessageController::class)->prefix('messages')->group(function () {
        Route::get('/searchMention', 'searchMention');
        Route::get('/{room}', 'get');
        Route::get('/{message}/seen', 'seen');
        Route::get('/{message}/pin', 'pin');
        Route::get('/{message}/unPin', 'unPin');
        Route::post('/', 'send');
        Route::put('/{message}', 'update');
        Route::delete('/{message}', 'delete');
    });

    Route::controller(JobController::class)->prefix('jobs')->group(function () {
        Route::post('/', 'create');
        Route::get('/{job}', 'get');
        Route::put('/{job}', 'update');
        Route::delete('/{job}', 'delete');

    });


    Route::controller(ReportController::class)->prefix('reports')->group(function () {
        Route::post('/', 'create');

    });
    Route::controller(SettingController::class)->prefix('settings')->group(function () {
        Route::post('/', 'set');

    });

});


Route::get('export-postman', PostmanExportController::class)->name('postman');

require __DIR__ . '/socket.php';
