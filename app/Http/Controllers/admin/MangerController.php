<?php

namespace App\Http\Controllers\admin;

use App\Actions\user\Index;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
    public function store(Request $request)
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
    public function show(string $id)
    {
            $manager = \App\Models\User::findOrFail($id) ;
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
    public function edit(string $id)
    {
        $manager = \App\Models\User::findOrFail($id) ;
        $countries = \App\Models\Countrie::all() ;
        return Inertia::render('managers/edit' ,  [
            'user' => $manager->load("country"),
            'countries' => $countries
         ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $manager = \App\Models\User::findOrFail($id) ;
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
    public function destroy(string $id)
    {
        try{
            $manager = \App\Models\User::findOrFail($id) ;
            $manager->delete() ;
            return redirect()->route('admin.managers.index')->with("success" , "Manager deleted successfully");
        }catch(\Exception $e){
            return redirect()->back()->with("error" , "An error occurred while deleting the manager");
        }
    }
}
