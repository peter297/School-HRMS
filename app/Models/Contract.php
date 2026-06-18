<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;
use Carbon\Carbon;

class Contract extends Model
{
    /** @use HasFactory<\Database\Factories\ContractFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employees_id',
        'contract_type',
        'start_date',
        'end_date',
        'renewal_alert_days',
        'status',
        'notes',
        'created_by',
    ];

    #[Override]
    protected function casts()
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employees::class, 'employees_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helpers

    public function getContractTypeLabelAttribute()
    {
        return match ($this->contract_type) {
            'permanent' => 'Permanent',
            'fixed-term' => 'Fixed Term',
            'probation' => 'Probation',
            'part-time' => 'Part Time',
            'internship' => 'Internship',
            default => ucfirst($this->contract_type),
        };
    }

    public function getDurationAttribute(): string
    {
        if(!$this->end_date){
            return 'Indefinite';
        }

        return $this->start_date->diffForHumans($this->end_date, true);
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->end_date) {
            return null; // Indefinite contract
        }
        return now()->diffInDays($this->end_date, false);
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->end_date) {
            return false; // Indefinite contract
        }
        return $this->days_until_expiry !== null 
        && $this->days_until_expiry >= 0
        && $this->days_until_expiry <= $this->renewal_alert_days;
    }

    public function getIsExpiredAttribute(): bool
    {
        if (!$this->end_date) {
            return false; // Indefinite contract
        }
        return $this->end_date->isPast() && $this->status !== 'terminated';
    }

    public function scopeExpiringSoon($query)
    {
        return $query->whereNotNull('end_date')
            ->where('status', 'active')
            ->whereRaw('DATEDIFF(end_date, CURDATE())  BETWEEN 0 AND renewal_alert_days');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }




}
