<?php

namespace App\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'manager') {
            return redirect()->route('manager.dashboard');
        }

        if ($user->role === 'receptionist') {
            return redirect()->route('receptionist.dashboard');
        }

        if (! $user->is_approved) {
            return redirect()->route('pending-approval');
        }

        return redirect()->route('dashboard');
    }
}
