@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Presence Statistics</h3>
                <p class="text-subtitle text-muted">View presence statistics and reports.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('presences.index') }}">Presences</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Statistics</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
    <section class="section">
        <!-- Date Range Filter -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('presences.statistics') }}" class="row g-3">
                    @if(\App\Constants\Roles::isAdmin(session('role')))
                    <div class="col-md-4">
                        <label for="employee_id" class="form-label">Employee</label>
                        <select class="form-select" id="employee_id" name="employee_id">
                            <option value="">All Employees</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $selectedEmployeeId == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->fullname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                    @else
                    <div class="col-md-5">
                    @endif
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}" required>
                    </div>
                    @if(\App\Constants\Roles::isAdmin(session('role')))
                    <div class="col-md-3">
                    @else
                    <div class="col-md-5">
                    @endif
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Total Days</h6>
                        <h3>{{ $stats['total_days'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Present</h6>
                        <h3 class="text-success">{{ $stats['present'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Absent</h6>
                        <h3 class="text-danger">{{ $stats['absent'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Leave</h6>
                        <h3 class="text-info">{{ $stats['leave'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Late Check-ins</h6>
                        <h3 class="text-warning">{{ $stats['late_checkins'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted">Average Hours</h6>
                        <h3>{{ $stats['average_hours'] ? number_format($stats['average_hours'], 2) : 'N/A' }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-muted mb-3">Work Type Breakdown</h6>
                        <div class="d-flex justify-content-between">
                            <div>
                                <span class="badge bg-primary">WFO</span>
                                <h5 class="mt-2">{{ $stats['work_type_breakdown']['WFO'] }}</h5>
                            </div>
                            <div>
                                <span class="badge bg-secondary">WFH</span>
                                <h5 class="mt-2">{{ $stats['work_type_breakdown']['WFH'] }}</h5>
                            </div>
                            <div>
                                <span class="badge bg-info">WFA</span>
                                <h5 class="mt-2">{{ $stats['work_type_breakdown']['WFA'] }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Statistics -->
        <div class="card mt-3">
            <div class="card-body">
                <h5>Attendance Rate</h5>
                @php
                    $attendanceRate = $stats['total_days'] > 0 
                        ? ($stats['present'] / $stats['total_days']) * 100 
                        : 0;
                @endphp
                <div class="progress" style="height: 30px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: {{ $attendanceRate }}%">
                        {{ number_format($attendanceRate, 1) }}%
                    </div>
                </div>
                <small class="text-muted">Based on total days: {{ $stats['total_days'] }}</small>
            </div>
        </div>
    </section>
</div>
@endsection