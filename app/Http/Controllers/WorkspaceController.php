<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomListResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Role;
use App\Models\Workspace;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function all()
    {
        $user = auth()->user();


        return api(WorkspaceResource::collection($user->workspaces));
    }

    public function rooms($workspace)
    {
        $user = auth()->user();
        $workspace = $user->workspaces()->find($workspace);
        if ($workspace === null) {
            return error('You have no access to this workspace');

        }

        return api(RoomListResource::collection($workspace->rooms));
    }

    public function get($workspace)
    {
        $user = auth()->user();
        $workspace = $user->workspaces()->find($workspace);
        if ($workspace === null) {
            return error('You have no access to this workspace');

        }
        return api(WorkspaceResource::make($workspace));

    }


    public function create(Request $request)
    {

        $request->validate(['title' => 'required']);
        $user = auth()->user();

        $workspace = Workspace::create($request->all());

        $user->workspaces()->attach($workspace->id, ['role_id', 2]);

    }


    public function update($workspace, Request $request)
    {
        $user = auth()->user();
        $workspace = $user->workspaces()->find($workspace);
        if ($workspace === null) {
            return error('You have no access to this workspace');
        }
        $role = Role::find($workspace->pivot->role_id);
        if ($role && $role->title === 'owner') {
            $workspace->update($request->all());
        }
        //TODO: has to check with sanctum permissions

        return api(WorkspaceResource::make($workspace));


    }


    public function join(Workspace $workspace)
    {
        $user = auth()->user();

        $user->workspaces()->attach($workspace->id, ['role_id', 1]);

        return api(true);


    }
}
