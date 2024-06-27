<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomResource;
use App\Http\Resources\UserResource;
use App\Utilities\Constants;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function me()
    {

        return api(UserResource::make(auth()->user()));
    }

    public function updateCoordinates(Request $request)
    {
        $user = auth()->user();
        $request->validate([
                               'coordinates' => 'required'
                           ]);

        $user->update([
                          'coordinates' => $request->coordinates
                      ]);

        $response = UserResource::make($user);
        sendSocket(Constants::userUpdated, $user->room->channel, $response);

        return api($response);

    }


    public function directs()
    {
        return api(RoomResource::collection(auth()->user()->directs()));
    }
}
