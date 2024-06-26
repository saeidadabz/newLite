<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomListResource;
use App\Http\Resources\UserMinimalResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Workspace;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function all()
    {
        $user = auth()->user();


        return api(WorkspaceResource::collection($user->workspaces));
    }


    public function rooms(Workspace $workspace)
    {

        return api(RoomListResource::collection($workspace->rooms));
    }


    public function users(Workspace $workspace)
    {
        return api(UserMinimalResource::collection($workspace->users));

    }

    public function get(Workspace $workspace)
    {

        if (auth()->user()->can('get-' . $workspace->id)) {
            return api(WorkspaceResource::make($workspace));

        }
        dd('Cant');


    }


    public function create(Request $request)
    {

        $request->validate(['title' => 'required']);
        $user = auth()->user();

        $workspace = Workspace::create($request->all());
        $workspace->joinUser($user, 'owner');


        return api(WorkspaceResource::make($workspace));

    }


    public function update(Workspace $workspace, Request $request)
    {

        //TODO: has to check with sanctum permissions
        $workspace->update($request->all());

        sendSocket('workspaceUpdated', $workspace->channel, $workspace);


        return api(WorkspaceResource::make($workspace));


    }


    public function join(Workspace $workspace)
    {
        $user = auth()->user();

//        $user->workspaces()->attach($workspace->id, ['role_id' => 1]);

        $workspace->joinUser($user);

//        $currentToken = $user->currentAccessToken();
//        $abilities = $user->currentAccessToken()->abilities;
//        $abilities[] = 'get-' . $workspace->id;
//        $currentToken->abilities = $abilities;
//        $currentToken->save();

        return api(TRUE);


    }
}
