{{-- resources/views/pdf/appraisal.blade.php --}}

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a1a; line-height: 1.6; }
        .page { padding: 40px 50px; }
        .header { text-align: center; border-bottom: 2px solid #1e3a5f; padding-bottom: 14px; margin-bottom: 20px; }
        .school-name { font-size: 17px; font-weight: bold; color: #1e3a5f; }
        .doc-title { font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-top: 5px; color: #444; }
        .confidential { color: #cc0000; font-weight: bold; font-size: 10px; text-align: right; margin-bottom: 10px; }
        .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px 30px; margin-bottom: 20px; }
        .meta-row { display: flex; gap: 8px; }
        .meta-label { color: #666; min-width: 120px; font-weight: bold; }
        .section-title { font-size: 11px; font-weight: bold; background: #1e3a5f; color: #fff; padding: 5px 10px; margin: 16px 0 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th { background: #f0f4f8; padding: 6px 8px; text-align: left; font-size: 10px; border: 1px solid #ddd; }
        td { padding: 6px 8px; border: 1px solid #ddd; font-size: 10px; vertical-align: top; }
        tr:nth-child(even) td { background: #fafafa; }
        .rating-exceeds { color: #065f46; font-weight: bold; }
        .rating-meets    { color: #1e40af; font-weight: bold; }
        .rating-below    { color: #991b1b; font-weight: bold; }
        .score-box { border: 2px solid #1e3a5f; padding: 10px 20px; text-align: center; display: inline-block; margin: 6px 0; }
        .score-value { font-size: 22px; font-weight: bold; color: #1e3a5f; }
        .score-label { font-size: 10px; color: #666; }
        .grade-badge { display: inline-block; padding: 4px 14px; border-radius: 4px; font-size: 11px; font-weight: bold; margin-left: 16px; }
        .grade-exceeds { background: #d1fae5; color: #065f46; }
        .grade-meets    { background: #dbeafe; color: #1e40af; }
        .grade-below    { background: #fee2e2; color: #991b1b; }
        .comments-box { border: 1px solid #ddd; padding: 10px; min-height: 50px; font-size: 10px; margin-bottom: 10px; }
        .sig-row { display: flex; gap: 30px; margin-top: 30px; }
        .sig-block { flex: 1; }
        .sig-line { border-top: 1px solid #333; padding-top: 4px; font-size: 10px; color: #555; margin-top: 28px; }
        .footer { margin-top: 20px; border-top: 1px solid #ddd; padding-top: 8px; font-size: 9px; color: #999; text-align: center; }
    </style>
</head>
<body>
<div class="page">
    <div class="confidential">STRICTLY CONFIDENTIAL</div>

    <div class="header">
        <div class="school-name">Al-Ameen Academy</div>
        <div class="doc-title">{{ $appraisal->type_label }}</div>
    </div>

    {{-- Employee details --}}
    <div class="meta-grid">
        <div class="meta-row">
            <span class="meta-label">Employee name:</span>
            <span>{{ $appraisal->employee->full_name }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Staff number:</span>
            <span>{{ $appraisal->employee->staff_number }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Division:</span>
            <span>{{ $appraisal->employee->division_label }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Branch:</span>
            <span>{{ $appraisal->employee->branch_label }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Job title:</span>
            <span>{{ $appraisal->employee->job_title ?? '—' }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Appraisal period:</span>
            <span>{{ $appraisal->period }} ({{ $appraisal->year }})</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Appraisal type:</span>
            <span>{{ $appraisal->type_label }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Date approved:</span>
            <span>{{ $appraisal->approved_at?->format('d F Y') ?? '—' }}</span>
        </div>
    </div>

    {{-- Rating scale key --}}
    <div class="section-title">Rating scale</div>
    <table>
        <tr>
            <th width="33%">Exceeds expectations (3)</th>
            <th width="33%">Meets expectations (2)</th>
            <th width="34%">Below expectations (1)</th>
        </tr>
        <tr>
            <td>Performance consistently surpasses requirements</td>
            <td>Performance meets all required standards</td>
            <td>Performance does not meet required standards</td>
        </tr>
    </table>

    {{-- KPI ratings --}}
    <div class="section-title">KPI ratings</div>
    <table>
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="40%">KPI</th>
                <th width="25%">Rating</th>
                <th width="30%">Comments</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appraisal->kpis as $kpi)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $kpi->kpi_label }}</td>
                    <td>
                        @if($kpi->rating)
                            <span class="rating-{{ str_replace('_expectations', '', $kpi->rating) }}">
                                {{ $kpi->rating_label }}
                            </span>
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $kpi->comments ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Overall score --}}
    <div class="section-title">Overall performance</div>
    <div style="padding: 10px 0;">
        <div class="score-box">
            <div class="score-value">{{ number_format($appraisal->overall_score, 2) }}</div>
            <div class="score-label">out of 3.00</div>
        </div>
        @if($appraisal->overall_grade)
            @php
                $gradeClass = match($appraisal->overall_grade) {
                    'exceeds_expectations' => 'exceeds',
                    'meets_expectations'   => 'meets',
                    default                => 'below',
                };
            @endphp
            <span class="grade-badge grade-{{ $gradeClass }}">
                {{ $appraisal->grade_label }}
            </span>
        @endif
    </div>

    {{-- Line manager comments --}}
    @if($appraisal->line_manager_comments)
        <div class="section-title">Line manager comments</div>
        <div class="comments-box">{{ $appraisal->line_manager_comments }}</div>
    @endif

    {{-- Recommendations --}}
    @if($appraisal->recommendations)
        <div class="section-title">Recommendations & development areas</div>
        <div class="comments-box">{{ $appraisal->recommendations }}</div>
    @endif

    {{-- HR comments --}}
    @if($appraisal->hr_comments)
        <div class="section-title">HR comments</div>
        <div class="comments-box">{{ $appraisal->hr_comments }}</div>
    @endif

    {{-- Signatures --}}
    <div class="sig-row">
        <div class="sig-block">
            <div class="sig-line">
                Line manager name &amp; signature<br>
                @if($appraisal->submittedBy)
                    {{ $appraisal->submittedBy->name }}
                @endif
            </div>
        </div>
        <div class="sig-block">
            <div class="sig-line">
                HR approval &amp; signature<br>
                @if($appraisal->approvedBy)
                    {{ $appraisal->approvedBy->name }}
                @endif
            </div>
        </div>
        <div class="sig-block">
            <div class="sig-line">
                Employee acknowledgement &amp; date
            </div>
        </div>
    </div>

    <div class="footer">
        This appraisal is strictly confidential.
        Al-Ameen Academy · Juja Road | Kitisuru | South C ·
        Generated {{ now()->format('d F Y') }}
    </div>
</div>
</body>
</html>