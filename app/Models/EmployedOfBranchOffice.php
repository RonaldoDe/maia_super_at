<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployedOfBranchOffice extends Model
{
    protected $table = 'employed_of_branch_office';
    public $timestamps = false;
    protected $fillable = [
        'name', 'last_name', 'dk_position', 'position','branch_offices_id', 'user_state_id', 'employed_dk'
    ];
}
