<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class rooomController extends Controller
{
    public function index()
    {
        return response()->json(Room::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:single,double,suite',
            'price' => 'required|numeric|min:0',
        ]);

        $room = Room::create($validated);
        return response()->json($room, 201);
    }

    public function show(Room $room)
    {
        return response()->json($room);
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'type' => 'sometimes|in:single,double,suite',
            'price' => 'sometimes|numeric|min:0',
            'available' => 'sometimes|boolean',
        ]);

        $room->update($validated);
        return response()->json($room);
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return response()->json(null, 204);
    }
}

