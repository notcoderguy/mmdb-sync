<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MmdbDownloadController;
use App\Http\Controllers\ApiKeyController;

Route::get('/mmdb/download/{type}', function (Request $request) {
    $controller = new MmdbDownloadController;
    $type = $request->route('type');

    try {
        $controller->downloadFile($request, $type);
        return response()->json(['message' => 'File downloaded successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('api-keys', ApiKeyController::class);
});
