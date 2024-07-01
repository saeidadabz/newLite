<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PostmanExportController extends Controller
{
    /**
     * Export postman collection of routes
     *
     * @param Request $request
     * @return BinaryFileResponse
     * @group General
     */
    public function __invoke(Request $request) : BinaryFileResponse
    {
        if ($request->has('base_url')) {
            config([
                'api-postman.base_url' => $request->input('base_url')
            ]);
        }

        Artisan::call('export:postman');
        $collection = collect(Storage::disk('local')->files('postman'))->last();

        return response()->download(storage_path('app/'.$collection));
    }
}
