<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFloorRequest;
use App\Http\Requests\UpdateFloorRequest;
use App\Models\Floor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FloorController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Floor::class);

        $query = Floor::with('manager:id,name')
            ->orderBy(
                $request->input('sort_by', 'created_at'),
                $request->input('sort_dir', 'desc')
            );

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('number', 'like', "%{$search}%");
            });
        }

        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $floors = $query->paginate($request->input('per_page', 10))
            ->withQueryString();

        return Inertia::render('Floors/Index', [
            'floors' => $floors,
            'isAdmin' => $user->hasRole('admin'),
            'filters' => $request->only(['search', 'sort_by', 'sort_dir']),
            'url' => route('manager.floors.index'),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Floor::class);

        return Inertia::render('Floors/Create', [
            'nextNumber' => Floor::generateNumber(),
        ]);
    }

    public function store(StoreFloorRequest $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        Floor::create([
            'name' => $request->name,
            'number' => Floor::generateNumber(),
            'manger_id' => $user->id,
        ]);

        return redirect()->route('manager.floors.index')
            ->with('success', 'Floor created successfully.');
    }

    public function edit(Floor $floor): Response
    {
        $this->authorize('update', $floor);

        return Inertia::render('Floors/Edit', [
            'floor' => $floor,
        ]);
    }

    public function update(UpdateFloorRequest $request, Floor $floor): RedirectResponse
    {
        $floor->update(['name' => $request->name]);

        return redirect()->route('manager.floors.index')
            ->with('success', 'Floor updated successfully.');
    }

    public function destroy(Floor $floor): RedirectResponse
    {
        $this->authorize('delete', $floor);

        if ($floor->hasRooms()) {
            return redirect()->back()->withErrors([
                'floor' => 'Cannot delete floor: it has rooms associated with it.',
            ]);
        }

        $floor->delete();

        return redirect()->back()->with('success', 'Floor deleted successfully.');
    }
}
