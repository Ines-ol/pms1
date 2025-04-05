<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function viewTasks() {
        // Voir ses tâches
        return auth()->user()->tasks;
    }
}
