<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApproval extends Model
{
    /** @use HasFactory<\Database\Factories\LeaveApprovalFactory> */
    use HasFactory;

    protected $fillable = [
        'leave_id',
        'stage',
        'action',
        'acted_by',
        'notes',
        'task_assigned_to',
        'task_description',
        'acted_at',
    ];

    protected function casts(): array
    {
        return ['acted_at' => 'datetime'];
    }

    public function leave()
    {
        return $this->belongsTo(Leaves::class);
    }

    public function actedBy()
    {
        return $this->belongsTo(User::class, 'acted_by');
    }

    public function getStageLabelAttribute(): string
    {
        return match ($this->stage) {
            'line_manager' => 'Line Manager',
            'hr'  => 'HR',
            default => ucfirst($this->stage),
        };
    }

    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'approved' => 'green',
            'rejected' => 'red',
            'overridden' => 'yellow',
            default => 'zinc',
        };
    }
}
