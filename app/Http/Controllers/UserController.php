<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomResource;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function me()
    {

        return api(UserResource::make(auth()->user()));
    }


    public function directs()
    {
        return api(RoomResource::collection(auth()->user()->directs()));
    }
}
