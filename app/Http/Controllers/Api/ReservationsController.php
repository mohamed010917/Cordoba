<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Reservation::query()->select([
            'id',
            'client_id',
            'room_id',
            'accompany_number',
            'paid_price_cents',
            'receptionist_id',
            'created_at',
            'updated_at',
        ]);

        $allowedIncludes = [
            'client',
            'room',
            'room.floor',
            'receptionist',
            'payments',
        ];

        $requestedIncludes = collect(explode(',', (string) $request->query('include', '')))
            ->map(fn (string $include): string => trim($include))
            ->filter()
            ->intersect($allowedIncludes)
            ->values()
            ->all();

        if ($requestedIncludes !== []) {
            $query->with($requestedIncludes);
        }

        $exactFilters = [
            'client_id' => 'client_id',
            'room_id' => 'room_id',
            'receptionist_id' => 'receptionist_id',
        ];

        foreach ($exactFilters as $requestKey => $column) {
            $value = $request->input("filter.{$requestKey}");

            if ($value !== null && $value !== '') {
                $query->where($column, $value);
            }
        }

        $minPaidPrice = $request->input('filter.min_paid_price_cents');
        $maxPaidPrice = $request->input('filter.max_paid_price_cents');

        if ($minPaidPrice !== null && $minPaidPrice !== '') {
            $query->where('paid_price_cents', '>=', (int) $minPaidPrice);
        }

        if ($maxPaidPrice !== null && $maxPaidPrice !== '') {
            $query->where('paid_price_cents', '<=', (int) $maxPaidPrice);
        }

        $allowedSorts = [
            'id',
            'created_at',
            'paid_price_cents',
            'accompany_number',
        ];

        $requestedSort = (string) $request->query('sort', '-created_at');
        $sortDirection = str_starts_with($requestedSort, '-') ? 'desc' : 'asc';
        $sortColumn = ltrim($requestedSort, '-');

        if (! in_array($sortColumn, $allowedSorts, true)) {
            $sortColumn = 'created_at';
            $sortDirection = 'desc';
        }

        $reservations = $query
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(15)
            ->withQueryString();

        return response()->json($reservations);
    }
}
