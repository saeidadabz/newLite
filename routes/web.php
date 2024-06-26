<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/tester', function () {

    $user = \App\Models\User::first();
    dd($user->giveRole('member', 1));
    $data = [
        'eventName' => 'workspaceUpdated',
        'channel'   => 'workspace-1',
        'data'      => [
            'title' => 'SALAM 123'
        ]
    ];
    $res = \Illuminate\Support\Facades\Http::post('http://localhost:3000/emit', $data);
    dd($res->json());
});
