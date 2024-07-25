<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Activity;
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
            'status' => Constants::ONLINE
        ]);


        return api(UserResource::make(auth()->user()));
    }

    public function events(Request $request)
    {

        $event = new EventType($request->all());

        $state = $event->participant()->state;
//
//        if ($event->hasParticipant()) {
//
//            if ($event->event === Constants::JOINED) {
//                if ($event->user()->room_id === null) {
//                    $event->user()->update([
//                        'room_id' => $event->room()->id,
//                        'workspace_id' => $event->room()->workspace->id,
//                    ]);
//                }
//
//            }
//            if ($event->event === Constants::LEFT) {
//                $event->user()->update([
//                    'room_id' => null,
//                    'workspace_id' => null,
//                ]);
//
//            }
//
//        }
        $event->user()->activities()->create([
            'event_id' => $event->id,
            'state' => $state,
            'event_type' => $event->event,
            'workspace_id' => $event->room()->workspace->id,
            'room_id' => $event->room()->id,
            'data' => json_encode($request->all()),
        ]);

    }

    public function disconnected()
    {

        $user = auth()->user();
        $user->update([
            'socket_id' => NULL,
            'status' => Constants::OFFLINE,
//            'room_id' => null,
//            'workspace_id' => null,

        ]);

        return api(UserResource::make(auth()->user()));
    }
}
