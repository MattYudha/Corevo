@extends('layouts.dashboard')

@section('content')
<div class="page-heading mb-4">
    <div class="row align-items-center">
        <div class="col-12">
            <h3 class="fw-bold">
                <i class="bi bi-calendar-plus text-primary me-2"></i> Manual Attendance Entry 
            </h3>
            <p class="text-subtitle text-muted mb-0">Use this form to log attendance for employees who failed to clock in, ensuring accurate payroll calculations.</p>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded mb-4">
    <div class="card-body p-3 p-md-5">
        <form action="{{ route('master-presences.store-presence') }}" method="POST">
            @csrf

            <div class="form-group mb-4">
                <label class="form-label fw-semibold text-secondary">Select Employee <span class="text-danger">*</span></label>
                <select name="employee_id" class="form-select rounded" required>
                    <option value="">-- Select Employee --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->fullname }} ({{ $emp->emp_code ?? '-' }})</option>
                    @endforeach
                </select>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="form-group mb-0">
                        <label class="form-label fw-semibold text-secondary">Attendance Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control rounded" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-0">
                        <label class="form-label fw-semibold text-secondary">Reference Office Location <span class="text-danger">*</span></label>
                        <select name="office_location_id" id="office_select" class="form-select rounded" required>
                            <option value="">-- Select Office --</option>
                            @foreach($offices as $off)
                                <option value="{{ $off->id }}" data-lat="{{ $off->latitude }}" data-lng="{{ $off->longitude }}">
                                    {{ $off->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row bg-light bg-opacity-50 p-3 rounded mb-4 border mx-0">
                <div class="col-sm-6 mb-3 mb-sm-0">
                    <small class="text-muted d-block fw-semibold mb-1">System Latitude</small>
                    <strong id="lbl_lat" class="text-primary fs-5 text-break">-</strong>
                </div>
                <div class="col-sm-6">
                    <small class="text-muted d-block fw-semibold mb-1">System Longitude</small>
                    <strong id="lbl_lng" class="text-primary fs-5 text-break">-</strong>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="form-group mb-0">
                        <label class="form-label fw-semibold text-secondary">Work Type <span class="text-danger">*</span></label>
                        <select name="work_type" class="form-select rounded" required>
                            <option value="WFO" selected>WFO (Work From Office)</option>
                            <option value="WFH">WFH (Work From Home)</option>
                            <option value="WFA">WFA (Work From Anywhere)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-0">
                        <label class="form-label fw-semibold text-secondary">Attendance Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select rounded" required>
                            <option value="present" selected>Present (On Time)</option>
                            <option value="late">Late</option>
                            <option value="leave">Leave (Authorized)</option>
                            <option value="absent">Absent (No Show)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column flex-md-row gap-3 mt-5">
                <button type="submit" class="btn btn-primary shadow-sm rounded px-5 fw-bold order-1 order-md-2">
                    <i class="bi bi-cloud-upload me-1"></i> Submit Attendance
                </button>
                <a href="{{ route('master-presences.index') }}" class="btn btn-light shadow-sm rounded px-4 fw-semibold text-secondary text-center order-2 order-md-1">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('office_select').addEventListener('change', function() {
    let selectedOption = this.options[this.selectedIndex];
    let lat = selectedOption.getAttribute('data-lat') || '-';
    let lng = selectedOption.getAttribute('data-lng') || '-';
    
    document.getElementById('lbl_lat').innerText = lat;
    document.getElementById('lbl_lng').innerText = lng;
});
</script>
@endpush
@endsection