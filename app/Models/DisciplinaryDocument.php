<?php
// app/Models/DisciplinaryDocument.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisciplinaryDocument extends Model
{
    protected $fillable = [
        'disciplinary_case_id',
        'type',
        'warning_level',
        'document_date',
        'content',
        'response_text',
        'response_date',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'document_date' => 'date',
            'response_date' => 'date',
        ];
    }

    public function disciplinaryCase()
    {
        return $this->belongsTo(DisciplinaryCase::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'show_cause_letter'     => 'Show cause letter',
            'response_to_show_cause'=> 'Response to show cause',
            'hearing_record'        => 'Hearing record',
            'warning_letter'        => 'Warning letter',
            default                 => ucfirst($this->type),
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'show_cause_letter'      => 'yellow',
            'response_to_show_cause' => 'blue',
            'hearing_record'         => 'purple',
            'warning_letter'         => 'red',
            default                  => 'zinc',
        };
    }

    public function getWarningLevelLabelAttribute(): string
    {
        return match($this->warning_level) {
            'verbal'        => 'Verbal warning',
            'written'       => 'Written warning',
            'final_written' => 'Final written warning',
            'dismissal'     => 'Dismissal',
            'none'          => '—',
            default         => ucfirst($this->warning_level),
        };
    }
}
