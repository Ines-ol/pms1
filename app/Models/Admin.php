<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admin'; 
    protected $fillable = [
        'id',
        'email',
        'test',
    ];
}
