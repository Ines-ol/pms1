<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class maintenancerequest extends Model
{
    protected $table = 'maintenance_request';
    protected $primaryKey = 'ID_REQUEST';
    public $timestamps = false;
    
    protected $fillable = [
        'ID_ADMIN',
        'DESCRIPTION',
        'STATUS'
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'ID_ADMIN', 'ID_ADMIN');
    }
}
