<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ApprovedClientController extends Controller 
{
        public function index(Request $request): Response
        {
            $search = trim($request->get('search', ''));
            $sort = $request->get('sort', 'approved_at');
            $direction = $request->get('direction', 'desc');
    
            $allowedSorts = ['name', 'email', 'approved_at', 'created_at'];
    
            if (!in_array($sort, $allowedSorts)) {
                $sort = 'approved_at';
            }
    
            if (!in_array($direction, ['asc', 'desc'])) {
                $direction = 'desc';
            }
    
            $clients = User::query()
                ->approvedClients()
                ->where('approved_by', auth()->id())
                ->with('country:id,name')
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhereHas('country', function ($countryQuery) use ($search) {
                                $countryQuery->where('name', 'like', "%{$search}%");
                            });
                    });
                })
                ->orderBy($sort, $direction)
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
                        'country' => $client->country?->name,
                        'approved_at' => $client->approved_at?->format('Y-m-d H:i'),
                    ];
                });
    
            return Inertia::render('receptionist/clients/Approved', [
                'clients' => $clients,
                'filters' => [
                    'search' => $search,
                    'sort' => $sort,
                    'direction' => $direction,
                ],
            ]);
        }
}