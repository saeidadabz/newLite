<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduleRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\Calendar;
use App\Models\Schedule;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(ScheduleRequest $request)
    {
        $data = $request->validated();
        $cal = Calendar::findOrFail($data['calendar_id']);

        $hasPerm = $request->user()->id !== $cal->owner_id;
        abort_if($hasPerm, Response::HTTP_FORBIDDEN);

        $data['owner_id'] = $cal->owner_id;
        $res = ScheduleResource::make(
            Schedule::create($data)
        );

        return api($res);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Schedule $schedule)
    {
        /** @var Calendar $cal */
        $cal = $schedule->calendar;

        $hasPerm = $cal->canUserAccess($request->user());
        throw_if(! $hasPerm, new AuthorizationException);

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
        if (! $schedule->update($request->validated())) {
            Log::error("Schedule Controller: Could not delete schedule ".$schedule->id);

            return api_gateway_error();
        }

        $schedule->fresh();
        $res = ScheduleResource::make($schedule);

        return api($res);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        if (! $schedule->delete()) {
            Log::error("Schedule Controller: Could not delete schedule ".$schedule->id);

            return api_gateway_error();
        }

        return api();
    }
}
