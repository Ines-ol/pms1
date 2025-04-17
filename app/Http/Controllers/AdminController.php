<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\PropertyManager;
use App\Models\maintenancerequest;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Validation\Rules; 

class AdminController extends Controller
{
    public function listUsers()
    {
        try {
            // Récupérer tous les utilisateurs avec pagination
            $users = User::select('ID_USER', 'NAME', 'EMAIL', 'ROLE', 'created_at')
                        ->orderBy('created_at', 'desc')
                        ->get();

            return response()->json([
                'success' => true,
                'users' => $users
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des utilisateurs',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function createUser(Request $request)
    {
        try {
            // Validation des données
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:191|unique:user,EMAIL',
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'role' => 'required|in:client,employee,admin,manager',
                'address' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'position' => 'nullable|string|max:255'
            ]);

            // Création de l'utilisateur
            $user = new User();
            $user->NAME = $validatedData['name'];
            $user->EMAIL = $validatedData['email'];
            $user->PASSWORD = Hash::make($validatedData['password']);
            $user->ROLE = $validatedData['role'];
            $user->save();

            // Création du profil spécifique selon le rôle
            $profile = null;
            switch ($validatedData['role']) {
                case 'client':
                    $profile = new Client();
                    $profile->ID_USER = $user->ID_USER;
                    $profile->ADDRESS = $validatedData['address'] ?? null;
                    $profile->PHONE = $validatedData['phone'] ?? null;
                    $profile->save();
                    break;
                    
                case 'employee':
                    $profile = new Employee();
                    $profile->ID_USER = $user->ID_USER;
                    $profile->POSITION = $validatedData['position'] ?? null;
                    $profile->save();
                    break;
                    
                case 'manager':
                    $profile = new PropertyManager();
                    $profile->ID_USER = $user->ID_USER;
                    $profile->save();
                    break;
                    
                case 'admin':
                    $profile = new Admin();
                    $profile->ID_USER = $user->ID_USER;
                    $profile->save();
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur créé avec succès',
                'user' => $user,
                'profile' => $profile
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'utilisateur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateUser(Request $request, $userId)
{
    try {
        // Validation des données
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:191|unique:user,EMAIL,'.$userId.',ID_USER',
            'password' => ['sometimes', 'confirmed', Rules\Password::defaults()],
            'role' => 'sometimes|in:client,employee,admin,manager',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'birthday' => 'nullable|string|max:255'
        ]);

        // Récupérer l'utilisateur
        $user = User::findOrFail($userId);

        // Mettre à jour les champs de base
        if (isset($validatedData['name'])) {
            $user->NAME = $validatedData['name'];
        }
        if (isset($validatedData['email'])) {
            $user->EMAIL = $validatedData['email'];
        }
        if (isset($validatedData['password'])) {
            $user->PASSWORD = Hash::make($validatedData['password']);
        }
        if (isset($validatedData['role'])) {
            $user->ROLE = $validatedData['role'];
        }
        $user->save();

        // Mettre à jour le profil spécifique
        $profile = null;
        switch ($user->ROLE) {
            case 'client':
                $profile = Client::where('ID_USER', $userId)->first();
                if (!$profile) {
                    $profile = new Client();
                    $profile->ID_USER = $userId;
                }
                if (isset($validatedData['address'])) {
                    $profile->ADDRESS = $validatedData['address'];
                }
                if (isset($validatedData['phone'])) {
                    $profile->PHONE = $validatedData['phone'];
                }
                if (isset($validatedData['birthday'])) {
                    $profile->BIRTHDAY = $validatedData['birthday'];
                }
                $profile->save();
                break;
                
            case 'employee':
                $profile = Employee::where('ID_USER', $userId)->first();
                if (!$profile) {
                    $profile = new Employee();
                    $profile->ID_USER = $userId;
                }
                if (isset($validatedData['position'])) {
                    $profile->POSITION = $validatedData['position'];
                }
                if (isset($validatedData['address'])) {
                    $profile->ADDRESS = $validatedData['address'];
                }
                if (isset($validatedData['phone'])) {
                    $profile->PHONE = $validatedData['phone'];
                }
                if (isset($validatedData['birthday'])) {
                    $profile->BIRTHDAY = $validatedData['birthday'];
                }
                $profile->save();
                break;

                case 'admin':
                    $profile = Admin::where('ID_USER', $userId)->first();
                    if (!$profile) {
                        $profile = new Admin();
                        $profile->ID_USER = $userId;
                    }
                    if (isset($validatedData['address'])) {
                        $profile->ADDRESS = $validatedData['address'];
                    }
                    if (isset($validatedData['phone'])) {
                        $profile->PHONE = $validatedData['phone'];
                    }
                    if (isset($validatedData['birthday'])) {
                        $profile->BIRTHDAY = $validatedData['birthday'];
                    }
                    $profile->save();
                    break;

            case 'manager':
                $profile = PropertyManager::where('ID_USER', $userId)->first();
                if (!$profile) {
                    $profile = new PropertyManager();
                    $profile->ID_USER = $userId;
                }
                if (isset($validatedData['address'])) {
                    $profile->ADDRESS = $validatedData['address'];
                }
                if (isset($validatedData['phone'])) {
                    $profile->PHONE = $validatedData['phone'];
                }
                if (isset($validatedData['birthday'])) {
                    $profile->BIRTHDAY = $validatedData['birthday'];
                }
                $profile->save();
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur mis à jour avec succès',
            'user' => $user,
            'profile' => $profile
        ], 200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Utilisateur non trouvé'
        ], 404);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur de validation',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour de l\'utilisateur',
            'error' => $e->getMessage()
        ], 500);
    }
}
    
    public function deleteUser($userId)
{
    try {
        // Récupérer l'utilisateur
        $user = User::findOrFail($userId);

        // Supprimer le profil spécifique selon le rôle
        switch ($user->ROLE) {
            case 'client':
                Client::where('ID_USER', $userId)->delete();
                break;
                
            case 'employee':
                Employee::where('ID_USER', $userId)->delete();
                break;
                
            case 'manager':
                PropertyManager::where('ID_USER', $userId)->delete();
                break;
                
            case 'admin':
                Admin::where('ID_USER', $userId)->delete();
                break;
            }
            
        // Supprimer l'utilisateur
        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Utilisateur supprimé avec succès'
        ], 200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Utilisateur non trouvé'
        ], 404);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la suppression de l\'utilisateur',
            'error' => $e->getMessage()
        ], 500);
    }
}


