<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManagerController extends Controller
{
    public function manageTeam() {
        // Gestion des employés seulement
        return User::where('role', Role::EMPLOYEE->value)->get();
    }
}
