<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('https://lite.cotopia.social');
});


Route::get('/tester', function () {

    $re = [
        'event'       => 'track_published',
        'room'        => [
            'sid'  => 'RM_xZgVzDDtz7fj',
            'name' => 'yourroom',
        ],
        'participant' => [
            'sid'      => 'PA_b5NK9MzWSG2Z',
            'identity' => 'publisher',
        ],
        'track'       => [
            'sid'       => 'TR_VCNoJfCRhTs5GT',
            'type'      => 'VIDEO',
            'name'      => 'demo',
            'width'     => 1280,
            'height'    => 720,
            'simulcast' => TRUE,
            'source'    => 'CAMERA',
            'layers'    =>
                [
                    0 =>
                        [
                            'width'   => 320,
                            'height'  => 180,
                            'bitrate' => 120000,
                        ],
                    1 =>
                        [
                            'quality' => 'MEDIUM',
                            'width'   => 640,
                            'height'  => 360,
                            'bitrate' => 400000,
                        ],
                    2 =>
                        [
                            'quality' => 'HIGH',
                            'width'   => 1280,
                            'height'  => 720,
                            'bitrate' => 1500000,
                            'ssrc'    => 2149810279,
                        ],
                ],
            'mimeType'  => 'video/H264',
            'mid'       => '0',
            'codecs'    =>
                [
                    0 =>
                        [
                            'mimeType' => 'video/H264',
                            'mid'      => '0',
                            'cid'      => 'demo-video',
                            'layers'   =>
                                [
                                    0 =>
                                        [
                                            'width'   => 320,
                                            'height'  => 180,
                                            'bitrate' => 120000,
                                        ],
                                    1 =>
                                        [
                                            'quality' => 'MEDIUM',
                                            'width'   => 640,
                                            'height'  => 360,
                                            'bitrate' => 400000,
                                        ],
                                    2 =>
                                        [
                                            'quality' => 'HIGH',
                                            'width'   => 1280,
                                            'height'  => 720,
                                            'bitrate' => 1500000,
                                            'ssrc'    => 2149810279,
                                        ],
                                ],
                        ],
                ],
            'stream'    => 'camera',
            'version'   =>
                [
                    'unixMicro' => '1720257776589891',
                ],
        ],
        'id'          => 'EV_7NBnpajBViEN',
        'createdAt'   => '1720257776',
    ];


    $event = new \App\Utilities\EventType($re);
    dd($event);
    $text = 'salam khobi @katerou22 chekhabar , khobi aghaye @habibi';


    dd(\Illuminate\Support\Str::before(\Illuminate\Support\Str::after($text, '@'), ' '));
});
Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
