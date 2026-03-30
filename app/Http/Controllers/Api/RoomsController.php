<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\JsonResponse;

class RoomsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $rooms = Room::query()
            ->select(['id', 'number', 'capacity', 'price_cents', 'floor_id', 'manager_id', 'created_at', 'updated_at'])
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'data' => $rooms,
        ]);
    }
}
