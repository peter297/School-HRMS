<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1a1a1a; line-height: 1.6; }
        .page { padding: 50px 60px; }
        .header { text-align: center; border-bottom: 2px solid #1e3a5f; padding-bottom: 16px; margin-bottom: 24px; }
        .school-name { font-size: 18px; font-weight: bold; color: #1e3a5f; }
        .doc-title { font-size: 14px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-top: 6px; color: #444; }
        .case-ref { font-size: 11px; color: #666; margin-top: 4px; }
        .meta { margin-bottom: 24px; }
        .meta-row { display: flex; margin-bottom: 6px; }
        .meta-label { width: 180px; font-weight: bold; color: #444; }
        .section-heading { font-weight: bold; font-size: 12px; margin: 20px 0 8px; background: #f0f4f8; padding: 6px 10px; }
        .content-box { border: 1px solid #ddd; padding: 12px; margin-bottom: 12px; min-height: 80px; }
        .outcome-box { background: #f0f4f8; border: 1px solid #1e3a5f; padding: 14px; margin: 16px 0; }
        .outcome-label { font-weight: bold; color: #1e3a5f; }
        .sig-row { display: flex; gap: 40px; margin-top: 40px; }
        .sig-block { flex: 1; }
        .sig-line { border-top: 1px solid #333; padding-top: 4px; font-size: 11px; color: #555; margin-top: 36px; }
        .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 10px; font-size: 10px; color: #888; text-align: center; }
        .confidential { color: #cc0000; font-weight: bold; font-size: 11px; text-align: right; margin-bottom: 12px; }
    </style>
</head>
<body>
<div class="page">
    <div class="confidential">STRICTLY CONFIDENTIAL</div>

    <div class="header">
        <div class="school-name">Al-Ameen Academy</div>
        <div class="doc-title">Disciplinary Hearing Record</div>
        <div class="case-ref">Case Reference: {{ $case->case_number }}</div>
    </div>

    <div class="meta">
        <div class="meta-row">
            <span class="meta-label">Hearing date:</span>
            <span>{{ $document->document_date->format('d F Y') }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Employee:</span>
            <span>{{ $case->employee->full_name }} ({{ $case->employee->staff_number }})</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Division:</span>
            <span>{{ $case->employee->division_label }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Branch:</span>
            <span>{{ $case->employee->branch_label }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Nature of matter:</span>
            <span>{{ $case->category_label }}</span>
        </div>
    </div>

    <div class="section-heading">Summary of allegations</div>
    <div class="content-box">{{ $case->description }}</div>

    <div class="section-heading">Hearing proceedings / notes</div>
    <div class="content-box">{{ $document->content }}</div>

    @if($case->outcome !== 'pending')
        <div class="outcome-box">
            <div class="outcome-label">Outcome: {{ $case->outcome_label }}</div>
            @if($case->outcome_notes)
                <p style="margin-top:6px;">{{ $case->outcome_notes }}</p>
            @endif
        </div>
    @endif

    <div class="sig-row">
        <div class="sig-block">
            <div class="sig-line">HR Representative &amp; date</div>
        </div>
        <div class="sig-block">
            <div class="sig-line">Employee signature &amp; date</div>
        </div>
        <div class="sig-block">
            <div class="sig-line">Witness &amp; date</div>
        </div>
    </div>

    <div class="footer">
        This document is strictly confidential.
        Al-Ameen Academy · Juja Road | Kitisuru | South C
    </div>
</div>
</body>
</html>