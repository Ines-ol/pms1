<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use App\Models\Employee;
use App\Models\PropertyManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules; 
use App\Http\Middleware\EnsureIsAdmin;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::with(['client', 'employee', 'propertyManager'])->get();
        return response()->json($users);
    }

    // Afficher un utilisateur spécifique
    public function show($id)
    {
        $user = User::with(['client', 'employee', 'propertyManager'])->findOrFail($id);
        return response()->json($user);
    }

    // Créer un nouvel utilisateur
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:user,EMAIL',
            'password' => ['required', Rules\Password::defaults()],
            'role' => 'required|in:client,employee,admin,manager',
            // Champs spécifiques selon le rôle
            'address' => 'required_if:role,client',
            'phone' => 'required_if:role,client',
            'position' => 'required_if:role,employee',
        ]);

        $user = User::create([
            'NAME' => $request->name,
            'EMAIL' => $request->email,
            'PASSWORD' => Hash::make($request->password),
            'ROLE' => $request->role,
        ]);

        // Créer le profil spécifique selon le rôle
        switch ($request->role) {
            case 'client':
                Client::create([
                    'ID_USER' => $user->ID_USER,
                    'ADDRESS' => $request->address,
                    'PHONE' => $request->phone,
                ]);
                break;
            case 'employee':
                Employee::create([
                    'ID_USER' => $user->ID_USER,
                    'POSITION' => $request->position,
                ]);
                break;
            case 'manager':
                PropertyManager::create([
                    'ID_USER' => $user->ID_USER,
                ]);
                break;
        }

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user->load(['client', 'employee', 'propertyManager'])
        ], 201);
    }

    // Mettre à jour un utilisateur
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:user,EMAIL,'.$user->ID_USER.',ID_USER',
            'password' => ['sometimes', Rules\Password::defaults()],
            'role' => 'sometimes|in:client,employee,admin,manager',
            // Champs spécifiques selon le rôle
            'address' => 'sometimes|required_if:role,client',
            'phone' => 'sometimes|required_if:role,client',
            'position' => 'sometimes|required_if:role,employee',
        ]);

        $user->NAME = $request->name ?? $user->NAME;
        $user->EMAIL = $request->email ?? $user->EMAIL;
        if ($request->has('password')) {
            $user->PASSWORD = Hash::make($request->password);
        }
        if ($request->has('role')) {
            $user->ROLE = $request->role;
        }
        $user->save();

        // Mettre à jour le profil spécifique
        switch ($user->ROLE) {
            case 'client':
                $client = $user->client ?? new Client(['ID_USER' => $user->ID_USER]);
                $client->ADDRESS = $request->address ?? $client->ADDRESS;
                $client->PHONE = $request->phone ?? $client->PHONE;
                $client->save();
                break;
            case 'employee':
                $employee = $user->employee ?? new Employee(['ID_USER' => $user->ID_USER]);
                $employee->POSITION = $request->position ?? $employee->POSITION;
                $employee->save();
                break;
            case 'manager':
                if (!$user->propertyManager) {
                    PropertyManager::create(['ID_USER' => $user->ID_USER]);
                }
                break;
        }

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->load(['client', 'employee', 'propertyManager'])
        ]);
    }

    // Supprimer un utilisateur
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}