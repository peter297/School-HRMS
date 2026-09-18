<?php
// app/Services/AppraisalKpiService.php

namespace App\Services;

class AppraisalKpiService
{
    /**
     * Return KPI definitions for a given staff type.
     */
    public function getKpisForStaffType(string $staffType): array
    {
        if (in_array($staffType, ['teacher_eye', 'teacher_upper_primary', 'teacher_junior'])) {
            return $this->teacherKpis();
        }

        return $this->adminSupportKpis();
    }

    private function teacherKpis(): array
    {
        return [
            ['key' => 'lesson_preparation',    'label' => 'Lesson preparation & delivery',       'order' => 1],
            ['key' => 'classroom_management',  'label' => 'Classroom management',                 'order' => 2],
            ['key' => 'punctuality',           'label' => 'Punctuality & attendance',             'order' => 3],
            ['key' => 'student_assessment',    'label' => 'Student assessment & feedback',        'order' => 4],
            ['key' => 'professional_conduct',  'label' => 'Professional conduct',                 'order' => 5],
            ['key' => 'parent_communication',  'label' => 'Communication with parents',           'order' => 6],
            ['key' => 'cpd_development',       'label' => 'CPD & self-development',               'order' => 7],
        ];
    }

    private function adminSupportKpis(): array
    {
        return [
            ['key' => 'punctuality',         'label' => 'Punctuality & attendance',      'order' => 1],
            ['key' => 'quality_of_work',     'label' => 'Quality of work',               'order' => 2],
            ['key' => 'communication',       'label' => 'Communication & teamwork',      'order' => 3],
            ['key' => 'job_knowledge',       'label' => 'Job knowledge',                 'order' => 4],
            ['key' => 'initiative',          'label' => 'Initiative & problem solving',  'order' => 5],
            ['key' => 'professional_conduct','label' => 'Professional conduct',          'order' => 6],
            ['key' => 'reliability',         'label' => 'Reliability & integrity',       'order' => 7],
        ];
    }

    public function ratingToScore(string $rating): int
    {
        return match($rating) {
            'exceeds_expectations' => 3,
            'meets_expectations'   => 2,
            'below_expectations'   => 1,
            default                => 0,
        };
    }
}