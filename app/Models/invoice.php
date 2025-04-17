<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class invoice extends Model
{
    protected $table = 'invoice';
    protected $primaryKey = 'ID_INVOICE';
    public $timestamps = false;

    protected $fillable = [
        'ID_RESERVATION',
        'AMOUNT',
        'STATUS',
        'INVOICE_DATE'
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'ID_RESERVATION');
    }
}
