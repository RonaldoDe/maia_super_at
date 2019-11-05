<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRegion extends Model
{
    protected $table = 'user_region';
    public $timestamps = false;
    protected $fillable = [
        'role_user_id', 'region_id'
    ];
}
