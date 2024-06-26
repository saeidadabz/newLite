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
    public function send(Request $request)
    {
        $request->validate([
            'text' => 'required'
        ]);

        $user = auth()->user();
        $eventName = 'roomMessages';

        if ($request->room_id === NULL) {
            $request->validate([
                'user_id' => 'required'
            ]);


            $receiver = User::find($request->user_id);


            $users = [
                $receiver->id,
                $user->id
            ];
            asort($users);
            $roomTitle = implode('-', $users);

            $room = Room::where('title', $roomTitle)->first();
            if ($room === NULL) {
                $room = Room::create([

                    'title' => implode('-', $users),
                    'is_private' => TRUE,
                ]);
            }
            $eventName = 'directMessages';


        } else {
            $room = Room::findOrFail($request->room_id);
            if (!$room->workspace->hasUser($user)) {
                return error('You are not authorized');
            }
        }


        $message = $room->messages()->create([
            'text' => $request->text,
            'reply_to' => $request->reply_to,
            'user_id' => $user->id
        ]);


        //EMIT TO USER
        sendSocket($eventName, $room->channel, MessageResource::make($message));


        if ($request->get('files')) {
            foreach ($request->get('files') as $file) {
                File::syncFile($file, $message);

            }
        }

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
