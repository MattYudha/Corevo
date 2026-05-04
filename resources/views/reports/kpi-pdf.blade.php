<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>KPI_Report_{{ $record->employee->fullname }}_{{ $record->period }}</title>
    <style>
        /* Enterprise Design System for DomPDF */
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1e293b;
            line-height: 1.5;
            font-size: 10pt;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        
        /* Layout Components */
        .container {
            padding: 40px 50px;
        }
        
        /* Header Section */
        .header {
            background-color: #0f172a; /* Slate 900 */
            color: white;
            padding: 40px 50px;
            position: relative;
        }
        .header-logo {
            float: left;
            width: 180px;
        }
        .header-company {
            float: right;
            text-align: right;
        }
        .company-name {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
            color: #ffffff;
        }
        .company-info {
            font-size: 8pt;
            color: #94a3b8;
            line-height: 1.3;
        }
        .clearfix {
            clear: both;
        }
        
        /* Title & Period */
        .report-title-container {
            margin-top: 30px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }
        .report-title {
            font-size: 20pt;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }
        .report-period {
            font-size: 11pt;
            color: #64748b;
            font-weight: 500;
        }
        
        /* Employee Info Table */
        .employee-info-grid {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }
        .info-cell {
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .info-label {
            font-size: 8pt;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .info-value {
            font-size: 11pt;
            font-weight: 600;
            color: #1e293b;
        }
        
        /* Summary Section */
        .summary-section {
            margin-top: 35px;
            background-color: #f8fafc;
            border-radius: 16px;
            padding: 30px;
            text-align: center;
        }
        .summary-title {
            font-size: 10pt;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        .score-display {
            font-size: 48pt;
            font-weight: 900;
            color: #0f172a;
            line-height: 1;
            margin-bottom: 15px;
        }
        .score-suffix {
            font-size: 20pt;
            color: #94a3b8;
            font-weight: 500;
        }
        .level-badge {
            display: inline-block;
            padding: 8px 24px;
            border-radius: 99px;
            font-weight: 800;
            font-size: 10pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .level-excellent { background-color: #dcfce7; color: #166534; }
        .level-good { background-color: #dbeafe; color: #1e40af; }
        .level-satisfactory { background-color: #fef9c3; color: #854d0e; }
        .level-needs_improvement { background-color: #ffedd5; color: #9a3412; }
        .level-unsatisfactory { background-color: #fee2e2; color: #991b1b; }
        
        /* KPI Table Section */
        .section-header {
            margin-top: 40px;
            margin-bottom: 15px;
        }
        .section-title {
            font-size: 12pt;
            font-weight: 800;
            color: #0f172a;
            border-left: 4px solid #3b82f6;
            padding-left: 12px;
        }
        
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
        }
        .kpi-table th {
            text-align: left;
            font-size: 8pt;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            padding: 12px 15px;
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }
        .kpi-table td {
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .kpi-name {
            font-weight: 700;
            color: #1e293b;
            font-size: 10pt;
        }
        .kpi-desc {
            font-size: 8pt;
            color: #64748b;
            margin-top: 2px;
        }
        
        /* Progress Bar Styling */
        .progress-track {
            width: 100px;
            height: 8px;
            background-color: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            display: inline-block;
            vertical-align: middle;
            margin-right: 8px;
        }
        .progress-fill {
            height: 100%;
            background-color: #3b82f6;
        }
        .progress-fill-success { background-color: #22c55e; }
        .progress-fill-warning { background-color: #f59e0b; }
        .progress-fill-danger { background-color: #ef4444; }
        
        /* Category Groups */
        .category-row {
            background-color: #f1f5f9;
        }
        .category-name {
            font-weight: 800;
            color: #475569;
            font-size: 9pt;
            padding: 8px 15px !important;
        }
        
        /* Incidents */
        .incident-card {
            border: 1px solid #fee2e2;
            background-color: #fffafb;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 10px;
        }
        .incident-date {
            font-size: 8pt;
            font-weight: 700;
            color: #ef4444;
        }
        .incident-title {
            font-weight: 700;
            color: #991b1b;
            margin-bottom: 4px;
        }
        
        /* Signatures */
        .signature-section {
            margin-top: 60px;
            width: 100%;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .sig-line {
            border-top: 1px solid #cbd5e1;
            margin-bottom: 8px;
            margin-top: 80px;
        }
        .sig-name {
            font-weight: 700;
            color: #0f172a;
        }
        .sig-role {
            font-size: 8pt;
            color: #64748b;
        }
        
        /* Footer */
        .footer {
            position: fixed;
            bottom: 30px;
            left: 50px;
            right: 50px;
            border-top: 1px solid #f1f5f9;
            padding-top: 15px;
            text-align: center;
            font-size: 7pt;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-logo">
            @if($config->logo_path && file_exists(public_path($config->logo_path)))
                <img src="{{ public_path($config->logo_path) }}" height="50">
            @else
                {{-- Fallback text logo if file not found --}}
                <div style="font-size: 24pt; font-weight: 900; letter-spacing: -1px;">COREVO</div>
            @endif
        </div>
        <div class="header-company">
            <div class="company-name">{{ $config->company_name ?? 'ARATECHNOLOGY' }}</div>
            <div class="company-info">
                {{ $config->company_address }}<br>
                @if($config->company_phone) Tel: {{ $config->company_phone }} @endif
                @if($config->company_email) | Email: {{ $config->company_email }} @endif
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="container">
        <div class="report-title-container">
            <h1 class="report-title">Employee Performance Report</h1>
            <div class="report-period">Assessment Period: {{ \Carbon\Carbon::createFromFormat('Y-m', $record->period)->format('F Y') }}</div>
        </div>

        <table class="employee-info-grid">
            <tr>
                <td class="info-cell" width="50%">
                    <div class="info-label">Full Name</div>
                    <div class="info-value">{{ $record->employee->fullname }}</div>
                </td>
                <td class="info-cell" width="50%">
                    <div class="info-label">Employee ID</div>
                    <div class="info-value">{{ $record->employee->employee_id ?? 'ARA-'.str_pad($record->employee->id, 4, '0', STR_PAD_LEFT) }}</div>
                </td>
            </tr>
            <tr>
                <td class="info-cell">
                    <div class="info-label">Department</div>
                    <div class="info-value">{{ $record->employee->department->name ?? 'N/A' }}</div>
                </td>
                <td class="info-cell">
                    <div class="info-label">Position / Role</div>
                    <div class="info-value">{{ $record->employee->role?->title ?? 'N/A' }}</div>
                </td>
            </tr>
            <tr>
                <td class="info-cell">
                    <div class="info-label">Direct Supervisor</div>
                    <div class="info-value">{{ $record->employee->supervisor?->fullname ?? 'Management' }}</div>
                </td>
                <td class="info-cell">
                    <div class="info-label">Report Generated</div>
                    <div class="info-value">{{ now()->format('d M Y, H:i') }}</div>
                </td>
            </tr>
        </table>

        <div class="summary-section">
            <div class="summary-title">Overall Performance Summary</div>
            <div class="score-display">
                {{ number_format($record->composite_score, 1) }}<span class="score-suffix">%</span>
            </div>
            <div class="level-badge level-{{ $record->performance_level }}">
                {{ str_replace('_', ' ', strtoupper($record->performance_level)) }} PERFORMANCE
            </div>
        </div>

        <div class="section-header">
            <div class="section-title">KPI Metric Breakdown</div>
        </div>

        <table class="kpi-table">
            <thead>
                <tr>
                    <th width="50%">Metric Details</th>
                    <th width="20%" style="text-align: center;">Target</th>
                    <th width="20%" style="text-align: center;">Actual</th>
                    <th width="10%" style="text-align: right;">Score</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kpisByCategory as $category => $items)
                    <tr class="category-row">
                        <td colspan="4" class="category-name">{{ strtoupper($category) }}</td>
                    </tr>
                    @foreach($items as $item)
                        <tr>
                            <td>
                                <div class="kpi-name">{{ $item->kpi->name }}</div>
                                <div class="kpi-desc">{{ $item->kpi->description ?: 'No description provided' }}</div>
                            </td>
                            <td style="text-align: center;">
                                <div class="info-value" style="font-size: 9pt;">{{ $item->target_value }}</div>
                                <div style="font-size: 7pt; color: #94a3b8; font-weight: bold;">{{ strtoupper($item->kpi->unit) }}</div>
                            </td>
                            <td style="text-align: center;">
                                <div class="info-value" style="font-size: 9pt; color: #3b82f6;">{{ $item->actual_value }}</div>
                                <div style="font-size: 7pt; color: #94a3b8; font-weight: bold;">{{ strtoupper($item->kpi->unit) }}</div>
                            </td>
                            <td style="text-align: right;">
                                <div class="info-value" style="font-size: 10pt;">{{ round($item->getAchievementPercentage(), 1) }}%</div>
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 30px; color: #94a3b8;">
                            No KPI records found for this period.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($incidents->count() > 0)
        <div class="section-header">
            <div class="section-title" style="border-left-color: #ef4444;">Disciplinary & Incident Records</div>
        </div>
        @foreach($incidents as $incident)
            <div class="incident-card">
                <div class="incident-date">{{ $incident->incident_date->format('d M Y') }} • {{ strtoupper($incident->severity) }}</div>
                <div class="incident-title">{{ ucfirst(str_replace('_', ' ', $incident->type)) }}</div>
                <div style="font-size: 9pt; color: #475569;">{{ $incident->description }}</div>
            </div>
        @endforeach
        @endif

        <table class="signature-section">
            <tr>
                <td class="signature-box" style="padding-right: 40px;">
                    <div class="sig-line"></div>
                    <div class="sig-name">{{ $record->employee->fullname }}</div>
                    <div class="sig-role">Employee Signature</div>
                </td>
                <td width="10%"></td>
                <td class="signature-box" style="padding-left: 40px;">
                    <div class="sig-line"></div>
                    <div class="sig-name">{{ $record->employee->supervisor?->fullname ?? 'Authorized Personnel' }}</div>
                    <div class="sig-role">Manager / Supervisor Signature</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Confidential Document • Generated by COREVO Aratech ERP System • Ref: KPI/{{ $record->employee->id }}/{{ str_replace('-', '', $record->period) }}<br>
        © {{ date('Y') }} {{ $config->company_name ?? 'ARATECHNOLOGY' }}. All rights reserved.
    </div>
</body>
</html>
