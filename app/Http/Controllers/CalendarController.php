<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalendarRequest;
use App\Http\Resources\CalendarResource;
use App\Models\Calendar;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Calendar::query()->whereOwnerId($request->user()->id);
        $res = CalendarResource::make(
            $query->get(),
        );

        return api($res);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CalendarRequest $request)
    {
        $data = $request->validated();
        $data['owner_id'] = $request->user()->id;
        $cal = Calendar::create($data);
        $res = CalendarResource::make($cal);

        return api(data: $res, http_code: Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Calendar $calendar)
    {
        $res = CalendarResource::make($calendar);

        return api($res);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CalendarRequest $request, Calendar $calendar)
    {
        if (! $calendar->update($request->validated())) {
            Log::error("Calendar Controller: Could not update calendar ".$calendar->id);

            return api_gateway_error();
        }

        $calendar->fresh();
        $res = CalendarResource::make($calendar);

        return api($res);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Calendar $calendar)
    {
        if (! $calendar->delete()) {
            Log::error("Calendar Controller: Could not delete calendar ".$calendar->id);

            return api_gateway_error();
        }

        return api();
    }
}
