<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class employee extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_EMPLOYEE';
    protected $fillable = ['ID_USER', 'POSITION'];

    public function user()
    {
        return $this->belongsTo(User::class, 'ID_USER');
    }
}
