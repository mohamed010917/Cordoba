<?php

use App\Http\Controllers\Api\ReservationsController;
use App\Http\Controllers\Api\RoomsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
//--
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Nnjeim\World\Models\Country;
use Nnjeim\World\Models\City;


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

//--

    Route::post('/payment/create-intent', function (Request $request) {
        Stripe::setApiKey(config('services.stripe.secret'));

        $request->validate([
            'amount' => 'required|integer|min:1',
        ]);

        $intent = PaymentIntent::create([
            'amount' => $request->amount,
            'currency' => 'usd',
            'automatic_payment_methods' => [
                'enabled' => true,
                'allow_redirects' => 'never',
                ],
            
        ]);

        return response()->json([
            'client_secret' => $intent->client_secret,
        ]);
    });

    Route::post('/payment/confirm', function (Request $request) {
        Stripe::setApiKey(config('services.stripe.secret'));

        $request->validate([
            'payment_intent_id' => 'required|string',
        ]);

        $intent = PaymentIntent::retrieve($request->payment_intent_id);

        if ($intent->status === 'succeeded') {
            return response()->json(['message' => 'Payment successful']);
        }

        return response()->json(['error' => 'Payment not completed'], 400);
    });
});

Route::get('/countries', function () {
    return Country::select('id', 'name')->get();
});

Route::get('/cities/{country_id}', function ($country_id) {
    return City::where('country_id', $country_id)
        ->select('id', 'name')
        ->get();
});