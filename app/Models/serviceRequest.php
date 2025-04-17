<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class serviceRequest extends Model
{
    use HasFactory;

    protected $table = 'service_request'; 
    protected $primaryKey = 'ID_SERVICE_REQUEST'; 
    public $timestamps = false; 

    protected $fillable = [
        'ID_CLIENT',
        'DESCRIPTION',
        'STATUS'
    ];

    // Cast des types
    protected $casts = [
        'ID_CLIENT' => 'integer'
    ];

    // Statuts possibles
    public const STATUSES = [
        'pending',
        'in_progress',
        'completed'
    ];

    // Relation avec le client
    public function client()
    {
        return $this->belongsTo(Client::class, 'ID_CLIENT', 'ID_CLIENT');
    }
}
