<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplianceByBranch extends Model
{
    protected $table = 'compliance_by_branch';
    public $timestamps = false;
    protected $fillable = [
        'branch_office_id', 'goal', 'sales', 'compliance','current_month'
    ];
}
