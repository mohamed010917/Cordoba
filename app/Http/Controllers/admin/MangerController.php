<?php

namespace App\Http\Controllers\admin;

use App\Actions\user\Index;
use App\Http\Controllers\Controller;
use App\Http\Requests\userCreate;
use App\Models\Countrie;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MangerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $IndexAction = new Index() ;
        $managers = $IndexAction->handle($request ,"manager") ;
        return Inertia::render('managers/users', [
            "managers" => $managers

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = \App\Models\Countrie::all() ;
        return Inertia::render('managers/create' ,  [
            'countries' => $countries
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(userCreate $request)
    {
        $createAction = new \App\Actions\user\Create() ;
        $manager = $createAction->handle($request , "manager") ;
        if(!$manager){
            return redirect()->back()->with("error" , "An error occurred while creating the manager");
        }
        return redirect()->to("/admin/managers")->with("success" , "Manager created successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(User $manager )
    {
            
            return Inertia::render('managers/show' ,  [
                'user' => $manager->load(([
                                    'country',
                                    'createdByManager',
                ]))
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $manager)
    {
     
        $countries = Countrie::all() ;
        return Inertia::render('managers/edit' ,  [
            'user' => $manager->load("country"),
            'countries' => $countries
         ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $manager)
    {
        try{
            if($request->hasFile('image')){
                $imagePath = $request->file('image')->store('users', 'public');
                $manager->image = asset('storage/' . $imagePath) ;
            }
            $manager->name = $request->name ;
            $manager->email = $request->email ;
            $manager->phone = $request->phone ;
            $manager->national_id = $request->national_id ;
            $manager->country_id = $request->country_id ;
            if($request->password){
                $manager->password = \Illuminate\Support\Facades\Hash::make($request->password) ;
            }
            $manager->save() ;
            return redirect()->route('admin.managers.index')->with("success" , "Manager updated successfully");
        }catch(\Exception $e){
            return redirect()->back()->with("error" , "An error occurred while updating the manager");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $manager)
    {
        try{
           if(Auth::user()->can('delete users') && $manager->id != Auth::id()){
            $manager->delete() ;
            return redirect()->route('admin.managers.index')->with("success" , "Manager deleted successfully");
           }
           abort(403 , "You don't have permission to delete this manager") ;
        }catch(\Exception $e){
            return redirect()->back()->with("error" , "An error occurred while deleting the manager");
        }
    }
}
