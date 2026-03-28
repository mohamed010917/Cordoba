<?php

namespace App\Actions\user;

use App\Models\User;

class Index
{
    public function handle($request , $role = 'user' )
    {
     $users = User::with("country")->where("role", $role);

        if ($request->search ) {
            $users->where(function ($q) use ($request) {
                $q->where("name", "like", "%{$request->search}%")
                ->orWhere("email", "like", "%{$request->search}%");
            });
        }
        if($request->ban !== null && $request->ban != '2'){
            $users->where("is_banned", $request->ban);
        }

        if ($request->active !== null && $request->active != '2') {
            $users->where("is_active", $request->active);
        }

        return $users->paginate(20)->withQueryString();

    }
}
