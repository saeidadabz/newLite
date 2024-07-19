<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityResource;
use App\Http\Resources\JobResource;
use App\Http\Resources\RoomResource;
use App\Http\Resources\UserMinimalResource;
use App\Http\Resources\UserResource;
use App\Models\File;
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


    public function update(Request $request)
    {
        $user = auth()->user();
        $user->update([
            'name' => $request->name ?? $user->name,
        ]);

        File::syncFile($request->avatar_id, $user, 'avatar');
        $response = UserMinimalResource::make($user);

        if ($user->room !== NULL) {
            sendSocket(Constants::userUpdated, $user->room->channel, $response);

        }

        return api($response);
    }

    public function activities(Request $request)
    {
        $user = auth()->user();

        $acts = $user->activities();

        if ($request->today) {

            $acts = $acts->where('created_at', '>=', today());


        }
        if ($request->thismonth) {

            $acts = $acts->where('created_at', '>=', now()->firstOfMonth());


        }


        $sum = 0;
        $acts = $acts->get();
        foreach ($acts as $act) {
            $start_time = $act->created_at;
            if ($act->event_type === Constants::JOINED) {
                $left = $acts->where('event_type', Constants::LEFT)
                    ->where('created_at', '>=', $start_time)
                    ->first();
                $end_time = now();

                if ($left !== null) {
                    $end_time = $left->created_at;

                }
                $sum += $start_time->diffInMinutes($end_time);
            }
        }
        return api($sum);
    }

    public function directs()
    {
        return api(RoomResource::collection(auth()->user()->directs()));
    }
}
