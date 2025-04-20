<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reservation extends Model
{
    use HasFactory;
    public $timestamps = false;
    
    protected $table = 'reservation'; 
    protected $primaryKey = 'ID_RESERVATION'; 
    protected $fillable = [
        'ID_CLIENT',
        'ID_ROOM',
        'START_DATE',
        'END_DATE',
        'STATUS'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'ID_ROOM');
    }
    public function client()
    {
        return $this->belongsTo(Client::class, 'ID_CLIENT');
    }
    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'ID_RESERVATION');
    }
}
