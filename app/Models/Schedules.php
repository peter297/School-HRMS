<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedules extends Model
{
    /** @use HasFactory<\Database\Factories\SchedulesFactory> */
    use HasFactory;

    protected $fillable = [
        'staff_type',
        'expected_in',
        'expected_out',
        'grace_minutes',
    ];
    
}
