<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    public $timestamps = false;
    protected $fillable = [
        'code', 'dk', 'name', 'category_id', 'labs_id', 'state_id'
    ];
}
