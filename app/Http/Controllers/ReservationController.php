<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorereservationRequest;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Stripe\Charge;
use Stripe\Stripe;

class ReservationController extends Controller
{
    public function index(): Response
    {
        $reservations = Reservation::with('room')
            ->where('client_id', auth()->id())
            ->latest()
            ->get();

        return Inertia::render('reservations/Index', [
            'reservations' => $reservations,
        ]);
    }

    public function create(Room $room): Response
    {
        return Inertia::render('reservations/Create', [
            'room' => $room,
        ]);
    }

    public function store(StorereservationRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $room = Room::query()
                ->lockForUpdate()
                ->findOrFail($request->room_id);

            if ($room->reservations()->exists()) {
                DB::rollBack();

                return back()->withErrors([
                    'room_id' => 'This room is no longer available.',
                ]);
            }

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
        } catch (\Exception $exception) {
            DB::rollBack();

            return back()->withErrors(['payment' => $exception->getMessage()]);
        }
    }

    public function destroy(Reservation $reservation): RedirectResponse
    {
        if ($reservation->client_id !== auth()->id()) {
            abort(403);
        }

        $reservation->delete();

        return back()->with('success', 'Reservation cancelled successfully.');
    }
}
