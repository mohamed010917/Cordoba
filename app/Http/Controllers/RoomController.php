<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Floor;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoomController extends Controller
{
    public function publicIndex(Request $request): Response
    {
        $rooms = Room::available()->with('floor:id,number')->get();

        return Inertia::render('rooms/PublicIndex', [
            'rooms' => $rooms,
        ]);
    }

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Room::class);
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $query = Room::query()
            ->with(['floor:id,name,number,manger_id', 'manager:id,name'])
            ->orderBy(
                $request->input('sort_by', 'created_at'),
                $request->input('sort_dir', 'desc')
            );

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhere('capacity', 'like', "%{$search}%")
                    ->orWhereHas('floor', fn ($fq) => $fq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($user->hasRole('manager')) {
            $query->whereHas('floor', fn ($q) => $q->where('manger_id', $user->id));
        }

        $rooms = $query->paginate($request->input('per_page', 10))->withQueryString();

        return Inertia::render('rooms/Index', [
            'rooms' => $rooms,
            'isAdmin' => $user->hasRole('admin'),
            'filters' => $request->only(['search', 'sort_by', 'sort_dir']),
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', Room::class);
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $floors = Floor::query()
            ->when(
                $user->hasRole('manager'),
                fn ($q) => $q->where('manger_id', $user->id)
            )
            ->select('id', 'name', 'number')
            ->orderBy('number')
            ->get();

        $managers = User::query()
            ->where('role', 'manager')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('rooms/Create', [
            'floors' => $floors,
            'managers' => $managers,
            'isAdmin' => $user->hasRole('admin'),
        ]);
    }

    public function store(StoreRoomRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        if (! $user->hasRole('admin')) {
            $data['manager_id'] = $user->id;
        }

        Room::create($data);

        return redirect()->route('manager.rooms.index')
            ->with('success', 'Room created successfully.');
    }

    public function edit(Request $request, Room $room): Response
    {
        $this->authorize('update', $room);
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $floors = Floor::query()
            ->when(
                $user->hasRole('manager'),
                fn ($q) => $q->where('manger_id', $user->id)
            )
            ->select('id', 'name', 'number')
            ->orderBy('number')
            ->get();

        $managers = User::query()
            ->where('role', 'manager')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('rooms/Edit', [
            'room' => $room->load(['floor:id,name,number', 'manager:id,name']),
            'floors' => $floors,
            'managers' => $managers,
            'isAdmin' => $user->hasRole('admin'),
        ]);
    }

    public function update(UpdateRoomRequest $request, Room $room): RedirectResponse
    {
        $this->authorize('update', $room);

        $data = $request->validated();
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        if (! $user->hasRole('admin')) {
            unset($data['manager_id']);
        }

        $room->update($data);

        return redirect()->route('manager.rooms.index')
            ->with('success', 'Room updated successfully.');
    }

    public function destroy(Request $request, Room $room): JsonResponse
    {
        $this->authorize('delete', $room);

        if ($room->isReserved()) {
            return response()->json([
                'message' => 'Cannot delete room: it has reservations associated with it.',
            ], 422);
        }

        $room->delete();

        return response()->json([
            'message' => 'Room deleted successfully.',
        ]);
    }
}
