<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityResource;
use App\Http\Resources\JobResource;
use App\Http\Resources\RoomResource;
use App\Http\Resources\ScheduleResource;
use App\Http\Resources\UserMinimalResource;
use App\Http\Resources\UserResource;
use App\Models\File;
use App\Models\Room;
use App\Models\User;
use App\Models\Workspace;
use App\Utilities\Constants;
use Illuminate\Http\Request;

class UserController extends Controller {
    public function me() {

        return api(UserResource::make(auth()->user()));
    }

    public function jobs() {
        $user = auth()->user();

        return api(JobResource::collection($user->jobs()));
    }


    public function workspaces() {
        $user = auth()->user();

        return api(JobResource::collection($user->workspaces()));
    }

    public function search(Request $request) {
        //TODO: have to use meiliserach instead
        $search = $request->search;
        $users = User::where(function ($query) use ($search) {
            $query->where('name', 'LIKE', $search . '%')->orWhere('username', 'LIKE', $search . '%')->orWhere('email', 'LIKE', $search . '%');
        })->get();
        return api(UserMinimalResource::collection($users));
    }

    public function updateCoordinates(Request $request) {

      

        $user = auth()->user();
        $request->validate([
                               'coordinates' => 'required'
                           ]);

        $user->update([
                          'coordinates' => $request->coordinates
                      ]);

    
        $user = auth()->user();
        
        $XY=explode(',',$user->coordinates);
        $X=$XY[0];
        $Y=$XY[1];

        $users=User::where('id','!=',$user->id)->get();
        $userscoordiantes=[];
        foreach ($users as $usr)
        {
            $userscoordiantes[$usr->id]=$usr->coordinates;

             
        }
    
        $distances=[];
        foreach($userscoordiantes as $key=>$usercoordinates)
        {
            $xy=explode(',',$usercoordinates);
            $x=$xy[0];
            $y=$xy[1];
            $distances[$key]  = abs(pow(($x-$X),2)- pow(($y-$Y),2));

        }
       
        $closestdistance=min($distances);
        $closestUserId=array_search($closestdistance, $distances);
        //dd($closestUserId);

        $closestUser=User::where('id','=',$closestUserId)->get();
        dd($closestUser);
        
            



       /* $closestUser = User::where('id', '!=', $user->id)
        ->orderByRaw('
            (POW((CAST(SUBSTRING_INDEX(coordinates, ",", 1) AS DECIMAL) - ?), 2) + 
            POW((CAST(SUBSTRING_INDEX(coordinates, ",", -1) AS DECIMAL) - ?), 2)) ASC', 
            [$x, $y])
        ->first(); */
        

        //??????????????


        $response = UserMinimalResource::make($user);

        if ($user->room !== NULL) {
            sendSocket(Constants::userUpdated, $user->room->channel, $response);

        }
        return api($response);

    }

        public function updateVideoCoordinates(Request $request)
        {
        $user = auth()->user();
        $request->validate([
        'video_coordinates' => 'required'
        ]);
        
        $user->update([
        'video_coordinates' => $request->video_coordinates
        ]);
        
        $response = UserMinimalResource::make($user);
        
        if ($user->room !== NULL) {
        sendSocket(Constants::userUpdated, $user->room->channel, $response);
        
        }
        return api($response);
        
        }
        public function updateScreenshareCoordinates(Request $request)
        {
        $user = auth()->user();
        $request->validate([
        'screenshare_coordinates' => 'required'
        ]);
        
        $user->update([
        'screenshare_coordinates' => $request->screenshare_coordinates
        ]);
        
        $response = UserMinimalResource::make($user);
        
        if ($user->room !== NULL) {
        sendSocket(Constants::userUpdated, $user->room->channel, $response);
        
        }
        return api($response);
        
        }
        

    public function toggleMegaphone() {
        $user = auth()->user();


        $user->update([
                          'is_megaphone' => !$user->is_megaphone
                      ]);

        $response = UserMinimalResource::make($user);
        if ($user->room !== NULL) {
            sendSocket(Constants::userUpdated, $user->room->channel, $response);

        }

        return api($response);

    }


    public function update(Request $request) {
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

    public function activities(Request $request) {

        return api(auth()->user()->getTime($request->period)['sum_minutes']);
    }

    public function directs() {
        return api(RoomResource::collection(auth()->user()->directs()));
    }


    public function schedules() {
        return api(ScheduleResource::collection(auth()->user()->schedules));
    }

}
