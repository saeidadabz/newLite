<?php

namespace App\Http\Controllers;

use App\Enums\NotificationStatus;
use App\Http\Requests\NotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Jobs\ReadNotification;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:notifications')->only('store');
    }

    public function index(Request $request): \Illuminate\Contracts\Foundation\Application|ResponseFactory|Application|Response
    {
        $data = $request->all();
        $query = Notification::query();
        if (isset($data['status'])) {
            $query->where('status', $data['status']);
        }
        if (isset($data['read'])) {
            $query = $data['read'] ? $query->whereNotNull('read_at') : $query->whereNull('read_at');
        }
        // TODO - Admin should see all
        $query->where('notifiable_type', User::class)->where('notifiable_id', $request->user()->id);

        $res = NotificationResource::make($query->paginate());

        return api($res);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param NotificationRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|ResponseFactory|Application|Response
     */
    public function store(NotificationRequest $request): \Illuminate\Contracts\Foundation\Application|ResponseFactory|Application|Response
    {
        $data = $request->validated();
        $notification = Notification::create([
            ...$data,
            'status'   => isset($data['sends_at']) && $data['sends_at']->greaterThan(now()) ? NotificationStatus::Scheduled->name : NotificationStatus::Pending->name,
            'sends_at' => $data['sends_at'] ?? null,
        ]);
        $res = NotificationResource::make($notification);

        return api($res);
    }

    /**
     * @param NotificationRequest $request
     * @param Notification $notification
     * @return Application|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
     */
    public function update(NotificationRequest $request, Notification $notification): Application|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
    {
        if (! $notification->isScheduled()) {
            return api()->validation(null, [
                'notification' => __('notification.not_scheduled')
            ]);
        }

        $data = $request->validated();
        if (! $notification->update($data)) {
            Log::error('Notification Controller: Could not update '.$notification->id);

            return api_gateway_error();
        }

        return api()->success(null, [
            'item' => Notification::find($notification->id),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Notification $notification
     * @return \Illuminate\Contracts\Foundation\Application|ResponseFactory|Application|Response
     */
    public function show(Notification $notification): Application|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
    {
        dispatch(new ReadNotification($notification));

        $res = NotificationResource::make($notification);

        return api($res);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Notification $notification
     * @return \Illuminate\Contracts\Foundation\Application|ResponseFactory|Application|Response
     */
    public function destroy(Notification $notification): Application|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
    {
        if (! $notification->delete()) {
            Log::error('Notification Controller: Could not delete '.$notification->id);

            return api_gateway_error();
        }

        return api();
    }
}
