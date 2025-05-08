<?php

use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\MmdbDownloadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/mmdb/download/{type}', function (Request $request) {
    $controller = new MmdbDownloadController;
    $type = $request->route('type');

    try {
        return $controller->downloadFile($request, $type);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
