@extends('layouts.dashboard')

@section('content')

<div class="page-heading">
    <div class="row">
        <div class="col-md-6">
            <h3>Monthly Performance Recap</h3>
        </div>
        <div class="col-md-6 text-right d-flex justify-content-end align-items-center">
            <form method="GET" class="form-inline d-flex align-items-center me-3">
                <div class="form-group mb-0 me-2">
                    <label class="me-2 fw-bold text-muted" style="font-size:0.9rem;">Period:</label>
                    <input type="month" name="period" value="{{ $period }}" class="form-control form-control-sm border-0 shadow-sm" onchange="this.form.submit()">
                </div>
            </form>
            <form method="POST" action="{{ route('reports.monthly-recap.sync') }}" class="d-inline me-2" id="syncForm">
                @csrf
                <input type="hidden" name="period" value="{{ $period }}">
                <button type="button" class="btn btn-primary btn-sm btn-action shadow-sm" onclick="confirmSync()">
                    <i class="bi bi-magic me-1"></i> Sync Real-Time Data
                </button>
            </form>
            <a href="{{ route('reports.export-csv') }}?period={{ $period }}" class="btn btn-success btn-sm btn-action shadow-sm">
                <i class="bi bi-download me-1"></i> Export CSV
            </a>
        </div>
    </div>
</div>

