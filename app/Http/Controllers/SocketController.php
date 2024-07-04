<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Utilities\Constants;
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
