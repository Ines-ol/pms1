<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payment extends Model
{
    use HasFactory;


    protected $table = 'payment';
    protected $primaryKey = 'ID_PAYMENT';
    public $timestamps = false;
    
    protected $fillable = [
        'ID_RESERVATION',
        'FIRST_NAME',
        'LAST_NAME',
        'CARD_NUMBER',
        'EXPIRATION_DATE',
        'CVV',
        'METHOD',
        'AMOUNT',
        'STATUS',
        'TRANSACTION_ID',
        'PAYMENT_DATE'
    ];

    protected $casts = [
        'PAYMENT_DATE' => 'datetime',
        'AMOUNT' => 'double'
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'ID_RESERVATION');
    }
}

