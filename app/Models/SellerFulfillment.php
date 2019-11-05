<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerFulfillment extends Model
{
    protected $table = 'seller_fulfillment';
    public $timestamps = false;
    protected $fillable = [
        'employed_code', 'name', 'goal', 'sales','fulfillment', 'current_month', 'branch_offices_id', 'user_state_id'
    ];
}
