<?php
// app/Models/DisciplinaryCase.php

namespace App\Models;

use App\Models\DisciplinaryDocument;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DisciplinaryCase extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'case_number',
        'employee_id',
        'category',
        'description',
        'incident_date',
        'stage',
        'outcome',
        'outcome_notes',
        'outcome_date',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'incident_date' => 'date',
            'outcome_date'  => 'date',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function documents()
    {
        return $this->hasMany(DisciplinaryDocument::class);
    }

    public function showCauseLetter()
    {
        return $this->hasOne(DisciplinaryDocument::class)
                    ->where('type', 'show_cause_letter')
                    ->latest();
    }

    public function responseToShowCause()
    {
        return $this->hasOne(DisciplinaryDocument::class)
                    ->where('type', 'response_to_show_cause')
                    ->latest();
    }

    public function hearingRecord()
    {
        return $this->hasOne(DisciplinaryDocument::class)
                    ->where('type', 'hearing_record')
                    ->latest();
    }

    public function warningLetter()
    {
        return $this->hasOne(DisciplinaryDocument::class)
                    ->where('type', 'warning_letter')
                    ->latest();
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'misconduct'     => 'Misconduct',
            'insubordination'=> 'Insubordination',
            'absenteeism'    => 'Absenteeism',
            'performance'    => 'Poor performance',
            'harassment'     => 'Harassment',
            'theft'          => 'Theft',
            'other'          => 'Other',
            default          => ucfirst($this->category),
        };
    }

    public function getStageLabelAttribute(): string
    {
        return match($this->stage) {
            'show_cause_issued'  => 'Show cause issued',
            'response_received'  => 'Response received',
            'hearing_held'       => 'Hearing held',
            'outcome_recorded'   => 'Outcome recorded',
            'warning_issued'     => 'Warning issued',
            'closed'             => 'Closed',
            default              => ucfirst($this->stage),
        };
    }

    public function getStageColorAttribute(): string
    {
        return match($this->stage) {
            'show_cause_issued' => 'yellow',
            'response_received' => 'blue',
            'hearing_held'      => 'purple',
            'outcome_recorded'  => 'orange',
            'warning_issued'    => 'red',
            'closed'            => 'green',
            default             => 'zinc',
        };
    }

    public function getOutcomeLabelAttribute(): string
    {
        return match($this->outcome) {
            'verbal_warning'        => 'Verbal warning',
            'written_warning'       => 'Written warning',
            'final_written_warning' => 'Final written warning',
            'dismissal'             => 'Dismissal',
            'no_action'             => 'No action taken',
            'pending'               => 'Pending',
            default                 => ucfirst($this->outcome),
        };
    }

    public function getOutcomeColorAttribute(): string
    {
        return match($this->outcome) {
            'verbal_warning'        => 'yellow',
            'written_warning'       => 'orange',
            'final_written_warning' => 'red',
            'dismissal'             => 'red',
            'no_action'             => 'green',
            'pending'               => 'zinc',
            default                 => 'zinc',
        };
    }

    public static function generateCaseNumber(): string
    {
        $year  = now()->year;
        $count = static::whereYear('created_at', $year)->count() + 1;
        return "DISC-{$year}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}
