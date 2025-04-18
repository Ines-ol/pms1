<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\EnsureIsAdmin;
use App\Http\Controllers\ManagerController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Routes user (signup signin)
Route::post('/signup', [UserController::class, 'signUp']);
Route::post('/signin', [UserController::class, 'signIn']);
Route::post('/signout', [UserController::class, 'signOut']);

//Routes admin(manager users)
Route::get('/listeuser', [AdminController::class, 'listUsers']);
Route::post('/createuser', [AdminController::class, 'createUser']);
Route::put('/updateuser/{userId}', [AdminController::class, 'updateUser']);
Route::delete('deleteuser/{userId}', [AdminController::class, 'deleteUser']);

Route::get('/listemaintenance', [AdminController::class, 'listMaintenanceRequests']);
Route::post('/addmaintenance', [AdminController::class, 'addMaintenanceRequest']);
Route::put('/updatemaintenance/{id}', [AdminController::class, 'updateMaintenanceRequest']);
Route::delete('/deletemaintenance/{id}', [AdminController::class, 'deleteMaintenanceRequest']);

//Route manager ( rooms , services )
    //Routes rooms
    Route::get('/listerooms', [ManagerController::class, 'listRooms']);
    Route::post('/addroom', [ManagerController::class, 'addRoom']);
    Route::put('/updateroom/{id}', [ManagerController::class, 'updateRoom']);
    Route::delete('/deleteroom/{id}', [ManagerController::class, 'deleteRoom']); 

    // Routes reservations
    Route::get('/listereservations', [ManagerController::class, 'listReservations']); 
    
    // Routes invoices 
    Route::post('/{id}/addinvoice', [ManagerController::class, 'createInvoice']);
    Route::put('/updateinvoice/{id}', [ManagerController::class, 'updateInvoice']);
    