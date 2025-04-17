<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admin';
    protected $primaryKey = 'ID_ADMIN';
    public $timestamps = false;
    
    protected $fillable = [
        'ID_USER',
        'ADDRESS',
        'PHONE',
        'BIRTHDAY'
    ];

    // Relation avec la table user
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'ID_USER');
    }
}
