<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\StoreClientRequest;
use App\Http\Requests\Manager\UpdateClientRequest;
use App\Models\Countrie;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function index(Request $request): Response
    {
        $search = (string) $request->query('search', '');
        $sort = (string) $request->query('sort', 'latest');
        $approvalStatus = (string) $request->query('approval_status', 'all');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $clients = User::query()
            ->clients()
            ->when($approvalStatus === 'approved', function ($query) {
                $query->whereNotNull('approved_at');
            })
            ->when($approvalStatus === 'not_approved', function ($query) {
                $query->whereNull('approved_at');
            })
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
            ->when($sort === 'name_asc', function ($query) {
                $query->orderBy('name', 'asc');
            })
            ->when($sort === 'name_desc', function ($query) {
                $query->orderBy('name', 'desc');
            })
            ->when($sort === 'oldest', function ($query) {
                $query->oldest();
            })
            ->when($sort === 'latest', function ($query) {
                $query->latest();
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('manager/clients/Index', [
            'clients' => $clients,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'approval_status' => $approvalStatus,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    public function create(): Response
    {
        $countries = Countrie::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('manager/clients/Create', [
            'countries' => $countries,
        ]);
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('users', 'public');
        }

        $client = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'national_id' => $data['national_id'] ?? null,
            'gender' => $data['gender'],
            'country_id' => $data['country_id'],
            'image' => $data['image'] ?? null,
            'role' => 'user',
            'is_active' => $data['is_active'] ?? true,
            'is_banned' => false,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        if (method_exists($client, 'assignRole')) {
            $client->assignRole('user');
        }

        return redirect()
            ->route('manager.clients.index')
            ->with('success', 'Client created successfully.');
    }

    public function edit(int $client): Response
    {
        $client = User::query()
            ->clients()
            ->findOrFail($client);

        $countries = Countrie::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('manager/clients/Edit', [
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
                'national_id' => $client->national_id,
                'gender' => $client->gender,
                'country_id' => $client->country_id,
                'image' => $client->image,
                'is_active' => (bool) $client->is_active,
            ],
            'countries' => $countries,
        ]);
    }

    public function update(UpdateClientRequest $request, int $client): RedirectResponse
    {
        $client = User::query()
            ->clients()
            ->findOrFail($client);

        $data = $request->validated();

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'national_id' => $data['national_id'] ?? null,
            'gender' => $data['gender'],
            'country_id' => $data['country_id'],
            'is_active' => $data['is_active'] ?? true,
        ];

        if ($request->hasFile('image')) {
            if ($client->image) {
                Storage::disk('public')->delete($client->image);
            }

            $updateData['image'] = $request->file('image')->store('users', 'public');
        }

        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $client->update($updateData);

        return redirect('/manager/clients')
            ->with('success', 'Client updated successfully');
    }

    public function destroy(int $client): RedirectResponse
    {
        $client = User::query()
            ->clients()
            ->findOrFail($client);

        if ($client->image) {
            Storage::disk('public')->delete($client->image);
        }

        $client->delete();

        return redirect('/manager/clients')
            ->with('success', 'Client deleted successfully.');
    }

    public function approve(int $client): RedirectResponse
    {
        $client = User::query()
            ->clients()
            ->whereNull('approved_at')
            ->findOrFail($client);

        $client->update([
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        return redirect()
            ->route('manager.clients.index')
            ->with('success', 'Client approved successfully.');
    }
}
