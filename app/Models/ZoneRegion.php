<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoneRegion extends Model
{
    protected $table = 'zone_region';
    public $timestamps = false;
    protected $fillable = [
        'region_id', 'zone_id'
    ];
}
