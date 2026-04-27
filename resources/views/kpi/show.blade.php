@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3>KPI Report - {{ $employee->fullname }}</h3>
            <p class="text-muted">{{ $employee->department->name }} • {{ $employee->role?->title }}</p>
        </div>
        <div>
            <a href="{{ route('kpi.trend', $employee->id) }}" class="btn btn-sm btn-outline-success">
                <i class="bi bi-graph-up"></i> View Trend
            </a>
            <a href="{{ route('reports.export-pdf', $employee->id) }}?period={{ $period }}" class="btn btn-sm btn-outline-primary" target="_blank">
                <i class="bi bi-file-earmark-pdf"></i> Export PDF
            </a>
            <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="container-fluid">
        <!-- Period and Summary -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="text-muted mb-2">Period</h6>
                                <div class="input-group input-group-sm">
                                    <input type="month" id="periodSelect" class="form-control" value="{{ $period }}" onchange="changePeriod()">
                                </div>
                            </div>
                            <div class="col-md-2 text-center">
                                <h6 class="text-muted mb-2">Composite Score</h6>
                                <h2 class="mb-0">
                                    <span class="text-{{ 
                                        ($kpiRecords->first()?->composite_score ?? 0) >= 90 ? 'success' : 
                                        (($kpiRecords->first()?->composite_score ?? 0) >= 75 ? 'info' : 
                                        (($kpiRecords->first()?->composite_score ?? 0) >= 60 ? 'warning' : 'danger'))
                                    }}">
                                        {{ round($kpiRecords->first()?->composite_score ?? 0, 2) }}/100
                                    </span>
                                </h2>
                            </div>
                            <div class="col-md-2 text-center">
                                <h6 class="text-muted mb-2">Performance Level</h6>
                                <span class="badge badge-{{ 
                                    $kpiRecords->first()?->performance_level === 'excellent' ? 'success' : 
                                    ($kpiRecords->first()?->performance_level === 'good' ? 'info' : 
                                    ($kpiRecords->first()?->performance_level === 'satisfactory' ? 'warning' : 
                                    ($kpiRecords->first()?->performance_level === 'needs_improvement' ? 'warning' : 'danger')))
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $kpiRecords->first()?->performance_level ?? 'N/A')) }}
                                </span>
                            </div>
                            <div class="col-md-2 text-center">
                                <h6 class="text-muted mb-2">KPIs Achieved</h6>
                                <h4 class="mb-0">{{ $kpiRecords->where('status', 'achieved')->count() }}/{{ $kpiRecords->count() }}</h4>
                            </div>
                            <div class="col-md-2 text-center">
                                <h6 class="text-muted mb-2">Warnings</h6>
                                <h4 class="mb-0 text-warning">{{ $kpiRecords->where('status', 'warning')->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Categories -->
        @foreach($kpisByCategory as $category => $records)
        @php
            $avgScore = $records->avg(function($r) { return $r->getAchievementPercentage(); });
            // Determine color based on average
            $cardBorderColor = $avgScore >= 90 ? 'success' : ($avgScore >= 75 ? 'info' : ($avgScore >= 60 ? 'warning' : 'danger'));
        @endphp
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-left-{{ $cardBorderColor }} shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="card-title mb-0 text-dark fw-bold">
                            {{ $category }}
                        </h5>
                        <div class="d-flex align-items-center">
                            <span class="text-muted me-2 small text-uppercase fw-bold">Average Achievement</span>
                            <span class="badge bg-{{ $cardBorderColor }} rounded-pill px-3 py-2">
                                {{ round($avgScore, 1) }}%
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3 text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Metric</th>
                                        <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7" style="width: 100px;">Target</th>
                                        <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7" style="width: 100px;">Actual</th>
                                        <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7" style="width: 200px;">Achievement</th>
                                        <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7" style="width: 120px;">Status</th>
                                        <th class="text-end px-4 text-uppercase text-secondary text-xs font-weight-bolder opacity-7" style="width: 100px;">Variance</th>
                                        @if(in_array(session('role'), [\App\Constants\Roles::MASTER_ADMIN, \App\Constants\Roles::HR_ADMINISTRATOR]))
                                        <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7" style="width: 80px;">Admin</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($records as $record)
                                    <tr>
                                        <td class="px-4">
                                            <div class="d-flex flex-column">
                                                <span class="mb-1 text-dark fw-bold">{{ $record->kpi->name }}</span>
                                                <span class="text-muted small">{{ $record->kpi->unit }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-dark font-weight-bold">{{ $record->target_value }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-secondary font-weight-bold">{{ $record->actual_value }}</span>
                                        </td>
                                        <td class="align-middle">
                                            @php
                                                $achievement = $record->getAchievementPercentage();
                                            @endphp
                                            <div class="d-flex align-items-center px-2">
                                                <div class="progress flex-grow-1 me-2" style="height: 8px; border-radius: 4px; background-color: #e9ecef;">
                                                    <div class="progress-bar {{ $achievement >= 100 ? 'bg-success' : ($achievement >= 80 ? 'bg-warning' : 'bg-danger') }}" 
                                                         role="progressbar"
                                                         style="width: {{ min($achievement, 100) }}%; border-radius: 4px;">
                                                    </div>
                                                </div>
                                                <span class="small fw-bold {{ $achievement >= 100 ? 'text-success' : ($achievement >= 80 ? 'text-warning' : 'text-danger') }}">
                                                    {{ round($achievement, 1) }}%
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @switch($record->status)
                                                @case('achieved')
                                                    <span class="badge bg-light-success text-success fw-bold px-3 py-2 rounded-pill border border-success border-opacity-25">Achieved</span>
                                                    @break
                                                @case('warning')
                                                    <span class="badge bg-light-warning text-warning fw-bold px-3 py-2 rounded-pill border border-warning border-opacity-25">Warning</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-light-danger text-danger fw-bold px-3 py-2 rounded-pill border border-danger border-opacity-25">Critical</span>
                                            @endswitch
                                        </td>
                                        <td class="text-end px-4">
                                            @php
                                                $variance = $record->getVariance();
                                            @endphp
                                            <span class="fw-bold {{ $variance > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $variance > 0 ? '+' : '' }}{{ round($variance, 2) }}
                                            </span>
                                        </td>
                                        @if(in_array(session('role'), [\App\Constants\Roles::MASTER_ADMIN, \App\Constants\Roles::HR_ADMINISTRATOR]))
                                        <td class="text-center">
                                            <a href="{{ route('kpi.admin-edit', [$employee->id, $record->record->id ?? $record->id]) }}"
                                               class="btn btn-xs btn-outline-warning py-0 px-2"
                                               title="Edit manual (Admin)">
                                                <i class="bi bi-pencil-fill" style="font-size:0.75rem;"></i>
                                            </a>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Performance Review -->
        @if($performanceReview)
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">Performance Review - {{ $performanceReview->reviewed_by }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="mb-3">Strengths</h6>
                                <p>{{ $performanceReview->strengths ?? 'No strengths recorded' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="mb-3">Areas for Improvement</h6>
                                <p>{{ $performanceReview->areas_for_improvement ?? 'No improvement areas recorded' }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="mb-3">Comments</h6>
                                <p>{{ $performanceReview->comments ?? 'No comments' }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Reviewed Date: {{ $performanceReview->reviewed_date->format('d M Y') }}</small>
                            </div>
                            <div class="col-md-6 text-end">
                                <span class="badge bg-{{ $performanceReview->status === 'approved' ? 'success' : 'warning' }}">
                                    {{ ucfirst($performanceReview->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No performance review available for this period.
                </div>
            </div>
        </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('kpi.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function changePeriod() {
        const period = document.getElementById('periodSelect').value;
        window.location.href = `{{ route('kpi.show', $employee->id) }}?period=${period}`;
    }
</script>
@endsection
