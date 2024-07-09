<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageListResource;
use App\Http\Resources\MessageResource;
use App\Http\Resources\RoomResource;
use App\Models\File;
use App\Models\Message;
use App\Models\Room;
use App\Models\Seen;
use App\Models\User;
use App\Models\Workspace;
use App\Utilities\Constants;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
                               'text' => 'required'
                           ]);

        $user = auth()->user();
        $eventName = Constants::roomMessages;

        if ($request->room_id === null) {
            $request->validate([
                                   'user_id' => 'required'
                               ]);


            $users = [
                $request->user_id,
                $user->id
            ];
            asort($users);
            $roomTitle = implode('-', $users);


            $room = Room::firstOrCreate(
                ['title' => $roomTitle],
                ['is_private' => true]
            );
            $eventName = Constants::directMessages;


        } else {
            $room = Room::findOrFail($request->room_id);
            //            if (!$room->workspace->hasUser($user)) {
            //                return error('You are not authorized');
            //            }
        }


        $message = Message::create([
                                       'text'     => $request->text,
                                       'reply_to' => $request->reply_to,
                                       'user_id'  => $user->id,
                                       'room_id'  => $room->id,
                                   ]);

        if ($request->mentions) {
            $models = [
                'user'      => User::class,
                'room'      => Room::class,
                'workspace' => Workspace::class,
            ];
            foreach ($request->mentions as $mention) {
                $message->mentions()->create([
                                                 'user_id'          => $user->id,
                                                 'start_position'   => $mention['start_position'],
                                                 'mentionable_type' => $models[$mention['model_type']],
                                                 'mentionable_id'   => $mention['model_id']

                                             ]);
            }
        }
        $messageResponse = MessageResource::make($message);
        //EMIT TO USER
        sendSocket($eventName, $room->channel, $messageResponse);


        Seen::firstOrCreate([
                                'user_id'    => $user->id,
                                'room_id'    => $room->id,
                                'message_id' => $message->id
                            ]);


        sendSocket(Constants::roomUpdated, $room->channel, RoomResource::make($room));


        if ($request->get('files')) {
            foreach ($request->get('files') as $file) {
                File::syncFile($file, $message);

            }
        }


        return api($messageResponse);

    }

    public function seen(Message $message)
    {
        $user = auth()->user();
        $room = $message->room;
        //        if (!$room->participants()->contains('id', $user->id)) {
        //            return error('You cant seen this message');
        //        }

        Seen::firstOrCreate([
                                'user_id'    => $user->id,
                                'room_id'    => $room->id,
                                'message_id' => $message->id
                            ]);

        sendSocket(Constants::roomUpdated, $room->channel, RoomResource::make($room));


        return api(true);


    }

    public function searchMention(Request $request)
    {


    }

    public function get(Room $room)
    {
        $user = auth()->user();
        //TODO check if user is in room

        $messages = $room->messages;

        return api(MessageListResource::collection($messages));

    }

    public function pin(Message $message)
    {
        //TODO: check user can pin message in this room

        $message->update([
                             'is_pinned' => true
                         ]);

    }


    public function update(Message $message, Request $request)
    {
        //        $user = auth()->user();

        //TODO: check user owned msg
        $message->update([
                             'text'      => $request->text,
                             'is_edited' => true
                         ]);


        File::syncFile($request->file_id, $message);

        return api(MessageResource::make($message));


    }


}
