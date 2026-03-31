<?php

namespace App\Actions\user;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Create
{
    public function handle($request)
    {
        try{
            if(! Auth::user()->hasRole('admin') &&( $request->role === 'admin' || $request->role === 'manager') ){
               abort(402);
            }
            if($request->hasFile('image')){
                $imagePath = $request->file('image')->store('users', 'public');
            }
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make( $request->password),
                'image' => asset('storage/' . $imagePath) ,
                'role' => $request->role,
                'is_active' => $request->is_active,
                'phone' => $request->phone,
                'national_id' => $request->national_id,
                'country_id' => $request->country_id ,
                'city_id' => $request->city_id ,
                "created_by_manager_id" => Auth::id() ,
                "gender" => $request->gender ,
            ]);

            $user->assignRole($request->role) ;
            return $user ;
        }catch(\Exception $e){
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
