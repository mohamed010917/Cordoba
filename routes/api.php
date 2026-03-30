<?php

use App\Http\Controllers\Api\ReservationsController;
use App\Http\Controllers\Api\RoomsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($request->only('email', 'password'))) {
        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    return response()->json(['error' => 'Unauthorized'], 401);
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('rooms', RoomsController::class)
        ->only(['index'])
        ->names(['index' => 'api.rooms.index']);

    Route::apiResource('reservations', ReservationsController::class)
        ->only(['index'])
        ->names(['index' => 'api.reservations.index']);
});
