<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Utilities\Constants;
use App\Utilities\EventType;
use Illuminate\Http\Request;

class SocketController extends Controller
{
    public function connected(Request $request)
    {

        $user = auth()->user();
        $user->update([
                          'socket_id' => $request->socket_id,
                          'status'    => Constants::ONLINE
                      ]);

        return api(UserResource::make(auth()->user()));
    }

    public function events(Request $request)
    {

        $event = new EventType($request->all());


        logger($request->all());

        return api(TRUE);
    }

    public function disconnected()
    {

        $user = auth()->user();
        $user->update([
                          'socket_id' => NULL,
                          'status'    => Constants::OFFLINE

                      ]);

        return api(UserResource::make(auth()->user()));
    }
}
