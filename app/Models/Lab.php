<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lab extends Model
{
    protected $table = 'labs';
    public $timestamps = false;
    protected $fillable = [
        'dk', 'name', 'state_id'
    ];
}
