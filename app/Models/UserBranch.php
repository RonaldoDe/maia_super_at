<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBranch extends Model
{
    protected $table = 'branch_office_administrator';
    public $timestamps = false;
    protected $fillable = [
        'user_id', 'branch_office_id'
    ];
}
