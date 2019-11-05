<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $table = 'zone';
    public $timestamps = false;
    protected $fillable = [
        'name', 'zone_dk', 'state_id'
    ];
}
