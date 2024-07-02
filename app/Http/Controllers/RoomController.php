<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Http\Resources\RoomResource;
use Agence104\LiveKit\AccessToken;
use Agence104\LiveKit\AccessTokenOptions;
use Agence104\LiveKit\VideoGrant;
use App\Models\File;
use App\Models\Room;
use App\Utilities\Constants;
use Illuminate\Http\Request;

class RoomController extends Controller
{


    public function update(Room $room, Request $request)
    {
        //TODO CHECK PERMISSION
        $room->update($request->all());

        File::syncFile($request->background_id, $room, 'background');
        File::syncFile($request->logo_id, $room, 'logo');

        sendSocket(Constants::roomUpdated, $room->channel, RoomResource::make($room));

        return api(RoomResource::make($room));


    }

    public function get(Room $room)
    {
//        $user = auth()->user();
//        $workspace = $user->workspaces()->find($workspace);
//        if ($workspace === NULL) {
//            return error('You have no access to this workspace');
//
//        }
//
//        $room = $workspace->rooms()->findOrFail($room);


        return api(RoomResource::make($room));
    }


    public function join(Room $room)
    {
        $user = auth()->user();

        $room = $room->joinUser($user);


        return api(RoomResource::make($room));

    }

    public function messages(Room $room)
    {
        return api(MessageResource::collection($room->messages()->orderByDesc('id')->paginate(10)));
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
