<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchOffice extends Model
{
    protected $table = 'branch_offices';
    public $timestamps = false;
    protected $fillable = [
        'zone_id', 'code', 'name', 'address', 'longitude', 'latitude', 'state', 'zone_dk', 'branch_office_dk'
    ];
}
