<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class client extends Model
{
    use HasFactory;

    protected $table = 'client';
    protected $primaryKey = 'ID_CLIENT';
    public $timestamps = false;
    
    protected $fillable = [
        'ID_USER',
        'ADDRESS',
        'PHONE',
        'BIRTHDAY'
    ];
    public function user()
    {
    return $this->belongsTo(User::class, 'ID_USER');
    }
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'ID_CLIENT');
    }
}
