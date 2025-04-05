<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\adminController;

Route::get('/usersList', [adminController::class, 'usersList']);
Route::post('/createAdmin', [adminController::class, 'createAdmin']);
Route::get('/check-table', function() {
    return [
        'table_exists' => \Illuminate\Support\Facades\Schema::hasTable('personal_access_tokens'),
        'columns' => \Illuminate\Support\Facades\DB::getSchemaBuilder()->getColumnListing('personal_access_tokens')
    ];
});