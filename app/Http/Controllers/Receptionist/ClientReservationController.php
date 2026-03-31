<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientReservationController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim($request->get('search', ''));
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $allowedSorts = [
            'client_name',
            'client_email',
            'accompany_number',
            'room_number',
            'paid_price_cents',
            'created_at',
        ];

        if (! in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }

        if (! in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $reservations = Reservation::query()
            ->with([
                'client:id,name,email,approved_by,role',
                'room:id,number',
            ])
            ->whereHas('client', function ($clientQuery) {
                $clientQuery->where('approved_by', auth()->id());
                $clientQuery->where('role', 'user');
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('room', function ($roomQuery) use ($search) {
                        $roomQuery->where('number', 'like', "%{$search}%");
                    })
                    ->orWhere('accompany_number', 'like', "%{$search}%")
                    ->orWhere('paid_price_cents', 'like', "%{$search}%")
                    ->orWhere('created_at', 'like', "%{$search}%");
                });
            });

        switch ($sort) {
            case 'client_name':
                $reservations
                    ->join('users as clients', 'reservations.client_id', '=', 'clients.id')
                    ->select('reservations.*')
                    ->orderBy('clients.name', $direction);
                break;

            case 'client_email':
                $reservations
                    ->join('users as clients', 'reservations.client_id', '=', 'clients.id')
                    ->select('reservations.*')
                    ->orderBy('clients.email', $direction);
                break;

            case 'room_number':
                $reservations
                    ->join('rooms', 'reservations.room_id', '=', 'rooms.id')
                    ->select('reservations.*')
                    ->orderBy('rooms.number', $direction);
                break;

            default:
                $reservations->orderBy($sort, $direction);
                break;
        }

        $reservations = $reservations
            ->paginate(10)
            ->withQueryString()
            ->through(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'client_name' => $reservation->client?->name,
                    'client_email' => $reservation->client?->email,
                    'accompany_number' => $reservation->accompany_number,
                    'room_number' => $reservation->room?->number,
                    'paid_price_cents' => $reservation->paid_price_cents,
                    'created_at' => $reservation->created_at?->format('Y-m-d H:i'),
                ];
            });

        return Inertia::render('receptionist/clients/Reservations', [
            'reservations' => $reservations,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
            ],
        ]);
    }
}