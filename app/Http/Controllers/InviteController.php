<?php

namespace App\Http\Controllers;

use App\Http\Resources\InviteResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Invite;
use App\Models\Room;
use App\Models\Workspace;
use Illuminate\Http\Request;

class InviteController extends Controller
{
    public function invite(Request $request)
    {
        $user = auth()->user();

        $invite = Invite::create([
                                     'owner_id'     => $user->id,
                                     'user_id'      => $request->user_id,
                                     'workspace_id' => $request->workspace_id,
                                     'room_id'      => $request->room_id,
                                     'status'       => 'pending'

                                 ]);


        return api(InviteResource::make($invite));


    }


    public function get($code)
    {
        $invite = Invite::findByCode($code);

        return api(InviteResource::make($invite));


    }

    public function join($code)
    {
        $invite = Invite::findByCode($code);
        $user = auth()->user();
        if ($invite->status !== 'pending' || $invite->user_id !== $user->id) {
            return error('Invite code expired');
        }


        if ($invite->workspace_id !== NULL) {
            $invite->user->workspaces()->attach($invite->workspace_id, ['role' => 'member']);
            $invite->status = 'joined';
            $invite->save();

            return api(WorkspaceResource::make($invite->workspace));

            //TODO:Join to workspace
            // Socket, user joined to ws.
        }
        if ($invite->room_id !== NULL) {
            //TODO:Join to workspace
        }


    }
}
