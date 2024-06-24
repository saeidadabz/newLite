<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageListResource;
use App\Http\Resources\MessageResource;
use App\Models\Direct;
use App\Models\File;
use App\Models\Message;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function send(Request $request, $room = null)
    {
        $request->validate([
            'text' => 'required'
        ]);

        $user = auth()->user();

        if ($room === null) {

            $receiver = User::find($request->user_id);
            $directs = $user->directs();

            $directs_with_receiver = Direct::whereIn('room_id', $directs->pluck('room_id')->toArray())->where('user_id', $receiver->id)->first();
            if ($directs_with_receiver === null) {
                $room = Room::create([

                    'title' => 'Chat With ' . $receiver->username, 'is_private' => true, 'user_id' => $user->id
                ]);
                Direct::create([
                    'room_id' => $room->id,
                    'user_id' => $user->id
                ]);
                Direct::create([
                    'room_id' => $room->id,
                    'user_id' => $receiver->id
                ]);
            } else {
                $room = $directs_with_receiver->room;
            }

        }


        $message = $room->messages()->create([
            'text' => $request->text,
            'reply_to' => $request->reply_to,
            'user_id' => $user->id
        ]);

        File::syncFile($request->file_id, $message);

        return api(MessageResource::make($message));

    }


    public function get(Room $room)
    {
        $user = auth()->user();
        //TODO check if user is in room

        $messages = $room->messages;

        return api(MessageListResource::collection($messages));

    }


    public function update(Message $message, Request $request)
    {
        $user = auth()->user();


        $message->update([
            'text' => $request->text
        ]);


        File::syncFile($request->file_id, $message);

        return api(MessageResource::make($message));


    }


}
