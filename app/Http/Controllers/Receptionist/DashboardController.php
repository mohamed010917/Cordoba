<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $receptionistId = auth()->id();

        $pendingClientsCount = User::query()->pendingClients()->count();

        $myApprovedCount = User::query()
            ->approvedClients()
            ->where('approved_by', $receptionistId)
            ->count();

        $myReservationsCount = Reservation::query()
            ->whereHas('client', function ($q) use ($receptionistId): void {
                $q->where('approved_by', $receptionistId)->where('role', 'user');
            })
            ->count();

        $recentPending = User::query()
            ->pendingClients()
            ->with(['country:id,name', 'city:id,name'])
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (User $u): array => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'country' => $u->country?->name,
                'city' => $u->city?->name,
                'created_at' => $u->created_at?->toIso8601String(),
            ]);

        $recentApproved = User::query()
            ->approvedClients()
            ->where('approved_by', $receptionistId)
            ->with(['country:id,name', 'city:id,name'])
            ->latest('approved_at')
            ->limit(6)
            ->get()
            ->map(fn (User $u): array => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'country' => $u->country?->name,
                'city' => $u->city?->name,
                'approved_at' => $u->approved_at?->toIso8601String(),
            ]);

        $recentReservations = Reservation::query()
            ->with([
                'client:id,name,email',
                'room:id,number',
            ])
            ->whereHas('client', function ($q) use ($receptionistId): void {
                $q->where('approved_by', $receptionistId)->where('role', 'user');
            })
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn (Reservation $r): array => [
                'id' => $r->id,
                'client_name' => $r->client?->name,
                'room_number' => $r->room?->number,
                'paid_price_cents' => $r->paid_price_cents,
                'created_at' => $r->created_at?->toIso8601String(),
            ]);

        return Inertia::render('receptionist/Dashboard', [
            'stats' => [
                'pending_clients' => $pendingClientsCount,
                'my_approved_clients' => $myApprovedCount,
                'my_reservations' => $myReservationsCount,
            ],
            'recent_pending' => $recentPending,
            'recent_approved' => $recentApproved,
            'recent_reservations' => $recentReservations,
        ]);
    }
}
