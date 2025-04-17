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
    \Log::info('SignIn Attempt: '.$request->email);
    
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    \Log::info('Validation passed');

    $user = User::where('EMAIL', $request->email)->first();

    if (!$user) {
        \Log::info('User not found');
        return response()->json(['success' => false, 'message' => 'Email ou mot de passe incorrect'], 401);
    }

    \Log::info('User found: '.$user->ID_USER);
    \Log::info('Input password: '.$request->password);
    \Log::info('Stored hash: '.$user->PASSWORD);

    if (!\Hash::check($request->password, $user->PASSWORD)) {
        \Log::info('Password mismatch');
        return response()->json(['success' => false, 'message' => 'Email ou mot de passe incorrect'], 401);
    }

    \Log::info('Authentication successful');
    
    return response()->json([
        'success' => true,
        'message' => 'Connexion réussie',
        'user' => [
            'id' => $user->ID_USER,
            'name' => $user->NAME,
            'email' => $user->EMAIL,
            'role' => $user->ROLE
        ]
    ]);
}
}