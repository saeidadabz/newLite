<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/tester', function () {


    $text = 'salam khobi @katerou22 chekhabar , khobi aghaye @habibi';


    dd(\Illuminate\Support\Str::before(\Illuminate\Support\Str::after($text, '@'), ' '));
});
