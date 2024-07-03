<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tester', function () {

    $before = now();
    $data = [
        'eventName' => 'workspaceUpdated',
        'channel'   => 'workspace-1',
        'data'      => [
            'title' => 'SALAM 123',
        ],
    ];
    \Illuminate\Support\Facades\Http::post('http://localhost:3010/emit', $data);
    $after = now();

    dd($before->diffInMilliseconds($after));
});
