<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class LeaveTypes extends Model
{
    /** @use HasFactory<\Database\Factories\LeaveTypesFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'days_allowed',
        'is_paid',
        'requires_approval',
        'description',
        'active',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'requires_approval' => 'boolean',
        'active' => 'boolean',
    ];

    public function leaves()
    {
        return $this->hasMany(Leaves::class, 'leave_type_id');
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalances::class, 'leave_type_id');
    }
}
