<?php

namespace App\Http\Controllers;

use App\Http\Resources\JobResource;
use App\Http\Resources\RoomResource;
use App\Http\Resources\UserMinimalResource;
use App\Http\Resources\UserResource;
use App\Models\Room;
use App\Models\User;
use App\Models\Workspace;
use App\Utilities\Constants;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function me()
    {

        return api(UserResource::make(auth()->user()));
    }

    public function jobs()
    {
        $user = auth()->user();

        return api(JobResource::collection($user->jobs()));

    }

    public function search(Request $request)
    {

        //TODO: have to use meiliserach instead
        $search = $request->search;
        $users = User::where(function ($query) use ($search) {
            $query->where('name', 'LIKE', $search . '%')
                  ->orWhere('username', 'LIKE', $search . '%')
                  ->orWhere('email', 'LIKE', $search . '%');
        })->get();
        return api(UserMinimalResource::collection($users));
    }

    public function updateCoordinates(Request $request)
    {
        $user = auth()->user();
        $request->validate([
                               'coordinates' => 'required'
                           ]);

        $user->update([
                          'coordinates' => $request->coordinates
                      ]);

        $response = UserMinimalResource::make($user);
        sendSocket(Constants::userUpdated, $user->room->channel, $response);

        return api($response);

    }

    public function toggleMegaphone()
    {
        $user = auth()->user();


        $user->update([
                          'is_megaphone' => !$user->is_megaphone
                      ]);

        $response = UserMinimalResource::make($user);
        sendSocket(Constants::userUpdated, $user->room->channel, $response);

        return api($response);

    }


    public function directs()
    {
        return api(RoomResource::collection(auth()->user()->directs()));
    }
}
