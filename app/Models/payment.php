<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class payment extends Model
{
    protected $table = 'payment';
    protected $primaryKey = 'ID_PAYMENT';
    public $timestamps = false;

    protected $fillable = [
        'ID_RESERVATION',
        'AMOUNT',
        'METHOD',
        'STATUS',
        'DATE_PAYMENT'
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'ID_RESERVATION');
    }
}
