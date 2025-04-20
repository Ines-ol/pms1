<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\room;
use App\Models\client;
use App\Models\reservation;
use App\Models\serviceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    // update compte 
    public function updateEmployeeProfile(Request $request, $employeeId)
{
    // Validation des données
    $validator = Validator::make($request->all(), [
        'name' => 'sometimes|string|max:255',
        'email' => 'sometimes|string|email|max:255|unique:user,EMAIL,'.$employeeId.',ID_USER',
        'password' => 'sometimes|string|min:8',
        'phone' => 'sometimes|string|max:20',
        'address' => 'sometimes|string|max:255',
        'birthday' => 'sometimes|date_format:Y-m-d',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation error',
            'errors' => $validator->errors()
        ], 422);
    }

    // Trouver l'employé
    $employee = Employee::find($employeeId);
    
    if (!$employee) {
        return response()->json([
            'message' => 'Employee not found'
        ], 404);
    }

    // Trouver l'utilisateur associé
    $user = User::find($employee->ID_USER);
    
    if (!$user) {
        return response()->json([
            'message' => 'Associated user not found'
        ], 404);
    }

    DB::beginTransaction();
    try {
        // Mettre à jour l'utilisateur associé
        if ($request->has('name')) {
            $user->NAME = $request->name;
        }
        
        if ($request->has('email')) {
            $user->EMAIL = $request->email;
        }
        
        if ($request->has('password')) {
            $user->PASSWORD = Hash::make($request->password);
        }
        $user->save();

        // Mettre à jour l'employé
        if ($request->has('phone')) {
            $employee->PHONE = $request->phone;
        }
        
        if ($request->has('address')) {
            $employee->ADDRESS = $request->address;
        }
        
        if ($request->has('birthday')) {
            $employee->BIRTHDAY = $request->birthday;
        }
        $employee->save();

        DB::commit();

        return response()->json([
            'message' => 'Employee profile updated successfully',
            'employee_id' => $employee->ID_EMPLOYEE,
            'changes' => $request->all()
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Update failed',
            'error' => $e->getMessage()
        ], 500);
    }
}
    // liste des chambres
    public function getAllRooms()
{
    try {
        // Récupérer toutes les chambres
        $rooms = Room::all();

        // Vérifier si des chambres existent
        if ($rooms->isEmpty()) {
            return response()->json([
                'message' => 'No rooms found',
                'data' => []
            ], 200);
        }

        // Retourner la liste des chambres
        return response()->json([
            'message' => 'Rooms retrieved successfully',
            'data' => $rooms
        ], 200);

    } catch (\Exception $e) {
        // Gérer les erreurs
        return response()->json([
            'message' => 'Failed to retrieve rooms',
            'error' => $e->getMessage()
        ], 500);
    }
}
    // liste des service par client
    public function getClientServices($clientId)
    {
        try {
            // Vérifier si le client existe
            $client = Client::find($clientId);
            
            if (!$client) {
                return response()->json([
                    'message' => 'Client not found',
                    'data' => []
                ], 404);
            }
    
            // Récupérer les services associés au client (sans inclure CREATED_AT)
            $services = ServiceRequest::where('ID_CLIENT', $clientId)
                ->get(['ID_SERVICE_REQUEST', 'DESCRIPTION', 'STATUS']); // Retirer CREATED_AT
    
            // Formater la réponse
            return response()->json([
                'message' => 'Services retrieved successfully',
                'client' => [
                    'id' => $client->ID_CLIENT,
                    'name' => $client->user->NAME ?? 'N/A', // Gestion du cas où user est null
                    'email' => $client->user->EMAIL ?? 'N/A'
                ],
                'services' => $services
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve services',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // paiement 
    // resrevations 
 
}