// add maintenance request 

public function addMaintenanceRequest(Request $request)
{
    try {
        // Validation des données
        $validatedData = $request->validate([
            'ID_ADMIN' => 'required|exists:admin,ID_ADMIN',
            'DESCRIPTION' => 'required|string',
            'STATUS' => 'sometimes|in:pending,in_progress,completed'
        ]);

        // Création de la demande
        $maintenanceRequest = new maintenancerequest(); // Notez la minuscule
        $maintenanceRequest->ID_ADMIN = $validatedData['ID_ADMIN'];
        $maintenanceRequest->DESCRIPTION = $validatedData['DESCRIPTION'];
        $maintenanceRequest->STATUS = $validatedData['STATUS'] ?? 'pending';
        $maintenanceRequest->save();

        return response()->json([
            'success' => true,
            'message' => 'Demande de maintenance ajoutée avec succès',
            'data' => $maintenanceRequest
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur de validation',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'ajout de la demande',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function updateMaintenanceRequest(Request $request, $id)
{
    try {
        // Validation des données
        $validatedData = $request->validate([
            'ID_ADMIN' => 'sometimes|exists:admin,ID_ADMIN',
            'DESCRIPTION' => 'sometimes|string',
            'STATUS' => 'sometimes|in:pending,in_progress,completed'
        ]);

        // Récupérer la demande existante
        $maintenanceRequest = maintenancerequest::findOrFail($id);

        // Mettre à jour les champs
        if (isset($validatedData['ID_ADMIN'])) {
            $maintenanceRequest->ID_ADMIN = $validatedData['ID_ADMIN'];
        }
        
        if (isset($validatedData['DESCRIPTION'])) {
            $maintenanceRequest->DESCRIPTION = $validatedData['DESCRIPTION'];
        }
        
        if (isset($validatedData['STATUS'])) {
            $maintenanceRequest->STATUS = $validatedData['STATUS'];
        }

        $maintenanceRequest->save();

        return response()->json([
            'success' => true,
            'message' => 'Demande de maintenance mise à jour avec succès',
            'data' => $maintenanceRequest
        ], 200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Demande de maintenance non trouvée'
        ], 404);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur de validation',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour de la demande',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function deleteMaintenanceRequest($id)
{
    try {
        // Trouver la demande
        $maintenanceRequest = maintenancerequest::findOrFail($id);
        
        // Supprimer la demande
        $maintenanceRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Demande de maintenance supprimée avec succès'
        ], 200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Demande de maintenance non trouvée'
        ], 404);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la suppression de la demande',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function listMaintenanceRequests()
{
    try {
        // Récupérer toutes les demandes avec les informations de l'admin
        $requests = maintenancerequest::with(['admin' => function($query) {
            $query->select('ID_ADMIN', 'ID_USER', 'ADDRESS', 'PHONE');
        }])->get();

        // Formater la réponse
        $formattedRequests = $requests->map(function($request) {
            return [
                'id' => $request->ID_REQUEST,
                'description' => $request->DESCRIPTION,
                'status' => $request->STATUS,
                'admin' => $request->admin ? [
                    'id' => $request->admin->ID_ADMIN,
                    'user_id' => $request->admin->ID_USER,
                    'address' => $request->admin->ADDRESS,
                    'phone' => $request->admin->PHONE
                ] : null,
                'created_at' => $request->created_at
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedRequests
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la récupération des demandes',
            'error' => $e->getMessage()
        ], 500);
    }
}
}