<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ClientApprovedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PendingClientController extends Controller
{
    public function index(Request $request): Response
    {
        $clients = User::query()
            ->pendingClients()
            ->with(['country:id,name', 'city:id,name'])
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'phone' => $client->phone,
                    'gender' => $client->gender,
                    'image' => $client->image,
                    'country' => $client->country ? $client->country->name : null,
                    'city' => $client->city?->name,
                    'created_at' => $client->created_at?->format('Y-m-d H:i'),
                ];
            });

        return Inertia::render('receptionist/clients/Pending', [
            'clients' => $clients,
        ]);
    }

    public function approve(int $client): RedirectResponse
    {
        $client = User::query()
            ->pendingClients()
            ->findOrFail($client);

        $client->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        $client->notify(new ClientApprovedNotification(Auth::user()?->name));

        return redirect()
            ->route('receptionist.clients.pending')
            ->with('success', 'Client approved successfully.');
    }
}
