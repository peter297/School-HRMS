<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; color: #374151; margin: 0; padding: 0; background: #f9fafb; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .header { background: #065f46; padding: 32px 40px; }
        .header h1 { color: #ffffff; margin: 0; font-size: 20px; }
        .header p { color: #6ee7b7; margin: 4px 0 0; font-size: 13px; }
        .body { padding: 32px 40px; }
        .body h2 { font-size: 16px; color: #065f46; margin-top: 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
        .detail-row:last-child { border-bottom: none; }
        .label { color: #6b7280; }
        .value { color: #111827; font-weight: 500; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 9999px; font-size: 12px; background: #d1fae5; color: #065f46; }
        .footer { background: #f9fafb; padding: 20px 40px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #f3f4f6; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Al-Ameen Academy HRMS</h1>
            <p>Leave Management System</p>
        </div>
        <div class="body">
            <h2>✓ Leave Approved</h2>
            <p style="font-size:14px; color:#4b5563;">
                Dear {{ $leave->employee->first_name }},<br><br>
                Great news! Your leave application has been <strong>approved by HR</strong>.
                Please make the necessary arrangements before your leave begins.
            </p>

            <div style="background:#f0fdf4; border-radius:6px; padding:16px; margin:20px 0; border-left: 4px solid #059669;">
                <div class="detail-row">
                    <span class="label">Leave type</span>
                    <span class="value">{{ $leave->leaveType->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">From</span>
                    <span class="value">{{ $leave->start_date->format('d M Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">To</span>
                    <span class="value">{{ $leave->end_date->format('d M Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Working days</span>
                    <span class="value">{{ $leave->duration_label }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Approved by</span>
                    <span class="value">{{ $leave->approvedBy?->name ?? 'HR' }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Approved on</span>
                    <span class="value">{{ $leave->approved_at?->format('d M Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Status</span>
                    <span class="value"><span class="badge">Approved</span></span>
                </div>
            </div>

            @php
                $hrApproval = $leave->approvals->where('stage', 'hr')->first();
            @endphp
            @if($hrApproval?->notes)
                <div style="background:#f9fafb; border-radius:6px; padding:12px 16px; margin-top:16px; font-size:13px; color:#4b5563;">
                    <strong>Note from HR:</strong> {{ $hrApproval->notes }}
                </div>
            @endif

            <p style="font-size:13px; color:#6b7280; margin-top:20px;">
                Please ensure your duties are properly handed over before your leave commences.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Al-Ameen Academy. This is an automated message — please do not reply.
        </div>
    </div>
</body>
</html>
