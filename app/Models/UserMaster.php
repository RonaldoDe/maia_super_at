<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMaster extends Model
{
    protected $table = 'users';
    protected $fillable = [
        'name', 'last_name', 'email', 'password', 'dni', 'recovery_code', 'user_dk', 'company_id', 'state_id'
    ];
}