<style>
    .kpi-table th {
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #6c757d;
        border-bottom: 2px solid #e9ecef !important;
        padding-bottom: 12px;
        font-weight: 700;
    }
    .kpi-table td {
        padding: 16px 12px;
        border-bottom: 1px solid #f1f3f5;
    }
    .badge-soft-success {
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.2);
        font-weight: 600;
    }
    .badge-soft-info {
        background-color: rgba(13, 202, 240, 0.1);
        color: #0dcaf0;
        border: 1px solid rgba(13, 202, 240, 0.2);
        font-weight: 600;
    }
    .badge-soft-warning {
        background-color: rgba(255, 193, 7, 0.1);
        color: #d39e00;
        border: 1px solid rgba(255, 193, 7, 0.2);
        font-weight: 600;
    }
    .badge-soft-danger {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.2);
        font-weight: 600;
    }
    .summary-card {
        border: none;
        border-left: 4px solid;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: transform 0.2s;
    }
    .summary-card:hover {
        transform: translateY(-3px);
    }
    .btn-action {
        border-radius: 50px;
        padding: 6px 14px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>
<div class="page-content">
    <div class="container-fluid">
        <!-- Summary Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card summary-card" style="border-left-color: #435ebe;">
                    <div class="card-body py-4">
                        <h6 class="text-muted text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Employees</h6>
                        <h2 class="mb-0 text-dark fw-bold">{{ count($kpiData) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card summary-card" style="border-left-color: #198754;">
                    <div class="card-body py-4">
                        <h6 class="text-muted text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Average Score</h6>
                        <h2 class="mb-0 text-success fw-bold">{{ count($kpiData) ? round(array_sum(array_column($kpiData, 'composite_score')) / count($kpiData), 2) : 0 }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card summary-card" style="border-left-color: #0dcaf0;">
                    <div class="card-body py-4">
                        <h6 class="text-muted text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Excellent</h6>
                        <h2 class="mb-0 text-info fw-bold">{{ count(array_filter($kpiData, fn($e) => $e['performance_level'] === 'excellent')) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card summary-card" style="border-left-color: #ffc107;">
                    <div class="card-body py-4">
                        <h6 class="text-muted text-uppercase fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Below Target</h6>
                        <h2 class="mb-0 text-warning fw-bold">{{ count(array_filter($kpiData, fn($e) => $e['performance_level'] === 'needs_improvement' || $e['performance_level'] === 'unsatisfactory')) }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0">
                        <h5 class="card-title fw-bold">Performance Ranking - {{ \Carbon\Carbon::createFromFormat('Y-m', $period)->format('F Y') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle kpi-table" id="performanceTable">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="20%">Employee</th>
                                        <th width="15%">Department</th>
                                        <th width="15%">Score</th>
                                        <th width="15%">Performance</th>
                                        <th width="8%" class="text-center">Achieved</th>
                                        <th width="8%" class="text-center">Warning</th>
                                        <th width="8%" class="text-center">Critical</th>
                                        <th width="10%" class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($kpiData as $index => $data)
                                    <tr>
                                        <td><strong>{{ $index + 1 }}</strong></td>
                                        <td>
                                            <strong>{{ $data['employee']->fullname }}</strong><br>
                                            <small class="text-muted">{{ $data['employee']->role?->title }}</small>
                                        </td>
                                        <td>{{ $data['employee']->department->name }}</td>
                                        <td>
                                            <h5 class="mb-0">
                                                <span class="badge rounded-pill px-3 py-2 fs-6 shadow-sm bg-{{ 
                                                    $data['composite_score'] >= 90 ? 'success' : 
                                                    ($data['composite_score'] >= 75 ? 'info' : 
                                                    ($data['composite_score'] >= 60 ? 'warning' : 'danger'))
                                                }}">
                                                    {{ round($data['composite_score'], 2) }}
                                                </span>
                                            </h5>
                                        </td>
                                        <td>
                                            @switch($data['performance_level'])
                                                @case('excellent')
                                                    <span class="badge badge-soft-success rounded-pill px-3">Excellent</span>
                                                    @break
                                                @case('good')
                                                    <span class="badge badge-soft-info rounded-pill px-3">Good</span>
                                                    @break
                                                @case('satisfactory')
                                                    <span class="badge badge-soft-warning rounded-pill px-3">Satisfactory</span>
                                                    @break
                                                @case('needs_improvement')
                                                    <span class="badge badge-soft-warning rounded-pill px-3">Needs Improvement</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-soft-danger rounded-pill px-3">Unsatisfactory</span>
                                            @endswitch
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-soft-success rounded-pill px-3">{{ $data['achievements'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-soft-warning rounded-pill px-3">{{ $data['warnings'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-soft-danger rounded-pill px-3">{{ $data['critical'] }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('kpi.show', $data['employee']->id) }}?period={{ $period }}" 
                                               class="btn btn-outline-info btn-sm btn-action" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('reports.export-pdf', $data['employee']->id) }}?period={{ $period }}" 
                                               class="btn btn-outline-primary btn-sm btn-action" target="_blank" title="Export PDF">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            No KPI data available for this period
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Color Legend -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3">Performance Level Guide:</h6>
                        <div class="row">
                            <div class="col-md-2">
                                <span class="badge badge-success" style="padding: 8px;">Excellent (90-100)</span>
                            </div>
                            <div class="col-md-2">
                                <span class="badge badge-info" style="padding: 8px;">Good (75-89)</span>
                            </div>
                            <div class="col-md-2">
                                <span class="badge badge-warning" style="padding: 8px;">Satisfactory (60-74)</span>
                            </div>
                            <div class="col-md-2">
                                <span class="badge badge-warning" style="padding: 8px;">Needs Improvement (45-59)</span>
                            </div>
                            <div class="col-md-2">
                                <span class="badge badge-danger" style="padding: 8px;">Unsatisfactory (<45)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmSync() {
        Swal.fire({
            title: 'Sinkronisasi Real-Time',
            text: "Sistem akan menarik data operasional terbaru (Absensi, Cuti, Tugas, dll) dan MENIMPA nilai aktual KPI. Nilai target dan bobot akan dipertahankan. Lanjutkan?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#435ebe',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Ya, Sinkronisasikan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Sinkronisasi Berjalan',
                    text: 'Mohon tunggu, sistem sedang mengakumulasikan data KPI...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                document.getElementById('syncForm').submit();
            }
        });
    }

    // Optional: Add DataTables for sorting and filtering
    $(document).ready(function() {
        $('#performanceTable').DataTable({
            pageLength: 25,
            ordering: true,
            searching: true,
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        });
    });
</script>
@endsection
