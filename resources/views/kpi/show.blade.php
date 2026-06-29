@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3>KPI Report - {{ $employee->fullname }}</h3>
            <p class="text-muted">{{ $employee->department->name }} • {{ $employee->role?->title }}</p>
        </div>
        <div>
            @if(in_array(session('role'), [\App\Constants\Roles::MASTER_ADMIN, \App\Constants\Roles::HR_ADMINISTRATOR]) || (auth()->user()->employee?->role?->title ?? '') === \App\Constants\Roles::MANAGER_UNIT_HEAD)
            <button type="button" class="btn btn-sm btn-outline-warning me-2" data-bs-toggle="modal" data-bs-target="#addKPIModal">
                <i class="bi bi-plus-circle"></i> Tambah KPI Manual
            </button>
            @endif
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

<!-- Add KPI Modal -->
<div class="modal fade" id="addKPIModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <form action="{{ route('kpi.store') }}" method="POST">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                <input type="hidden" name="period" value="{{ $period }}">
                
                <div class="modal-header border-0 pb-0 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #EEF2FF;">
                            <i class="bi bi-plus-circle-fill fs-3 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark ls-tight" style="letter-spacing: -0.5px;">Tambah KPI Manual</h5>
                            <p class="mb-0 text-muted small fw-semibold">Assign metrik penilaian baru ke periode berjalan.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body py-4">
                    <div class="mb-4" id="selectKpiContainer">
                        <label for="kpi_id" class="form-label text-muted small fw-bold ls-1">PILIH METRIK KPI</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-primary"><i class="bi bi-list-stars"></i></span>
                            <select class="form-select border-start-0 ps-1 fw-semibold" id="kpi_id" name="kpi_id">
                                <option value="">-- Silakan Pilih KPI --</option>
                                @foreach($allKpis ?? [] as $k)
                                    <option value="{{ $k->id }}">[{{ $k->category }}] {{ $k->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-2 text-end">
                            <button type="button" class="btn btn-link btn-sm text-primary fw-bold text-decoration-none p-0" id="btnShowNewKpi">
                                <i class="bi bi-plus-lg"></i> Atau buat metrik baru...
                            </button>
                        </div>
                    </div>

                    <!-- New KPI Fields (Hidden by default) -->
                    <div id="newKpiContainer" class="d-none">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <label class="form-label text-muted small fw-bold ls-1 mb-0">BUAT METRIK BARU</label>
                            <button type="button" class="btn btn-link btn-sm text-secondary text-decoration-none p-0" id="btnShowSelectKpi">
                                <i class="bi bi-arrow-left"></i> Kembali pilih list
                            </button>
                        </div>
                        
                        <div class="mb-3">
                            <input type="text" class="form-control fw-semibold" name="new_kpi_name" id="new_kpi_name" placeholder="Nama Metrik (misal: Customer Satisfaction)">
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <select class="form-select small fw-semibold" name="new_kpi_category">
                                    <option value="Attendance">Attendance</option>
                                    <option value="Productivity">Productivity</option>
                                    <option value="Quality" selected>Quality</option>
                                    <option value="Behavior">Behavior</option>
                                    <option value="Leave">Leave</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <input type="text" class="form-control small fw-semibold" name="new_kpi_unit" placeholder="Satuan (%, Jam, Rp)">
                            </div>
                        </div>
                        <input type="hidden" name="is_new_kpi" id="is_new_kpi" value="0">
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <label for="add_target_value" class="form-label text-muted small fw-bold ls-1">NILAI TARGET</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-bullseye"></i></span>
                                <input type="number" step="0.01" class="form-control border-start-0 ps-1 fw-bold" id="add_target_value" name="target_value" value="100" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="add_actual_value" class="form-label text-muted small fw-bold ls-1">NILAI AKTUAL</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-warning"><i class="bi bi-lightning-charge-fill"></i></span>
                                <input type="number" step="0.01" class="form-control border-start-0 ps-1 fw-bold text-dark" id="add_actual_value" name="actual_value" required placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label for="add_notes" class="form-label text-muted small fw-bold ls-1">CATATAN PENDUKUNG (OPSIONAL)</label>
                        <textarea class="form-control" id="add_notes" name="notes" rows="3" placeholder="Tuliskan justifikasi atau keterangan nilai aktual..."></textarea>
                    </div>
                </div>
                
                <div class="modal-footer border-0 bg-light bg-opacity-50">
                    <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none px-4" data-bs-dismiss="modal">Batalkan</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="bi bi-cloud-arrow-up-fill"></i> Simpan Data KPI
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    $('#addKPIModal').appendTo('body');

    // Toggle New KPI fields
    $('#btnShowNewKpi').on('click', function() {
        $('#selectKpiContainer').addClass('d-none');
        $('#newKpiContainer').removeClass('d-none');
        $('#is_new_kpi').val('1');
        $('#kpi_id').val('').prop('required', false);
        $('#new_kpi_name').prop('required', true);
    });

    $('#btnShowSelectKpi').on('click', function() {
        $('#newKpiContainer').addClass('d-none');
        $('#selectKpiContainer').removeClass('d-none');
        $('#is_new_kpi').val('0');
        $('#kpi_id').prop('required', true);
        $('#new_kpi_name').prop('required', false);
    });
});
</script>
@endpush
@endsection
