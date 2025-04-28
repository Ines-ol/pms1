<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\room;
use App\Models\client;
use App\Models\reservation;
use App\Models\payment;
use App\Models\invoice;
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
        // create reservation
        public function createReservationAsEmployee(Request $request)
    {
    // Validation
    $validator = Validator::make($request->all(), [
        'client_id' => 'required|exists:client,ID_CLIENT',
        'room_id' => 'required|exists:room,ID_ROOM',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'required|date|after:start_date',
        'status' => 'sometimes|in:pending,confirmed' // L'employé peut définir le statut
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }

    // Vérification de la disponibilité de la chambre
    $room = Room::find($request->room_id);
    if (!$room->AVAILABLE && $request->status !== 'confirmed') {
        return response()->json(['message' => 'Room is not available'], 400);
    }

    // Vérification des réservations qui se chevauchent
    $overlapping = Reservation::where('ID_ROOM', $request->room_id)
        ->where(function($query) use ($request) {
            $query->whereBetween('START_DATE', [$request->start_date, $request->end_date])
                  ->orWhereBetween('END_DATE', [$request->start_date, $request->end_date])
                  ->orWhere(function($query) use ($request) {
                      $query->where('START_DATE', '<=', $request->start_date)
                            ->where('END_DATE', '>=', $request->end_date);
                  });
        })
        ->exists();

    if ($overlapping) {
        return response()->json(['message' => 'Room is already booked for these dates'], 400);
    }

    DB::beginTransaction();
    try {
        // Création de la réservation
        $reservation = Reservation::create([
            'ID_CLIENT' => $request->client_id,
            'ID_ROOM' => $request->room_id,
            'START_DATE' => $request->start_date,
            'END_DATE' => $request->end_date,
            'STATUS' => $request->status ?? 'confirmed' // Par défaut "confirmed" pour les employés
        ]);

        // Marquer la chambre comme indisponible si le statut est "confirmed"
        if ($reservation->STATUS === 'confirmed') {
            $room->AVAILABLE = false;
            $room->save();
        }

        DB::commit();

        return response()->json([
            'message' => 'Reservation created successfully',
            'reservation' => $reservation
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Reservation creation failed',
            'error' => $e->getMessage()
        ], 500);
    }
    }

        // delete reservation
        public function deleteReservation($reservationId)
    {
    DB::beginTransaction();
    try {
        // Trouver la réservation
        $reservation = Reservation::find($reservationId);
        
        if (!$reservation) {
            return response()->json([
                'message' => 'Reservation not found'
            ], 404);
        }

        // Trouver la chambre associée
        $room = Room::find($reservation->ID_ROOM);
        
        // Supprimer la réservation
        $reservation->delete();

        // Si la chambre existe et que la réservation était confirmée, marquer la chambre comme disponible
        if ($room && $reservation->STATUS === 'confirmed') {
            $room->AVAILABLE = true;
            $room->save();
        }

        DB::commit();

        return response()->json([
            'message' => 'Reservation deleted successfully',
            'reservation_id' => $reservationId
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Failed to delete reservation',
            'error' => $e->getMessage()
        ], 500);
    }
    }
    
    // payment 

        // make payment

        public function makePayment(Request $request)
{
    // Validation des données
    $validator = Validator::make($request->all(), [
        'reservation_id' => 'required|exists:reservation,ID_RESERVATION',
        'amount' => 'required|numeric|min:0.01',
        'method' => 'required|in:cash,credit_card,bank_transfer',
        'first_name' => 'required_if:method,credit_card|string|max:100',
        'last_name' => 'required_if:method,credit_card|string|max:100',
        'card_number' => 'required_if:method,credit_card|string|max:20',
        'expiration_date' => 'required_if:method,credit_card|string|max:10',
        'cvv' => 'required_if:method,credit_card|string|max:4',
        'transaction_id' => 'sometimes|string|max:100'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation error',
            'errors' => $validator->errors()
        ], 400);
    }

    DB::beginTransaction();
    try {
        // Vérifier que la réservation existe
        $reservation = Reservation::find($request->reservation_id);
        if (!$reservation) {
            return response()->json([
                'message' => 'Reservation not found'
            ], 404);
        }

        // Créer le paiement
        $payment = Payment::create([
            'ID_RESERVATION' => $request->reservation_id,
            'FIRST_NAME' => $request->first_name,
            'LAST_NAME' => $request->last_name,
            'CARD_NUMBER' => $request->card_number,
            'EXPIRATION_DATE' => $request->expiration_date,
            'CVV' => $request->cvv,
            'METHOD' => $request->method,
            'AMOUNT' => $request->amount,
            'STATUS' => 'completed', // Directement marqué comme complété pour l'employee
            'TRANSACTION_ID' => $request->transaction_id ?? null,
            'PAYMENT_DATE' => now()
        ]);

        // Mettre à jour le statut de la facture associée si elle existe
        $invoice = Invoice::where('ID_RESERVATION', $request->reservation_id)->first();
        if ($invoice) {
            $invoice->STATUS = 'paid';
            $invoice->save();
        }

        DB::commit();

        return response()->json([
            'message' => 'Payment processed successfully',
            'payment_id' => $payment->ID_PAYMENT,
            'amount' => $payment->AMOUNT,
            'method' => $payment->METHOD
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Payment processing failed',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
