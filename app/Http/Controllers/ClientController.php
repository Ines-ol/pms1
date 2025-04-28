<?php

namespace App\Http\Controllers;

use App\Models\client;
use App\Models\reservation;
use App\Models\room;
use App\Models\User;
use App\Models\payment;
use App\Models\invoice;
use App\Models\serviceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class ClientController extends Controller
{

    public function updateClientProfile(Request $request, $clientId)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:user,EMAIL,'.$clientId.',ID_USER',
            'password' => 'sometimes|string|min:8',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:255',
            'birthday' => 'sometimes|date_format:Y-m-d',
            'update_token' => 'sometimes|string' // Token optionnel
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
    
        // Trouver le client
        $client = Client::find($clientId);
        
        if (!$client) {
            return response()->json([
                'message' => 'Client not found'
            ], 404);
        }

    
        DB::beginTransaction();
        try {
            // Mettre à jour l'utilisateur associé
            $user = User::find($client->ID_USER);
            
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
    
            // Mettre à jour le client
            if ($request->has('phone')) {
                $client->PHONE = $request->phone;
            }
            
            if ($request->has('address')) {
                $client->ADDRESS = $request->address;
            }
            
            if ($request->has('birthday')) {
                $client->BIRTHDAY = $request->birthday;
            }
            $client->save();
    
            DB::commit();
    
            return response()->json([
                'message' => 'Client profile updated successfully',
                'client_id' => $client->ID_CLIENT,
                'changes' => $request->all() // Retourne les champs modifiés
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // reservation 
    public function createReservation(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:client,ID_CLIENT',
            'room_id' => 'required|exists:room,ID_ROOM',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Check if room is available
        $room = Room::find($request->room_id);
        if (!$room->AVAILABLE) {
            return response()->json(['message' => 'Room is not available'], 400);
        }

        // Check for overlapping reservations
        $overlappingReservation = Reservation::where('ID_ROOM', $request->room_id)
            ->where(function($query) use ($request) {
                $query->whereBetween('START_DATE', [$request->start_date, $request->end_date])
                      ->orWhereBetween('END_DATE', [$request->start_date, $request->end_date])
                      ->orWhere(function($query) use ($request) {
                          $query->where('START_DATE', '<=', $request->start_date)
                                ->where('END_DATE', '>=', $request->end_date);
                      });
            })
            ->exists();

        if ($overlappingReservation) {
            return response()->json(['message' => 'Room is already booked for the selected dates'], 400);
        }

        // Create the reservation
        $reservation = Reservation::create([
            'ID_CLIENT' => $request->client_id,
            'ID_ROOM' => $request->room_id,
            'START_DATE' => $request->start_date,
            'END_DATE' => $request->end_date,
            'STATUS' => 'pending'
        ]);

        // Mark room as unavailable
        $room->AVAILABLE = false;
        $room->save();

        return response()->json([
            'message' => 'Reservation created successfully',
            'reservation' => $reservation
        ], 201);
    }
    // update reservation
    public function updateReservation(Request $request, $reservationId)
    {
        // // 1. Vérifier l'authentification
        // if (!auth()->check()) {
        //     return response()->json(['message' => 'Non authentifié'], 401);
        // }
    
        // 2. Récupérer l'utilisateur authentifié
        $user = auth()->user();
        
        // 3. Valider les données
        $validator = Validator::make($request->all(), [
            'room_id' => 'sometimes|exists:room,ID_ROOM',
            'start_date' => 'sometimes|date|after_or_equal:today',
            'end_date' => 'sometimes|date|after:start_date',
            'status' => 'sometimes|in:pending,confirmed,cancelled'
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
    
        // 4. Récupérer la réservation avec la relation client
        $reservation = Reservation::with('client')->find($reservationId);
        
        if (!$reservation) {
            return response()->json(['message' => 'Réservation non trouvée'], 404);
        }
    
        // 5. Vérifier que le client existe
        if (!$reservation->client) {
            return response()->json(['message' => 'Client associé à la réservation non trouvé'], 404);
        }
    
        // 6. Vérifier les droits d'accès
        if ($user->ID_USER !== $reservation->client->ID_USER) {
            return response()->json(['message' => 'Non autorisé à modifier cette réservation'], 403);
        }
    
        // ... reste du code inchangé ...
    }
    // Cancel a reservation
    public function cancelReservation($reservationId)
    {
        $reservation = Reservation::findOrFail($reservationId);
        
        // Only allow cancellation if reservation is pending or confirmed
        if (!in_array($reservation->STATUS, ['pending', 'confirmed'])) {
            return response()->json(['message' => 'Reservation cannot be canceled at this stage'], 400);
        }

        // Mark room as available again
        $room = $reservation->room;
        $room->AVAILABLE = true;
        $room->save();

        // Update reservation status
        $reservation->STATUS = 'cancelled';
        $reservation->save();

        return response()->json(['message' => 'Reservation cancelled successfully']);
    }

    // services   
    public function requestService(Request $request)
    {
    // Validation des données
    $validator = Validator::make($request->all(), [
        'client_id' => 'required|exists:client,ID_CLIENT',
        'description' => 'required|string|max:1000',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }

    // Création de la demande de service
    try {
        $serviceRequest = ServiceRequest::create([
            'ID_CLIENT' => $request->client_id,
            'DESCRIPTION' => $request->description,
            'STATUS' => 'pending'
        ]);

        return response()->json([
            'message' => 'Demande de service créée avec succès',
            'service_request' => $serviceRequest
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erreur lors de la création de la demande',
            'error' => $e->getMessage()
        ], 500);
    }
    }

    //paiement 

        // make paiement

public function makePayment(Request $request)
{

    $reservation = Reservation::find($request->reservation_id);
    
    if (!$reservation) {
        return response()->json([
            'message' => 'Reservation not found',
            'reservation_id' => $request->reservation_id
        ], 404);
    }
    // 1. Validation des données
    $validator = Validator::make($request->all(), [
        'reservation_id' => 'required|exists:reservation,ID_RESERVATION',
        'amount' => 'required|numeric|min:100',
        'method' => 'required|in:cash,credit_card,bank_transfer',
        'first_name' => 'required_if:method,credit_card|string|max:100',
        'last_name' => 'required_if:method,credit_card|string|max:100',
        'card_number' => 'required_if:method,credit_card|string|size:16',
        'expiration_date' => [
            'required_if:method,credit_card',
            'date_format:m/y',
            function ($attribute, $value, $fail) {
                $expiry = Carbon::createFromFormat('m/y', $value);
                if ($expiry->lessThan(Carbon::now())) {
                    $fail('The card has expired.');
                }
            }
        ],
        'cvv' => 'required_if:method,credit_card|string|size:3',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    DB::beginTransaction();
    try {
        // 2. Récupération de la réservation
        $reservation = Reservation::find($request->reservation_id);
        
        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }

        // 3. Création du paiement
        $paymentData = [
            'ID_RESERVATION' => $reservation->ID_RESERVATION,
            'AMOUNT' => $request->amount,
            'METHOD' => $request->method,
            'STATUS' => 'completed',
            'PAYMENT_DATE' => now(),
            'TRANSACTION_ID' => 'PAY-' . Str::random(16)
        ];

        if ($request->method === 'credit_card') {
            $paymentData = array_merge($paymentData, [
                'FIRST_NAME' => $request->first_name,
                'LAST_NAME' => $request->last_name,
                'CARD_NUMBER' => Crypt::encryptString($request->card_number),
                'EXPIRATION_DATE' => $request->expiration_date,
                'CVV' => Crypt::encryptString($request->cvv)
            ]);
        }

        $payment = Payment::create($paymentData);

        // 4. Mise à jour de la réservation
        $reservation->update(['STATUS' => 'confirmed']);

        // 5. Mise à jour de la chambre si nécessaire
        if ($reservation->ID_ROOM) {
            Room::where('ID_ROOM', $reservation->ID_ROOM)
               ->update(['AVAILABLE' => false]);
        }

        // 6. Création/Mise à jour de la facture
        $invoice = Invoice::updateOrCreate(
            ['ID_RESERVATION' => $reservation->ID_RESERVATION],
            [
                'AMOUNT' => $request->amount,
                'STATUS' => 'paid',
                'CREATED_AT' => now()
            ]
        );

        DB::commit();

        return response()->json([
            'message' => 'Payment processed successfully',
            'payment_id' => $payment->ID_PAYMENT,
            'transaction_id' => $payment->TRANSACTION_ID,
            'reservation_status' => 'confirmed',
            'invoice_id' => $invoice->ID_INVOICE ?? null
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Payment processing failed',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
