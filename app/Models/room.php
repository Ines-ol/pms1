<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class room extends Model
{
    use HasFactory;

    protected $table = 'room'; 
    protected $primaryKey = 'ID_ROOM';
    public $timestamps = false;
    protected $fillable = [
        'TYPE',
        'PRICE',
        'AVAILABLE'
    ];

    protected $casts = [
        'AVAILABLE' => 'boolean',
        'PRICE' => 'double'
    ];
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'ID_ROOM');
    }
}
