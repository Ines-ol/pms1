<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\reservation;
use App\Models\Room;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{


    //rooms

    // liste rooms
    public function listRooms(Request $request)
    {
    // // Vérification des permissions
    // if ($request->user()->role !== 'manager') {
    //     return response()->json([
    //         'message' => 'Unauthorized - Only property managers can list rooms'
    //     ], 403);
    // }

    // Récupération de toutes les chambres avec pagination
    $rooms = Room::select([
            'ID_ROOM as id',
            'TYPE as type',
            'PRICE as price',
            'AVAILABLE as available'
        ])
        ->paginate(10); // 10 chambres par page

    return response()->json([
        'message' => 'Rooms retrieved successfully',
        'data' => $rooms
    ]);
}
    //add rooms
    public function addRoom(Request $request)
    {
        // // Vérifier que l'utilisateur est bien un property manager
        // if ($request->user()->role !== 'manager') {
        //     return response()->json([
        //         'message' => 'Unauthorized - Only property managers can add rooms'
        //     ], 403);
        // }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'TYPE' => 'required|in:single,double,suite',
            'PRICE' => 'required|numeric|min:0',
            'AVAILABLE' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Création de la chambre
        $room = Room::create([
            'TYPE' => $request->TYPE,
            'PRICE' => $request->PRICE,
            'AVAILABLE' => $request->AVAILABLE ?? true
        ]);

        return response()->json([
            'message' => 'Room added successfully',
            'room' => $room
        ], 201);
    }

    //update rooms
    public function updateRoom(Request $request, $id)
    {
    // // Vérifier que l'utilisateur est bien un property manager
    // if ($request->user()->role !== 'manager') {
    //     return response()->json([
    //         'message' => 'Unauthorized - Only property managers can update rooms'
    //     ], 403);
    // }

    // Validation des données
    $validator = Validator::make($request->all(), [
        'TYPE' => 'sometimes|in:single,double,suite',
        'PRICE' => 'sometimes|numeric|min:0',
        'AVAILABLE' => 'sometimes|boolean'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation error',
            'errors' => $validator->errors()
        ], 422);
    }

    // Recherche de la chambre
    $room = Room::find($id);

    if (!$room) {
        return response()->json([
            'message' => 'Room not found'
        ], 404);
    }

    // Mise à jour des champs fournis
    if ($request->has('TYPE')) {
        $room->TYPE = $request->TYPE;
    }
    if ($request->has('PRICE')) {
        $room->PRICE = $request->PRICE;
    }
    if ($request->has('AVAILABLE')) {
        $room->AVAILABLE = $request->AVAILABLE;
    }

    $room->save();

    return response()->json([
        'message' => 'Room updated successfully',
        'room' => $room
    ]);
}

//delete rooms

public function deleteRoom(Request $request, $id)
{
    // // Vérifier que l'utilisateur est bien un property manager
    // if ($request->user()->role !== 'manager') {
    //     return response()->json([
    //         'message' => 'Unauthorized - Only property managers can delete rooms'
    //     ], 403);
    // }

    // Recherche de la chambre
    $room = Room::find($id);

    if (!$room) {
        return response()->json([
            'message' => 'Room not found'
        ], 404);
    }

    // Vérifier s'il y a des réservations actives pour cette chambre
    $activeReservations = Reservation::where('ID_ROOM', $id)
        ->where('STATUS', 'confirmed')
        ->where('END_DATE', '>=', now())
        ->exists();

    if ($activeReservations) {
        return response()->json([
            'message' => 'Cannot delete room with active reservations'
        ], 400);
    }

    // Suppression de la chambre
    $room->delete();

    return response()->json([
        'message' => 'Room deleted successfully'
    ]);
}



    //reservation

    //liste des reservation 
    public function listReservations(Request $request)
{
    // // Vérification plus robuste de l'authentification
    // $user = Auth::user();
    
    // if (!$user) {
    //     return response()->json([
    //         'message' => 'Unauthenticated - Please log in'
    //     ], 401);
    // }

    // if ($user->role !== 'manager') {
    //     return response()->json([
    //         'message' => 'Forbidden - You do not have manager privileges'
    //     ], 403);
    // }

    try {
        // Construction de la requête avec eager loading
        $query = Reservation::with([
                'client.user:id,NAME,EMAIL',
                'room:id,TYPE,PRICE'
            ])
            ->select([
                'ID_RESERVATION as id',
                'ID_CLIENT',
                'ID_ROOM',
                'START_DATE as start_date',
                'END_DATE as end_date',
                'STATUS as status'
            ]);

        // Filtres optionnels
        if ($request->has('status')) {
            $query->where('STATUS', $request->status);
        }

        if ($request->has('date_from')) {
            $query->where('END_DATE', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('START_DATE', '<=', $request->date_to);
        }

        if ($request->has('room_type')) {
            $query->whereHas('room', function($q) use ($request) {
                $q->where('TYPE', $request->room_type);
            });
        }

        // Tri par défaut sur la date de début (les plus récentes en premier)
        $sortField = $request->get('sort_by', 'START_DATE');
        $sortDirection = $request->get('sort_dir', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination avec 15 éléments par page par défaut
        $perPage = $request->get('per_page', 15);
        $reservations = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Reservations retrieved successfully',
            'data' => $reservations
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve reservations',
            'error' => $e->getMessage()
        ], 500);
    }
}
}