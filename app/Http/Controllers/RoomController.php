<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Inertia\Inertia;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::available()->with('floor')->get();

        return Inertia::render('rooms/Index', [
            'rooms' => $rooms,
        ]);
    }
}
