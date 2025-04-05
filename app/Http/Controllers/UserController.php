<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Enums\UserRole;

class UserController extends Controller
{
    
    public function signUp(Request $request)
{
    $validated = $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:user',
        'password' => 'required|min:8',
        'role' => 'required|in:'.implode(',', UserRole::values())
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => $validated['role']
    ]);

    $redirectTo = match($user->role) {
        UserRole::ADMIN->value => '/admin/dashboard',
        UserRole::MANAGER => '/manager/dashboard',
        UserRole::EMPLOYEE => '/employee/dashboard',
        UserRole::CLIENT => '/client/dashboard',
        default => '/'
    };

    return response()->json([
        'message' => 'Utilisateur créé',
        'redirect_to' => $redirectTo,
        'user' => $user
    ], 201);
}

   

public function signIn(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Recherche avec le bon nom de colonne (EMAIL en majuscules)
    $user = User::where('EMAIL', $request->email)->first();

    // Vérification du mot de passe
    if (!$user || !Hash::check($request->password, $user->PASSWORD)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // Création du token en spécifiant explicitement l'ID
    $token = $user->createToken('auth_token', [
        'tokenable_id' => $user->ID_USER, // Force l'ID utilisateur
        'tokenable_type' => User::class,
    ])->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
    ]);
}
}