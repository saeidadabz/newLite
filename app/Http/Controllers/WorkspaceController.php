<?php

namespace App\Http\Controllers;

use App\Http\Resources\WorkspaceResource;
use App\Models\Workspace;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function all()
    {
        $user = auth()->user();


        return api(WorkspaceResource::collection($user->workspace));
    }


    public function join(Workspace $workspace)
    {
        $user = auth()->user();

        $user->workspaces()->attach($workspace->id, ['role_id', 1]);

        return api(true);


    }
}
