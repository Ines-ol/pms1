<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class propertyManager extends Model
{
    use HasFactory;

    protected $table = 'property_manager';
    protected $primaryKey = 'ID_PROPERTY_MANAGER';
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'ID_USER');
    }
}
