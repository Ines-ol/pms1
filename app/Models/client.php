<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class client extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_CLIENT';
    protected $fillable = ['ID_USER', 'ADDRESS', 'PHONE'];

    public function user()
    {
        return $this->belongsTo(User::class, 'ID_USER');
    }
}
