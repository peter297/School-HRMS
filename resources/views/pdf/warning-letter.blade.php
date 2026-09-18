<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1a1a1a; line-height: 1.6; }
        .page { padding: 50px 60px; }
        .header { text-align: center; border-bottom: 2px solid #8b0000; padding-bottom: 16px; margin-bottom: 24px; }
        .school-name { font-size: 18px; font-weight: bold; color: #1e3a5f; }
        .doc-title { font-size: 14px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-top: 6px; color: #8b0000; }
        .case-ref { font-size: 11px; color: #666; margin-top: 4px; }
        .warning-badge { background: #fff0f0; border: 1px solid #8b0000; color: #8b0000; font-weight: bold; padding: 4px 12px; display: inline-block; margin: 8px 0; font-size: 11px; }
        .meta { margin-bottom: 24px; }
        .meta-row { display: flex; margin-bottom: 6px; }
        .meta-label { width: 160px; font-weight: bold; color: #444; }
        .body-text { margin-bottom: 14px; text-align: justify; }
        .section-heading { font-weight: bold; margin: 20px 0 8px; text-decoration: underline; }
        .incident-box { background: #fff8f8; border-left: 3px solid #8b0000; padding: 12px 16px; margin: 12px 0; }
        .acknowledgement { margin-top: 40px; border: 1px solid #ccc; padding: 16px; }
        .ack-title { font-weight: bold; margin-bottom: 8px; }
        .ack-line { border-top: 1px solid #333; width: 200px; margin-top: 32px; padding-top: 4px; font-size: 11px; color: #555; display: inline-block; }
        .signature-block { margin-top: 40px; }
        .signature-line { border-top: 1px solid #333; width: 220px; margin-top: 40px; padding-top: 6px; font-size: 11px; color: #555; }
        .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 10px; font-size: 10px; color: #888; text-align: center; }
        .confidential { color: #cc0000; font-weight: bold; font-size: 11px; text-align: right; margin-bottom: 12px; }
    </style>
</head>
<body>
<div class="page">
    <div class="confidential">STRICTLY CONFIDENTIAL</div>

    <div class="header">
        <div class="school-name">Al-Ameen Academy</div>
        <div class="doc-title">{{ $document->warning_level_label }}</div>
        <div class="case-ref">Case Reference: {{ $case->case_number }}</div>
        <div class="warning-badge">{{ strtoupper($document->warning_level_label) }}</div>
    </div>

    <div class="meta">
        <div class="meta-row">
            <span class="meta-label">Date:</span>
            <span>{{ $document->document_date->format('d F Y') }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">To:</span>
            <span>{{ $case->employee->full_name }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Staff number:</span>
            <span>{{ $case->employee->staff_number }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Division:</span>
            <span>{{ $case->employee->division_label }}</span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Branch:</span>
            <span>{{ $case->employee->branch_label }}</span>
        </div>
    </div>

    <p class="body-text">Dear {{ $case->employee->first_name }},</p>

    <p class="section-heading">
        RE: {{ strtoupper($document->warning_level_label) }} — {{ strtoupper($case->category_label) }}
    </p>

    <p class="body-text">
        Further to the disciplinary proceedings conducted on
        <strong>{{ $case->outcome_date?->format('d F Y') ?? 'the date of the hearing' }}</strong>,
        and having carefully considered all the evidence and your response, the management of
        Al-Ameen Academy has determined that disciplinary action is warranted.
    </p>

    <p class="body-text">The nature of the misconduct/breach was as follows:</p>

    <div class="incident-box">
        {{ $case->description }}
    </div>

    @if($document->content)
        <p class="body-text">{{ $document->content }}</p>
    @endif

    @if($document->warning_level === 'dismissal')
        <p class="body-text">
            As a result of the foregoing, your employment with Al-Ameen Academy is hereby
            <strong>terminated with immediate effect</strong>. You are required to hand over all
            school property and complete the clearance process within 24 hours.
        </p>
    @else
        <p class="body-text">
            This letter serves as a <strong>{{ $document->warning_level_label }}</strong> and will
            remain on your personnel file. Any recurrence of this or similar misconduct may result
            in more serious disciplinary action, up to and including dismissal.
        </p>
        <p class="body-text">
            We trust that you will take this warning seriously and make every effort to meet the
            standards required of all staff at Al-Ameen Academy.
        </p>
    @endif

    <div class="signature-block">
        <p>Signed:</p>
        <div class="signature-line">
            Human Resources Department<br>
            Al-Ameen Academy
        </div>
    </div>

    {{-- Acknowledgement slip --}}
    <div class="acknowledgement">
        <div class="ack-title">Acknowledgement of Receipt</div>
        <p style="font-size:11px;">I, <strong>{{ $case->employee->full_name }}</strong>, hereby
        acknowledge receipt of this {{ $document->warning_level_label }}
        dated {{ $document->document_date->format('d F Y') }}.</p>
        <div>
            <span class="ack-line">Employee signature &amp; date</span>
        </div>
    </div>

    <div class="footer">
        This letter is strictly confidential and intended solely for the named recipient.
        Al-Ameen Academy · Juja Road | Kitisuru | South C
    </div>
</div>
</body>
</html>