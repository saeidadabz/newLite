<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Http\Resources\CalendarResource;
use App\Http\Resources\JobResource;
use App\Http\Resources\RoomListResource;
use App\Http\Resources\TagResource;
use App\Http\Resources\UserMinimalResource;
use App\Http\Resources\WorkspaceResource;
use App\Models\Role;
use App\Models\User;
use App\Models\Workspace;
use App\Notifications\WorkspaceCreatedNotification;
use App\Notifications\WorkspaceJoinedNotification;
use App\Utilities\Constants;
use Illuminate\Http\Request;

class WorkspaceController extends Controller {
    public function all() {
        $user = auth()->user();

        return api(WorkspaceResource::collection($user->workspaces));
    }

    public function rooms(Workspace $workspace) {
        return api(RoomListResource::collection($workspace->rooms));
    }

    public function tags(Workspace $workspace) {

        return api(TagResource::collection($workspace->tags));
    }

    public function jobs(Workspace $workspace) {

        return api(JobResource::collection($workspace->jobs));
    }

    public function users(Workspace $workspace) {
        return api(UserMinimalResource::collection($workspace->users));
    }

    public function calendars(Workspace $workspace) {
        return api(CalendarResource::make($workspace->calendars));
    }

    public function get(Workspace $workspace) {
        if (auth()->user()->tokenCan(Permission::WS_GET->value . '-' . $workspace->id)) {
            return api(WorkspaceResource::make($workspace));

        }
        return error('Permission Denied');
    }


    public function create(Request $request) {
        $request->validate(['title' => 'required']);
        /** @var User $user */
        $user = auth()->user();

        $workspace = Workspace::create($request->all());
        $workspace->joinUser($user, 'super-admin');


        $workspace->rooms()->create([
                                        'title'   => 'general',
                                        'user_id' => $user->id
                                    ]);


        $user->notify(new WorkspaceCreatedNotification($workspace));

        return api(WorkspaceResource::make($workspace));
    }

    public function update(Workspace $workspace, Request $request) {

        //TODO: has to check with sanctum permissions
        $workspace->update($request->all());

        sendSocket(Constants::workspaceUpdated, $workspace->channel, $workspace);

        return api(WorkspaceResource::make($workspace));

    }

    public function addRole(Workspace $workspace, Request $request) {

        $request->validate([
                               'role'    => 'required',
                               'user_id' => 'required',
                           ]);

        $user = auth()->user();
        if ($user->isSuperAdmin($workspace)) {

            $role = Role::findOrFail($request->role_id);
            $wsUser = User::findOrFail($request->user_id);

            $wsUser->giveRole($role, $workspace->id);


        }


        return api(WorkspaceResource::make($workspace));

    }

    public function addTag(Workspace $workspace, Request $request) {
        $request->validate([
                               'tag'     => 'required',
                               'user_id' => 'required',
                           ]);

        $wsUser = User::find($request->user_id);
        $workspace->users()->updateExistingPivot($wsUser, ['tag' => $request->role]);

        return api(WorkspaceResource::make($workspace));
    }


    public function leaderboard(Workspace $workspace) {


        $users = $workspace->users;
        $d = [];
        foreach ($users as $user) {
            $d[] = collect($user->getTime('currentMonth', NULL, NULL, FALSE, $workspace->id));
        }
        return api(collect($d)->sortByDesc('sum_minutes')->values()->toArray());

    }

    public function join(Workspace $workspace) {
        /** @var User $user */
        $user = auth()->user();
        $workspace->joinUser($user);

        $user->notify(new WorkspaceJoinedNotification($workspace));

        return api(TRUE);
    }
}
