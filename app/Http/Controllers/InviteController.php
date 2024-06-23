<?php

namespace App\Http\Controllers;

use App\Http\Resources\InviteResource;
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


    public function join($code)
    {
        $invite = Invite::findByCode($code);

        if ($invite->workspace_id !== NULL) {
            $invite->user->workspaces()->attach($invite->workspace_id, ['role' => 'member']);

            //TODO:Join to workspace
        }
        if ($invite->room_id !== NULL) {
            //TODO:Join to workspace
        }

        return api(InviteResource::make($invite));


    }
}
