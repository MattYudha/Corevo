@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <div class="page-title mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3>Master KPI Configuration</h3>
                <p class="text-subtitle text-muted">Manage default targets and weights for KPIs</p>
            </div>
            <div class="col-md-6 text-md-end">
                <nav aria-label="breadcrumb" class="breadcrumb-header">
                    <ol class="breadcrumb justify-content-md-end">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Master KPI</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <style>
            .custom-pills .nav-link {
                border-radius: 50px;
                margin: 0 5px 10px 0;
                padding: 8px 24px;
                font-weight: 600;
                color: #6c757d;
                background-color: #f8f9fa;
                border: 1px solid transparent;
                transition: all 0.3s ease;
            }
            .custom-pills .nav-link.active {
                background-color: #435ebe;
                color: white;
                box-shadow: 0 4px 12px rgba(67, 94, 190, 0.25);
            }
            .custom-pills .nav-link:hover:not(.active) {
                background-color: #e9ecef;
            }
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
            .badge-soft-primary {
                background-color: rgba(67, 94, 190, 0.1);
                color: #435ebe;
                border: 1px solid rgba(67, 94, 190, 0.15);
                font-weight: 600;
            }
            .badge-soft-secondary {
                background-color: rgba(108, 117, 125, 0.1);
                color: #6c757d;
                border: 1px solid rgba(108, 117, 125, 0.15);
                font-weight: 600;
            }
            .kpi-code-text {
                font-family: 'Courier New', Courier, monospace;
                font-weight: 600;
                color: #495057;
                background: #f8f9fa;
                padding: 4px 8px;
                border-radius: 6px;
                border: 1px solid #e9ecef;
            }
            .btn-edit-target {
                border-radius: 50px;
                padding: 6px 16px;
                font-weight: 600;
                transition: all 0.2s;
            }
            .btn-edit-target:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 8px rgba(67, 94, 190, 0.2);
            }
        </style>
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <ul class="nav nav-pills custom-pills mb-4" id="kpiTabs" role="tablist">
                    @foreach($kpis->groupBy('category') as $category => $categoryKpis)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                                id="tab-{{ Str::slug($category) }}" 
                                data-bs-toggle="tab" 
                                data-bs-target="#pane-{{ Str::slug($category) }}" 
                                type="button" role="tab" 
                                aria-controls="pane-{{ Str::slug($category) }}" 
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                <i class="bi bi-folder2-open me-1"></i> {{ $category }}
                            </button>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content pt-4" id="kpiTabsContent">
                    @foreach($kpis->groupBy('category') as $category => $categoryKpis)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                            id="pane-{{ Str::slug($category) }}" 
                            role="tabpanel" 
                            aria-labelledby="tab-{{ Str::slug($category) }}">
                            
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle kpi-table">
                                    <thead>
                                        <tr>
                                            <th width="12%">Code</th>
                                            <th width="25%">KPI Name</th>
                                            <th width="25%">Assigned Roles</th>
                                            <th width="12%">Target Value</th>
                                            <th width="10%">Weight</th>
                                            <th width="16%" class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($categoryKpis as $kpi)
                                        <tr>
                                            <td><span class="kpi-code-text">{{ $kpi->code }}</span></td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $kpi->name }}</div>
                                                <small class="text-muted"><i class="bi bi-tag"></i> {{ $kpi->metric_key }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-1">
                                                    @if($kpi->roles->isNotEmpty())
                                                        @foreach($kpi->roles as $role)
                                                            <span class="badge badge-soft-primary rounded-pill px-3">{{ $role->title }}</span>
                                                        @endforeach
                                                    @else
                                                        <span class="badge badge-soft-secondary rounded-pill px-3">Global / All</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <h6 class="mb-0 fw-bold">{{ $kpi->target_value }} <small class="text-muted fw-normal ms-1">{{ $kpi->unit }}</small></h6>
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-secondary">{{ $kpi->weight }}%</span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('kpi-masters.edit', $kpi->id) }}" class="btn btn-outline-primary btn-edit-target">
                                                    <i class="bi bi-sliders me-1"></i> Adjust
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
