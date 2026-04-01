<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $managerId = auth()->id();

        $totalClients = User::query()->clients()->count();
        $approvedClients = User::query()->clients()->whereNotNull('approved_at')->count();
        $pendingClients = User::query()->clients()->whereNull('approved_at')->count();
        $inactiveClients = User::query()->clients()->where('is_active', false)->count();

        $totalReceptionists = User::query()->receptionists()->count();
        $activeReceptionists = User::query()->receptionists()->where('is_active', true)->where('is_banned', false)->count();

        $totalReservations = Reservation::query()->count();
        $reservationsToday = Reservation::query()->whereDate('created_at', Carbon::today())->count();

        $myRoomsQuery = Room::query()->whereHas('floor', function ($q) use ($managerId): void {
            $q->where('manger_id', $managerId);
        });

        $myRoomsTotal = (clone $myRoomsQuery)->count();
        $myRoomsOccupied = (clone $myRoomsQuery)->whereHas('reservations')->count();

        $latestReservations = Reservation::query()
            ->with([
                'client:id,name,email',
                'room:id,number,floor_id',
                'room.floor:id,name',
            ])
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn (Reservation $r): array => [
                'id' => $r->id,
                'client_name' => $r->client?->name,
                'client_email' => $r->client?->email,
                'room_number' => $r->room?->number,
                'floor_name' => $r->room?->floor?->name,
                'paid_price_cents' => $r->paid_price_cents,
                'created_at' => $r->created_at?->toIso8601String(),
            ]);

        $latestClients = User::query()
            ->clients()
            ->with(['country:id,name', 'city:id,name'])
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn (User $u): array => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'country' => $u->country?->name,
                'city' => $u->city?->name,
                'approved_at' => $u->approved_at?->toIso8601String(),
                'created_at' => $u->created_at?->toIso8601String(),
            ]);

        $reservationTrend = $this->reservationTrendLastSevenDays();

        return Inertia::render('manager/Dashboard', [
            'stats' => [
                'total_clients' => $totalClients,
                'approved_clients' => $approvedClients,
                'pending_clients' => $pendingClients,
                'inactive_clients' => $inactiveClients,
                'total_receptionists' => $totalReceptionists,
                'active_receptionists' => $activeReceptionists,
                'total_reservations' => $totalReservations,
                'reservations_today' => $reservationsToday,
                'my_rooms_total' => $myRoomsTotal,
                'my_rooms_occupied' => $myRoomsOccupied,
            ],
            'latest_reservations' => $latestReservations,
            'latest_clients' => $latestClients,
            'reservation_trend' => $reservationTrend,
        ]);
    }

    /**
     * @return array<int, array{date: string, count: int}>
     */
    private function reservationTrendLastSevenDays(): array
    {
        $start = Carbon::today()->subDays(6)->startOfDay();

        $counts = Reservation::query()
            ->where('created_at', '>=', $start)
            ->get(['created_at'])
            ->groupBy(fn ($r) => $r->created_at->toDateString())
            ->map(fn (Collection $group) => $group->count());

        $out = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i)->toDateString();
            $out[] = [
                'date' => $d,
                'count' => (int) ($counts[$d] ?? 0),
            ];
        }

        return $out;
    }
}
