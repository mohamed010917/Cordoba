<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorereservationRequest;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Stripe\Charge;
use Stripe\Stripe;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with('room')
            ->where('client_id', auth()->id())
            ->latest()
            ->get();

        return Inertia::render('reservations/Index', [
            'reservations' => $reservations,
        ]);
    }

    public function create(Room $room)
    {
        return Inertia::render('reservations/Create', [
            'room' => $room,
        ]);
    }

    public function store(StorereservationRequest $request)
    {
        $room = Room::findOrFail($request->room_id);

        DB::beginTransaction();
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $charge = Charge::create([
                'amount' => $room->price_cents,
                'currency' => 'usd',
                'source' => $request->stripe_token,
                'description' => "Reservation for Room #{$room->number}",
            ]);

            $reservation = Reservation::create([
                'client_id' => auth()->id(),
                'room_id' => $room->id,
                'accompany_number' => $request->accompany_number,
                'paid_price_cents' => $room->price_cents,
            ]);

            Payment::create([
                'reservation_id' => $reservation->id,
                'amount_cents' => $room->price_cents,
                'payment_method' => 'stripe',
                'status' => 'succeeded',
                'transaction_id' => $charge->id,
            ]);

            DB::commit();

            return redirect()->route('reservations.index')->with('success', 'Reservation successful!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['payment' => $e->getMessage()]);
        }
    }

    public function destroy(Reservation $reservation)
    {
        if ($reservation->client_id !== auth()->id()) {
            abort(403);
        }

        $reservation->delete();

        return back()->with('success', 'Reservation cancelled successfully.');
    }
}
