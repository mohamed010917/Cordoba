<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StoreReceptionistRequest;
use App\Http\Requests\Manager\UpdateReceptionistRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ReceptionistController extends Controller
{
    private function authorizeOwnership(int $managerId, ?int $creatorId): void
    {
    abort_if($managerId !== (int) $creatorId, 403, 'You are not allowed to modify this receptionist.');
    }
    //index
    public function index(Request $request): Response
    {
        $manager = auth()->user();
    
        $search = $request->string('search')->toString();
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
    
        $allowedSorts = ['name', 'email', 'created_at'];
    
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }
    
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }
    
        $receptionists = User::query()
            ->receptionists()
            ->with(['createdByManager:id,name'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('national_id', 'like', "%{$search}%");
                });
            })
            ->when($dateFrom, function ($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString()
            ->through(function ($receptionist) use ($manager) {
                return [
                    'id' => $receptionist->id,
                    'name' => $receptionist->name,
                    'email' => $receptionist->email,
                    'phone' => $receptionist->phone,
                    'image' => $receptionist->image,
                    'created_at' => $receptionist->created_at?->format('Y-m-d H:i'),
                    'is_banned' => (bool) $receptionist->is_banned,
                    'banned_at' => $receptionist->banned_at,
                    'manager_name' => $receptionist->createdByManager?->name,
                    'can_edit' => (int) $receptionist->created_by_manager_id === (int) $manager->id,
                    'can_delete' => (int) $receptionist->created_by_manager_id === (int) $manager->id,
                ];
            });
    
        return Inertia::render('manager/receptionists/Index', [
            'receptionists' => $receptionists,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    //create
    public function create(): Response
    {
        return Inertia::render('manager/receptionists/Create');
    }

    //store
    public function store(StoreReceptionistRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('users', 'public');
        }
        $receptionist = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'national_id' => $data['national_id'] ?? null,
            'gender' => $data['gender'] ?? null,
            'country_id' => $data['country_id'] ?? null,
            'image' => $data['image'] ?? null,
            'role' => 'receptionist',
            'created_by_manager_id' => auth()->id(),
            'is_active' => true,
            'is_banned' => false,
        ]);
        return redirect()
        ->route('manager.receptionists.index')
        ->with('success', 'Receptionist created successfully.');
    }

    //edit
    public function edit(int $receptionist): Response
    {
        $manager = auth()->user();
        $receptionist = user::query()
        ->receptionists()
        ->findOrFail($receptionist);
        $this->authorizeOwnership($manager->id, $receptionist->created_by_manager_id);
        return Inertia::render('manager/receptionists/Edit',[
            'receptionist'=>[
                'id' => $receptionist->id,
                'name' => $receptionist->name,
                'email' => $receptionist->email,
                'phone' => $receptionist->phone,
                'national_id' => $receptionist->national_id,
                'gender' => $receptionist->gender,
                'country_id' => $receptionist->country_id,
                'image' => $receptionist->image,
            ],
        ]);
    }

    //update
    public function update(UpdateReceptionistRequest $request, User $receptionist): RedirectResponse
    {
        $manager = auth()->user();
    
        $receptionist = User::query()
            ->receptionists()
            ->findOrFail($receptionist->id);
    
        $this->authorizeOwnership($manager->id, $receptionist->created_by_manager_id);
    
        $data = $request->validated();
    
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'national_id' => $data['national_id'] ?? null,
            'gender' => $data['gender'] ?? null,
            'country_id' => $data['country_id'] ?? null,
        ];
    
        if ($request->hasFile('image')) {
            if ($receptionist->image) {
                Storage::disk('public')->delete($receptionist->image);
            }
    
            $updateData['image'] = $request->file('image')->store('users', 'public');
        }
    
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }
    
        $receptionist->update($updateData);
    
        return redirect()
            ->route('manager.receptionists.index')
            ->with('success', 'Receptionist updated successfully.');
    }

    //destroy
    public function destroy(int $receptionist): RedirectResponse
    {
        $manager = auth()->user();

        $receptionist = User::query()
            ->receptionists()
            ->findOrFail($receptionist);

        $this->authorizeOwnership($manager->id, $receptionist->created_by_manager_id);

        if ($receptionist->image) {
            Storage::disk('public')->delete($receptionist->image);
        }

        $receptionist->delete();

        return redirect()
            ->route('manager.receptionists.index')
            ->with('success', 'Receptionist deleted successfully.');
    }

    //ban
    public function ban(int $receptionist): RedirectResponse
    {
        $receptionist = User::query()
            ->receptionists()
            ->findOrFail($receptionist);

        if (!$receptionist->is_banned) {
            $receptionist->update([
                'is_banned' => true,
                'banned_at' => now(),
                'banned_by' => auth()->id(),
            ]);
        }

        return redirect()
            ->route('manager.receptionists.index')
            ->with('success', 'Receptionist banned successfully.');
    }

    //unban
    public function unban(int $receptionist): RedirectResponse
    {
        $receptionist = User::query()
            ->receptionists()
            ->findOrFail($receptionist);

        $receptionist->update([
            'is_banned' => false,
            'banned_at' => null,
            'banned_by' => null,
        ]);

        return redirect()
            ->route('manager.receptionists.index')
            ->with('success', 'Receptionist unbanned successfully.');
    }
}