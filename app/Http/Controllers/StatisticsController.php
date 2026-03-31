<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class StatisticsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('statistics/Index');
    }

    public function gender(Request $request): JsonResponse
    {
        $rows = $this->baseReservationsQuery($request)
            ->join('users', 'users.id', '=', 'reservations.client_id')
            ->select('users.gender', DB::raw('COUNT(reservations.id) as total'))
            ->whereIn('users.gender', ['male', 'female'])
            ->groupBy('users.gender')
            ->pluck('total', 'users.gender');

        return response()->json([
            'labels' => ['Male', 'Female'],
            'data' => [
                (int) ($rows['male'] ?? 0),
                (int) ($rows['female'] ?? 0),
            ],
        ]);
    }

    public function revenue(Request $request): JsonResponse
    {
        $rows = $this->baseReservationsQuery($request)
            ->select('reservations.created_at', 'reservations.paid_price_cents')
            ->get();

        $monthlyTotals = array_fill(1, 12, 0);

        foreach ($rows as $row) {
            $month = (int) $row->created_at->format('n');
            $monthlyTotals[$month] += (int) $row->paid_price_cents;
        }

        $labels = [];
        $data = [];

        for ($month = 1; $month <= 12; $month++) {
            $labels[] = now()->startOfYear()->setMonth($month)->format('M');
            $data[] = round($monthlyTotals[$month] / 100, 2);
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    public function countries(Request $request): JsonResponse
    {
        $rows = $this->baseReservationsQuery($request)
            ->join('users', 'users.id', '=', 'reservations.client_id')
            ->leftJoin('countries', 'countries.id', '=', 'users.country_id')
            ->selectRaw('COALESCE(countries.name, "Unknown") as country_name, COUNT(reservations.id) as total')
            ->groupBy('country_name')
            ->orderByDesc('total')
            ->get();

        return response()->json([
            'labels' => $rows->pluck('country_name')->values(),
            'data' => $rows->pluck('total')->map(fn ($value) => (int) $value)->values(),
        ]);
    }

    public function topClients(Request $request): JsonResponse
    {
        $rows = $this->baseReservationsQuery($request)
            ->join('users', 'users.id', '=', 'reservations.client_id')
            ->select('users.name', DB::raw('COUNT(reservations.id) as total'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return response()->json([
            'labels' => $rows->pluck('name')->values(),
            'data' => $rows->pluck('total')->map(fn ($value) => (int) $value)->values(),
        ]);
    }

    private function baseReservationsQuery(Request $request): QueryBuilder
    {
        $selectedYear = (int) data_get($request->query(), 'filter.year', now()->year);

        $query = QueryBuilder::for(Reservation::query())
            ->allowedFilters(
                AllowedFilter::callback('year', function (Builder $query, mixed $value): void {
                    $query->whereYear('reservations.created_at', (int) $value);
                })
            );

        $query->whereYear('reservations.created_at', $selectedYear);

        return $query;
    }
}
