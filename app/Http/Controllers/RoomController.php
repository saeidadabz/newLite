<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Http\Resources\RoomResource;
use App\Models\File;
use App\Models\Room;
use App\Models\Seen;
use App\Models\Workspace;
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


    public function create(Request $request)
    {


        $request->validate([
                               'workspace_id' => 'required',
                               'title'        => 'required',
                           ]);

        //TODO:check has create room permission

        $workspace = Workspace::findOrFail($request->workspace_id);
        $user = auth()->user();

        $room = $workspace->rooms()->create([
                                                'title'   => $request->title,
                                                'user_id' => $user->id
                                            ]);


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


        $res = RoomResource::make($room);


        sendSocket(Constants::roomUpdated, $room->channel, $res);


        return api($res);

    }

    public function messages(Room $room)
    {
        $user = auth()->user();

        $messages = $room->messages()->orderByDesc('id')->paginate(10);
        //        foreach ($messages->items() as $msg) {
        //
        //            Seen::firstOrCreate([
        //                                    'user_id'    => $user->id,
        //                                    'room_id'    => $room->id,
        //                                    'message_id' => $msg->id
        //                                ]);
        //        }
        //
        //        sendSocket(Constants::roomUpdated, $room->channel, RoomResource::make($room));
        //
        //        // TODO: CODE UPPER HAS TO DELETED, JUST SET FOR MEHDI RASTI TILL SEEN MESSAGES ON VIEWPORT


        return api(MessageResource::collection($messages));
    }

    public function leave()
    {
        $user = auth()->user();

        $user->update([
                          'room_id' => NULL,
                      ]);

        return api(TRUE);

    }
}
