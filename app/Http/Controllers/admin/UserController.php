<?php

namespace App\Http\Controllers\admin;

use App\Actions\user\Create as CreateUserAction;
use App\Actions\user\Index as IndexUsersAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUser;
use App\Http\Requests\userCreate;
use App\Models\Countrie;
use App\Models\User;
use App\Notifications\ClientApprovedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $indexAction = new IndexUsersAction;
        $users = $indexAction->handle($request);

        return Inertia::render('users/users', [
            'users' => $users,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('users/create', [
            'countries' => Countrie::all(),
        ]);
    }

    public function store(userCreate $request): RedirectResponse
    {
        $createAction = new CreateUserAction;
        $user = $createAction->handle($request);

        if (! $user instanceof User) {
            return redirect()->back()->with('error', 'An error occurred while creating the user');
        }
        return redirect()->route('admin.users.index');

    }

    public function show(User $user): Response
    {
        return Inertia::render('users/show', [
            'user' => $user->load([
                'country',
                'createdByManager',
                'approvedBy',
                'bannedBy',
            ]),
        ]);
    }

    public function edit(User $user): Response
    {
        return Inertia::render('users/edit', [
            'user' => $user->load('country'),
            'countries' => Countrie::all(),
        ]);
    }

    public function update(UpdateUser $request, User $user): RedirectResponse
    {
        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'image' => $request->hasFile('image') ? asset('storage/'.$request->file('image')->store('users', 'public')) : $user->image,
                'role' => $request->role,
                'is_active' => $request->is_active,
                'phone' => $request->phone,
                'national_id' => $request->national_id,
                'country_id' => $request->country_id,
            ]);

            $user->syncRoles($request->role) ;
            return redirect()->route('admin.users.index')->with("success" , "User updated successfully");
        }catch(\Exception $e){
            return redirect()->back()->with("error" , "An error occurred while updating the user");

        }
    }

    public function destroy(User $user): RedirectResponse
    {
        if (Auth::user()->can('delete users')) {
            if (Auth::id() === $user->id) {
                return redirect()->back()->with('error', 'cant delete your self');
            }

            $user->delete();

            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
        }

        abort(403);
    }

    public function toggleBan(User $user): RedirectResponse
    {
        if (Auth::user()->can('edit users')) {
            if (Auth::id() === $user->id) {
                return redirect()->back()->with('error', 'cant ban your self');
            }

            $user->is_banned = ! $user->is_banned;

            if ($user->is_banned) {
                $user->banned_at = now();
                $user->banned_by = Auth::id();
            } else {
                $user->banned_at = null;
                $user->banned_by = null;
            }

            $user->save();

            return redirect()->back()->with('success', 'User ban status toggled successfully');
        }

        abort(403);
    }

    public function changRole(User $user, Request $request): RedirectResponse
    {
        if (Auth::user()->can('edit users')) {
            if (Auth::id() === $user->id) {
                return redirect()->back()->with('error', 'cant change your self role');
            }

            if (! Auth::user()->hasRole('admin') && ($request->role === 'admin' || $request->role === 'manager')) {
                abort(402);
            }

            $user->role = $request->role;
            $user->save();
            $user->syncRoles($request->role);

            return redirect()->back()->with('success', 'User role changed successfully');
        }

        return redirect()->back()->with('error', 'You dont have permission to change user role');
    }

    public function toggleActive(User $user): RedirectResponse
    {
        if (Auth::user()->can('edit users')) {
            if (Auth::id() === $user->id) {
                return redirect()->back()->with('error', 'cant change your self active status');
            }

            $user->is_active = ! $user->is_active;
            $user->save();

            return redirect()->back()->with('success', 'User active status toggled successfully');
        }

        abort(403);
    }

    public function approve(User $user): RedirectResponse
    {
        if (Auth::user()->can('edit users')) {
            if ($user->is_approved) {
                return redirect()->back()->with('success', 'User is already approved.');
            }

            $user->is_approved = true;
            $user->approved_by = Auth::id();
            $user->approved_at = now();
            $user->save();

            $user->notify(new ClientApprovedNotification(Auth::user()->name));

            return redirect()->back()->with('success', 'User approved successfully');
        }

        abort(403);
    }
}
