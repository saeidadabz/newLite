<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Http\Resources\RoomResource;
use Agence104\LiveKit\AccessToken;
use Agence104\LiveKit\AccessTokenOptions;
use Agence104\LiveKit\VideoGrant;
use App\Models\Room;

class RoomController extends Controller
{
    public function get($workspace, $room)
    {
        $user = auth()->user();
        $workspace = $user->workspaces()->find($workspace);
        if ($workspace === NULL) {
            return error('You have no access to this workspace');

        }

        $room = $workspace->rooms()->findOrFail($room);


        return api(RoomResource::make($room));
    }


    public function join(Room $room)
    {
        $user = auth()->user();
        $workspace = $room->workspace;
        $workspace = $user->workspaces->find($workspace->id);
        if ($workspace === NULL) {
            return error('You have no access to this workspace');

        }


        $user->update([
                          'room_id' => $room->id
                      ]);


        $roomName = $room->id;
        $participantName = $user->username;

        $tokenOptions = (new AccessTokenOptions())
            ->setIdentity($participantName);

        $videoGrant = (new VideoGrant())
            ->setRoomJoin()
            ->setRoomName($roomName);

        $token = (new AccessToken('devkey', 'secret'))
            ->init($tokenOptions)
            ->setGrant($videoGrant)
            ->toJwt();

        $room->token = $token;


        return api(RoomResource::make($room));

    }

    public function messages(Room $room)
    {
        return api(MessageResource::collection($room->messages()->paginate(10)));
    }

    public function leave()
    {
        $user = auth()->user();


        $user->update([
                          'room_id' => NULL
                      ]);


        return api(TRUE);

    }
}
