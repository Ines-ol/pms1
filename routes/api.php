<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Middleware\EnsureIsAdmin;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ClientController;


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
    // Routes update compte 
    Route::put('/updatemanager/{id}', [ManagerController::class, 'updateManagerProfile']);
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

//Routes client ( compte , reservation , paiement , service )

    //update compte
    Route::put('/{clientId}/updateclient', [ClientController::class, 'updateClientProfile']);

    // Routes reservation 
    Route::get('/available', [ClientController::class, 'getAvailableRooms']);
    Route::post('/addreservation', [ClientController::class, 'createReservation']);
    Route::put('/updatereservation/{reservationId}', [ClientController::class, 'updateReservation']);
    Route::delete('/cancelreserva/{reservationId}', [ClientController::class, 'cancelReservation']);

    // Routes service 
    Route::post('/servicerequest', [ClientController::class, 'requestService']);

    // Routes payment 
    
    Route::post('/makepayment', [ClientController::class, 'makePayment']);

//Routes employee(compte , reservation , paiement , service , rooms )

    // update compte 
    Route::put('/{employeeId}/updateemployee', [EmployeeController::class, 'updateEmployeeProfile']);
    
    // liste rooms
    Route::get('/allrooms', [EmployeeController::class, 'getAllRooms']);

    // liste service 
    Route::get('/{clientId}/allservice', [EmployeeController::class, 'getClientServices']);

    //  reservation 
    Route::post('/createreserva', [EmployeeController::class, 'createReservationAsEmployee']);
    Route::delete('/deletereserva/{reservationId}', [EmployeeController::class, 'deleteReservation']);

    // paiement
    Route::post('/payments', [EmployeeController::class, 'makePayment']);