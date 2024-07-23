<?php

use App\Utilities\Constants;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('https://lite.cotopia.social');
});


Route::get('/tester', function () {
    dd(now()->timezone('Asia/Tehran'));

    $user = \App\Models\User::find(3);
    $acts = $user->activities();

    if (TRUE) {

//        $acts = $acts->where('created_at', '>=', today());


    }
    $acts = $acts->whereIn('event_type', [Constants::JOINED, Constants::LEFT])
                 ->where('created_at', '>=', today()->subDay())->where('created_at', '<=', today());
    $sum = 0;
    $acts = $acts->get();
    foreach ($acts as $act) {
        $start_time = $act->created_at;
        if ($act->event_type === Constants::JOINED) {
            $left = $acts->where('event_type', Constants::LEFT)
                         ->where('created_at', '>=', $start_time)
                         ->first();
            $end_time = now();

            if ($left !== NULL) {
                $end_time = $left->created_at;

            }
            $sum += $start_time->diffInMinutes($end_time);
        }
    }
    return [
        'activities' => $acts,
        'sum'        => $sum,
    ];
//    if ($request->yesterday) {
//
//        $acts = $acts->where('created_at', '>=', today()->subDay())->where('created_at', '<=', today());
//
//
//    }
//
//    if ($request->currentMonth) {
//
//        $acts = $acts->where('created_at', '>=', now()->firstOfMonth());
//
//
//    }


    return api($sum);
});
Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);


Route::get('/acts', function () {

    $request = request();
    if ($request->user_id === NULL) {
        $users = \App\Models\User::all();
        $d = [];
        foreach ($users as $user) {
            $acts = $user->activities()->whereIn('event_type', [Constants::JOINED, Constants::LEFT]);

            if ($request->today) {

                $acts = $acts->where('created_at', '>=', today());


            }


            if ($request->yesterday) {

                $acts = $acts->where('created_at', '>=', today()->subDay())->where('created_at', '<=', today());


            }

            if ($request->currentMonth) {

                $acts = $acts->where('created_at', '>=', now()->firstOfMonth());


            }
            $sum_minutes = 0;
            $data = [];
            $acts = $acts->get();
            foreach ($acts as $act) {
                if ($act->event_type === Constants::JOINED) {
                    $start_time = $act->created_at;

                    $left = $acts->where('event_type', Constants::LEFT)
                                 ->where('created_at', '>=', $start_time)
                                 ->first();
                    $end_time = now();

                    if ($left !== NULL) {
                        $end_time = $left->created_at;

                    }
                    $data[] = 'Joined: ' . $start_time->timezone('Asia/Tehran')
                                                      ->toDateTimeString() . ' Left: ' . $end_time->timezone('Asia/Tehran')
                                                                                                  ->toDateTimeString() . ' Diff: ' . $start_time->diffInMinutes($end_time);
                    $sum_minutes += $start_time->diffInMinutes($end_time);
                }
            }
            $d[] = [
                'user'        => $user,
                'count'       => $acts->count(),
                'sum_minutes' => $sum_minutes,
                'sum_hours'   => \Carbon\CarbonInterval::minutes($sum_minutes)->cascade()->forHumans(),
                'data'        => $data,
                'activities'  => $acts->map(function ($act) {
                    return [
                        'id'         => $act->id,
                        'type'       => $act->event_type,
                        'created_at' => $act->created_at->timezone('Asia/Tehran'),
                    ];
                }),
            ];
        }
        return $d;
    }
    $user = \App\Models\User::find($request->user_id);
    $acts = $user->activities()->whereIn('event_type', [Constants::JOINED, Constants::LEFT]);

    if ($request->today) {

        $acts = $acts->where('created_at', '>=', today());


    }


    if ($request->yesterday) {

        $acts = $acts->where('created_at', '>=', today()->subDay())->where('created_at', '<=', today());


    }

    if ($request->currentMonth) {

        $acts = $acts->where('created_at', '>=', now()->firstOfMonth());


    }
    $sum_minutes = 0;
    $data = [];
    $acts = $acts->get();
    foreach ($acts as $act) {
        if ($act->event_type === Constants::JOINED) {
            $start_time = $act->created_at;

            $left = $acts->where('event_type', Constants::LEFT)
                         ->where('created_at', '>=', $start_time)
                         ->first();
            $end_time = now();

            if ($left !== NULL) {
                $end_time = $left->created_at;

            }
            $data[] = 'Joined: ' . $start_time->timezone('Asia/Tehran')
                                              ->toDateTimeString() . ' Left: ' . $end_time->timezone('Asia/Tehran')
                                                                                          ->toDateTimeString() . ' Diff: ' . $start_time->diffInMinutes($end_time);
            $sum_minutes += $start_time->diffInMinutes($end_time);
        }
    }
    return [
        'count'       => $acts->count(),
        'sum_minutes' => $sum_minutes,
        'sum_hours'   => \Carbon\CarbonInterval::minutes($sum_minutes)->cascade()->forHumans(),
        'data'        => $data,
        'activities'  => $acts->map(function ($act) {
            return [
                'id'         => $act->id,
                'type'       => $act->event_type,
                'created_at' => $act->created_at->timezone('Asia/Tehran'),
            ];
        }),
    ];
//    if ($request->yesterday) {
//
//        $acts = $acts->where('created_at', '>=', today()->subDay())->where('created_at', '<=', today());
//
//
//    }
//
//    if ($request->currentMonth) {
//
//        $acts = $acts->where('created_at', '>=', now()->firstOfMonth());
//
//
//    }


    return api($sum);
});
