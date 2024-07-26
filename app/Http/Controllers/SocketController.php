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

////
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
////            if ($event->event === Constants::LEFT) {
////                $event->user()->update([
////                    'room_id' => null,
////                    'workspace_id' => null,
////                ]);
////
////            }
//
//        }

        $user = $event->user();
        $room = $event->room();

        $last_activity = $user->activities()->orderBy('id', 'desc')->first();
        if ($last_activity !== null && $last_activity->left_at === null) {
            $last_activity->update([
                'left_at' => now(),
                'data' => json_encode($request->all()),

            ]);
        }

        if ($event->event === Constants::JOINED) {


            $event->user()->activities()->create([
                'join_at' => now(),
                'left_at' => null,
                'workspace_id' => $room->workspace->id,
                'room_id' => $room->id,
                'data' => json_encode($request->all()),
            ]);


        }


    }

    public function disconnected()
    {

        $user = auth()->user();
        $user->update([
            'socket_id' => NULL,
            'status' => Constants::OFFLINE,
            'room_id' => null,
            'workspace_id' => null,

        ]);

        return api(UserResource::make(auth()->user()));
    }
}
