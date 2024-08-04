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
    public function create(ScheduleRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();


        $schedule = $user->schedules()->create([
            'availability_type' => json_encode($data['availability_type']),
            'days' => json_encode($data['availability_type']),
            'start_time' => $request->start_time ?? '08:00:00',
            'end_time' => $request->end_time ?? '18:00:00',
            'is_recurrence' => $request->is_recurrence,
            'recurrence_start_at' => $request->recurrence_start_at ?? now(),
            'recurrence_end_at' => $request->recurrence_end_at,
            'timezone' => $request->timezone ?? 'Asia/Tehran',
        ]);


        return api(ScheduleResource::make($schedule));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Schedule $schedule)
    {
        /** @var User $user */
        $user = $request->user();
        $hasPerm = $user->isOwner($schedule->owner_id) || $user->tokenCan(Permission::SCHEDULE_VIEW->value);
        abort_if(!$hasPerm, Response::HTTP_FORBIDDEN);

        /** @var Calendar $cal */
        $cal = $schedule->calendar;

        $hasPerm = $cal->canUserAccess($request->user());
        throw_if(!$hasPerm, new AuthorizationException());

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
        abort_if(!$hasPerm, Response::HTTP_FORBIDDEN);

        if (!$schedule->update($request->validated())) {
            Log::error('Schedule Controller: Could not delete schedule ' . $schedule->id);

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
        abort_if(!$hasPerm, Response::HTTP_FORBIDDEN);

        if (!$schedule->delete()) {
            Log::error('Schedule Controller: Could not delete schedule ' . $schedule->id);

            return api_gateway_error();
        }

        return api();
    }
}
