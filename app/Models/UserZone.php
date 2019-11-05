<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserZone extends Model
{
    protected $table = 'user_zone';
    public $timestamps = false;
    protected $fillable = [
        'zone_id', 'role_user_id'
    ];
}
