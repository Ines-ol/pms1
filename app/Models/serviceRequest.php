<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class serviceRequest extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'service_request';
    protected $primaryKey = 'ID_SERVICE_REQUEST';
    protected $fillable = [
        'ID_CLIENT',
        'DESCRIPTION',
        'STATUS'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'ID_CLIENT');
    }
}
