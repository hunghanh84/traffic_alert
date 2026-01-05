<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-server', function () {
    return response()->json(['status' => 'ok']);
});

use App\Http\Controllers\Api\LocationController;

Route::prefix('locations')->group(function () {
    Route::get('/wards', [LocationController::class, 'getWards']);
    Route::get('/streets', [LocationController::class, 'getStreets']);
    Route::get('/streets/ward/{wardId}', [LocationController::class, 'getStreetsByWard']);
    Route::get('/data', [LocationController::class, 'getLocationData']);
});
