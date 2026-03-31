<?php

namespace App\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user?->role === 'user' && ! $user->is_approved) {
            return redirect()->route('pending-approval');
        }

        return redirect()->intended(config('fortify.home'));
    }
}
