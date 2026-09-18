{{-- resources/views/pdf/show-cause-letter.blade.php --}}

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
        .meta-label { width: 160px; font-weight: bold; color: #444; }
        .body-text { margin-bottom: 14px; text-align: justify; }
        .section-heading { font-weight: bold; margin: 20px 0 8px; text-decoration: underline; }
        .incident-box { background: #f5f5f5; border-left: 3px solid #1e3a5f; padding: 12px 16px; margin: 12px 0; }
        .signature-block { margin-top: 60px; }
        .signature-line { border-top: 1px solid #333; width: 220px; margin-top: 40px; padding-top: 6px; font-size: 11px; color: #555; }
        .footer { margin-top: 40px; border-top: 1px solid #ddd; padding-top: 10px; font-size: 10px; color: #888; text-align: center; }
        .confidential { color: #cc0000; font-weight: bold; font-size: 11px; text-align: right; margin-bottom: 12px; }
    </style>
</head>
<body>
<div class="page">
    <div class="confidential">STRICTLY CONFIDENTIAL</div>

    <div class="header">
        <div class="school-name">Al-Ameen Academy</div>
        <div class="doc-title">Show Cause Letter</div>
        <div class="case-ref">Case Reference: {{ $case->case_number }}</div>
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

    <p class="section-heading">RE: SHOW CAUSE — {{ strtoupper($case->category_label) }}</p>

    <p class="body-text">
        It has come to the attention of the management of Al-Ameen Academy that on or around
        <strong>{{ $case->incident_date->format('d F Y') }}</strong>,
        you were allegedly involved in conduct that constitutes a violation of the school's
        policies and code of conduct, specifically:
    </p>

    <div class="incident-box">
        {{ $case->description }}
    </div>

    <p class="body-text">
        The above conduct, if proven, would constitute a serious breach of your employment obligations
        and the school's code of conduct.
    </p>

    <p class="body-text">
        You are hereby required to show cause in writing why disciplinary action should not be taken
        against you. Your written response should be submitted to the Human Resources department
        within <strong>48 hours</strong> of receiving this letter.
    </p>

    <p class="body-text">
        Failure to respond within the stipulated time shall be taken to mean that you have no
        response to the allegations, and the matter will proceed accordingly.
    </p>

    <div class="signature-block">
        <p>Yours sincerely,</p>
        <div class="signature-line">
            Human Resources Department<br>
            Al-Ameen Academy
        </div>
    </div>

    <div class="footer">
        This letter is strictly confidential and intended solely for the named recipient.
        Al-Ameen Academy · Juja Road | Kitisuru | South C
    </div>
</div>
</body>
</html>