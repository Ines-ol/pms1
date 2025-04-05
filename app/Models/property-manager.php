<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class propertymanager extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_PROPERTY_MANAGER';
    protected $fillable = ['ID_USER'];

    public function user()
    {
        return $this->belongsTo(User::class, 'ID_USER');
    }
}
