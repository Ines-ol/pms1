<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\UserRole;

enum Role: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case EMPLOYEE = 'employee';
    case CLIENT = 'client';
}
class User extends Authenticatable
{

    use HasApiTokens, Notifiable;
    protected $table = 'user'; 
    protected $primaryKey = 'ID_USER'; // Spécifier la clé primaire
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function getAuthPassword()
{
    return $this->password;
}
}