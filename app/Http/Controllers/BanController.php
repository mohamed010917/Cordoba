<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorebanRequest;
use App\Http\Requests\UpdatebanRequest;
use App\Models\ban;

class BanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorebanRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ban $ban)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ban $ban)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatebanRequest $request, ban $ban)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ban $ban)
    {
        //
    }
}
