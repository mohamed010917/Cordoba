<?php

use App\Http\Controllers\Api\RoomsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('rooms', RoomsController::class)->only(['index']);
});
