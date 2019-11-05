<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'region';
    public $timestamps = false;
    protected $fillable = [
        'name', 'region_dk', 'state_id'
    ];
}
