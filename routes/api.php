<?php

use App\Http\Controllers\MmdbDownloadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/mmdb/download/{type}', function (Request $request) {
        $controller = new MmdbDownloadController;
        $type = $request->route('type');

        try {
            return $controller->downloadFile($request, $type);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    })->name('mmdb.download')
        ->where('type', 'asn|city|country');

    Route::get('/mmdb/last-modified', function () {
        $files = ['asn', 'country', 'city'];
        $lastModified = [];

        foreach ($files as $file) {
            $path = "mmdb/{$file}.tar.gz";
            if (Storage::exists($path)) {
                $lastModified[$file] = date('Y-m-d H:i:s', Storage::lastModified($path));
            } else {
                $lastModified[$file] = null;
            }
        }

        return response()->json($lastModified);
    });

    Route::post('/mmdb/update', function () {
        Artisan::call('app:pull:mmdb');

        return response()->json(['message' => 'Databases updated successfully']);
    });
});
