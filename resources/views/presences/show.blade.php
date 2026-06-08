@extends ('layouts.dashboard')

@section ('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Presence Details</h3>
                    <p class="text-subtitle text-muted">Detailed view of attendance tracking and location status.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('presences.index') }}">Presences</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Details</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-lg-4 col-md-12 mb-4">
                <div class="card shadow-sm h-100 rounded-3">
                    <div class="card-header border-bottom bg-transparent pt-4 pb-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person-badge text-primary me-2"></i> Employee Profile
                        </h5>
                    </div>
                    <div class="card-body pt-4">
                        <div class="text-center mb-4">
                            @if ($presence->photo_path)
                                <div class="mb-3 position-relative d-inline-block">
                                    <img
                                        src="{{ asset('storage/' . $presence->photo_path) }}"
                                        alt="Presence Photo"
                                        class="rounded-3 shadow-sm object-fit-cover"
                                        style="width: 140px; height: 140px; border: 3px solid var(--bs-primary)"
                                    />
                                    <span
                                        class="position-absolute bottom-0 start-50 translate-middle-x badge bg-primary"
                                        style="transform: translateY(50%) !important"
                                    >
                                        Live Photo
                                    </span>
                                </div>
                            @else
                                <div class="d-flex justify-content-center mb-3">
                                    <div
                                        class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                        style="width: 80px; height: 80px"
                                    >
                                        <span class="fs-3 fw-bold">
                                            {{ substr($presence->employee->fullname ?? 'U', 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                            <h5 class="mb-1 mt-3 text-body">{{ $presence->employee->fullname ?? 'Unknown' }}</h5>
                            <p class="text-muted small mb-0">{{ $presence->employee->nik ?? '-' }}</p>
                        </div>
                        <ul class="list-group list-group-flush mt-4">
                            <li class="list-group-item bg-transparent px-0 border-secondary-subtle">
                                <small class="text-muted d-block mb-1">Department</small>
                                <span
                                    class="fw-semibold text-body fs-6"
                                    >{{ $presence->employee->department->name ?? 'N/A' }}</span
                                >
                            </li>
                            <li class="list-group-item bg-transparent px-0 border-bottom-0 pt-3">
                                <small class="text-muted d-block mb-1">Position</small>
                                <span
                                    class="fw-semibold text-body fs-6"
                                    >{{ $presence->employee->role->title ?? 'N/A' }}</span
                                >
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 col-md-12 mb-4">
                <div class="card shadow-sm h-100 rounded-3">
                    <div
                        class="card-header bg-transparent border-bottom pt-4 pb-3 d-flex justify-content-between align-items-center flex-wrap gap-2"
                    >
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clock-history text-info me-2"></i> Attendance Log
                        </h5>
                        <div>
                            @php
                                $badgeClass = match ($presence->status) {
                                    'present' => 'bg-success',
                                    'absent' => 'bg-danger',
                                    'leave' => 'bg-info text-dark',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} fs-6 px-3 py-2">{{ ucfirst($presence->status) }}</span>
                            @if ($isLate ?? false)
                                <span class="badge bg-warning text-dark fs-6 px-3 py-2 ms-1 shadow-sm">
                                    <i class="bi bi-exclamation-circle me-1"></i> Late
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <div class="row mb-4">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <small class="text-muted d-block mb-1">Working Date</small>
                                <span class="fs-5 fw-bold text-body">{{
                                    \Carbon\Carbon::parse($presence->date)->format(
                                        'l, d F Y',
                                    )
                                }}</span>
                            </div>
                            <div class="col-sm-6 text-sm-end">
                                <small class="text-muted d-block mb-1">Work Type</small>
                                <span class="badge bg-secondary fs-6">{{ $presence->work_type ?? 'WFO' }}</span>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div
                                    class="card shadow-sm border-0 border-top border-4 {{ ($isLate ?? false) ? 'border-warning' : 'border-success' }} rounded-3 h-100 mb-0"
                                >
                                    <div class="card-body p-4">
                                        <small class="text-muted d-block mb-1 fw-semibold"
                                            ><i class="bi bi-box-arrow-in-right me-1"></i> Check-In Time</small
                                        >
                                        <span class="fs-3 fw-bold text-body">
                                            {{
                                                $presence->check_in
                                                    ? \Carbon\Carbon::parse($presence->check_in)->format('H:i:s')
                                                    : '--:--:--'
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div
                                    class="card shadow-sm border-0 border-top border-4 border-info rounded-3 h-100 mb-0"
                                >
                                    <div class="card-body p-4">
                                        <small class="text-muted d-block mb-1 fw-semibold"
                                            ><i class="bi bi-box-arrow-right me-1"></i> Check-Out Time</small
                                        >
                                        <span class="fs-3 fw-bold text-body">
                                            {{
                                                $presence->check_out
                                                    ? \Carbon\Carbon::parse($presence->check_out)->format('H:i:s')
                                                    : '--:--:--'
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (in_array($presence->work_type, ['WFO', 'WFA']))
                            <h6 class="mt-5 mb-3 border-bottom pb-2 fw-bold text-body">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i> Location Information
                            </h6>
                            @if ($presence->work_type === 'WFO')
                                <p class="text-muted small mb-3">Base Office: <strong class="text-body">{{ $officeConfig['name'] ?? 'N/A' }}</strong> (Allowed Radius: {{ $officeConfig['radius'] ?? 0 }}m)</p>
                            @else
                                <p class="text-muted small mb-3">Work From Anywhere <span class="badge bg-transparent border border-secondary text-muted ms-2">No Geofence Restriction</span></p>
                            @endif
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div
                                        class="card shadow-sm border-0 border-top border-4 border-success rounded-3 h-100 mb-0"
                                    >
                                        <div class="card-header bg-transparent border-bottom py-3">
                                            <h6 class="mb-0 text-success">
                                                <i class="bi bi-geo-alt me-1"></i> Arrival Location
                                            </h6>
                                        </div>
                                        <div class="card-body py-3">
                                            @if ($presence->latitude && $presence->longitude)
                                                @if ($presence->work_type === 'WFO')
                                                    <div class="mb-3">
                                                        <small class="text-muted d-block mb-1">Geofence Status</small>
                                                        @if ($isCheckInOutOfRadius)
                                                            <span class="badge bg-danger"
                                                                ><i class="bi bi-exclamation-triangle"></i> Out of
                                                                Bounds ({{ round($checkInDistance ?? 0) }}m)</span
                                                            >
                                                        @else
                                                            <span class="badge bg-success"
                                                                ><i class="bi bi-check-circle"></i> Verified In Office ({{ round($checkInDistance ?? 0) }}m)</span
                                                            >
                                                        @endif
                                                    </div>
                                                @endif
                                                <div
                                                    class="d-flex justify-content-between align-items-center flex-wrap gap-2 p-3 border border-secondary-subtle rounded-3"
                                                >
                                                    <div>
                                                        <small class="text-muted d-block" style="font-size: 0.7rem"
                                                            >Coordinates</small
                                                        >
                                                        <code class="text-body fw-bold"
                                                            >{{ $presence->latitude }}, {{ $presence->longitude }}</code
                                                        >
                                                    </div>
                                                    <a
                                                        href="https://www.google.com/maps?q={{ $presence->latitude }},{{ $presence->longitude }}"
                                                        target="_blank"
                                                        class="btn btn-sm btn-outline-success"
                                                    >
                                                        <i class="bi bi-map"></i> Map
                                                    </a>
                                                </div>
                                            @else
                                                <div class="text-center py-4">
                                                    <i class="bi bi-geo-slash text-muted fs-3 d-block mb-2"></i>
                                                    <span class="text-muted fst-italic small"
                                                        >No location recorded</span
                                                    >
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div
                                        class="card shadow-sm border-0 border-top border-4 border-info rounded-3 h-100 mb-0"
                                    >
                                        <div class="card-header bg-transparent border-bottom py-3">
                                            <h6 class="mb-0 text-info">
                                                <i class="bi bi-geo-alt me-1"></i> Departure Location
                                            </h6>
                                        </div>
                                        <div class="card-body py-3">
                                            @if ($presence->check_out_latitude && $presence->check_out_longitude)
                                                @if ($presence->work_type === 'WFO')
                                                    <div class="mb-3">
                                                        <small class="text-muted d-block mb-1">Geofence Status</small>
                                                        @if ($isCheckOutOutOfRadius)
                                                            <span class="badge bg-danger"
                                                                ><i class="bi bi-exclamation-triangle"></i> Out of
                                                                Bounds ({{ round($checkOutDistance ?? 0) }}m)</span
                                                            >
                                                        @else
                                                            <span class="badge bg-success"
                                                                ><i class="bi bi-check-circle"></i> Verified In Office ({{ round($checkOutDistance ?? 0) }}m)</span
                                                            >
                                                        @endif
                                                    </div>
                                                @endif
                                                <div
                                                    class="d-flex justify-content-between align-items-center flex-wrap gap-2 p-3 border border-secondary-subtle rounded-3"
                                                >
                                                    <div>
                                                        <small class="text-muted d-block" style="font-size: 0.7rem"
                                                            >Coordinates</small
                                                        >
                                                        <code class="text-body fw-bold"
                                                            >{{ $presence->check_out_latitude }}, {{ $presence->check_out_longitude }}</code
                                                        >
                                                    </div>
                                                    <a
                                                        href="https://www.google.com/maps?q={{ $presence->check_out_latitude }},{{ $presence->check_out_longitude }}"
                                                        target="_blank"
                                                        class="btn btn-sm btn-outline-info"
                                                    >
                                                        <i class="bi bi-map"></i> Map
                                                    </a>
                                                </div>
                                            @else
                                                <div class="text-center py-4">
                                                    <i class="bi bi-geo-slash text-muted fs-3 d-block mb-2"></i>
                                                    <span class="text-muted fst-italic small"
                                                        >No location recorded</span
                                                    >
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent border-top pt-4 pb-4 text-end">
                        <a href="{{ route('presences.index') }}" class="btn btn-secondary px-4 fw-semibold rounded-3">
                            <i class="bi bi-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
