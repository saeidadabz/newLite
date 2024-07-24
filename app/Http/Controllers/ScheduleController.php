<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Http\Requests\ScheduleRequest;
use App\Http\Resources\ScheduleResource;
use App\Jobs\RecurSchedule;
use App\Models\Calendar;
use App\Models\Schedule;
use App\Models\User;
use App\Params\RecurParam;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{


    public function index(Request $request)
    {
        $user = $request->user();



        $schedules = $user->schedules;


        $res = ScheduleResource::collection($schedules);

        return api($res);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(ScheduleRequest $request)
    {
        $data = $request->validated();
        $cal = Calendar::findOrFail($data['calendar_id']);

        /** @var User $user */
        $user = $request->user();
        $hasPerm = $user->isOwner($cal->owner_id) || $user->tokenCan(Permission::SCHEDULE_CREATE->value);
        abort_if(! $hasPerm, Response::HTTP_FORBIDDEN);

        $data['owner_id'] = $cal->owner_id;
        $sch = Schedule::create($data);
        if (isset($data['recurrence_pattern'])) {
            $param = new RecurParam($data['recurrence_pattern'], $data['recurrence_end_date'], $data['recurrence_days'] ?? []);
            dispatch(new RecurSchedule($sch, $param));
        }

        return api();
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Schedule $schedule)
    {
        /** @var User $user */
        $user = $request->user();
        $hasPerm = $user->isOwner($schedule->owner_id) || $user->tokenCan(Permission::SCHEDULE_VIEW->value);
        abort_if(! $hasPerm, Response::HTTP_FORBIDDEN);

        /** @var Calendar $cal */
        $cal = $schedule->calendar;

        $hasPerm = $cal->canUserAccess($request->user());
        throw_if(! $hasPerm, new AuthorizationException());

        if ($expand = $request->get('expand')) {
            $schedule->loadExpands($expand);
        }

        $res = ScheduleResource::make($schedule);

        return api($res);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ScheduleRequest $request, Schedule $schedule)
    {
        /** @var User $user */
        $user = $request->user();
        $hasPerm = $user->isOwner($schedule->owner_id) || $user->tokenCan(Permission::SCHEDULE_UPDATE->value);
        abort_if(! $hasPerm, Response::HTTP_FORBIDDEN);

        if (! $schedule->update($request->validated())) {
            Log::error('Schedule Controller: Could not delete schedule '.$schedule->id);

            return api_gateway_error();
        }

        $schedule->fresh();
        $res = ScheduleResource::make($schedule);

        return api($res);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Schedule $schedule)
    {
        /** @var User $user */
        $user = $request->user();
        $hasPerm = $user->isOwner($schedule->owner_id) || $user->tokenCan(Permission::SCHEDULE_DELETE->value);
        abort_if(! $hasPerm, Response::HTTP_FORBIDDEN);

        if (! $schedule->delete()) {
            Log::error('Schedule Controller: Could not delete schedule '.$schedule->id);

            return api_gateway_error();
        }

        return api();
    }
}
