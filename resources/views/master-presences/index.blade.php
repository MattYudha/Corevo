@extends('layouts.dashboard')

@section('content')
<div class="page-heading mb-4">
    <div class="row align-items-center">
        <div class="col-md-7 col-lg-8 mb-3 mb-md-0">
            <h3 class="fw-bold"><i class="bi bi-gear-fill text-primary me-2"></i> Master Presence</h3>
            <p class="text-subtitle text-muted mb-0">Manage work hours, lateness rules, and network security.</p>
        </div>
        <div class="col-md-5 col-lg-4 text-md-end">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-md-end">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('presences.index') }}" class="text-decoration-none">Presences</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Master</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<section class="section">
    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded mb-4" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error) 
                    <li><i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $error }}</li> 
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-4">
        <a href="{{ route('master-presences.create-presence') }}" class="btn btn-success shadow-sm rounded fw-semibold">
            <i class="bi bi-calendar-plus-fill me-1"></i> Add Manual Attendance
        </a>
    </div>

    <form action="{{ route('master-presences.index.update') }}" method="POST">
        @csrf
        <div class="row mb-4">
            {{-- Lateness Rules --}}
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="card shadow-sm border-0 rounded h-100">
                    <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2 px-4">
                        <h5 class="card-title mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i> Attendance Rules</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-group mb-4">
                            <label class="form-label fw-semibold text-secondary">Default Clock-In Time</label>
                            <input type="time" name="work_start_time" class="form-control rounded" value="{{ $settings['work_start_time'] }}" required>
                            <small class="text-muted">Cutoff time before lateness is counted.</small>
                        </div>
                        
                        <hr class="text-muted my-4">
                        
                        {{-- WFO Settings --}}
                        <div class="mb-3 p-3 border rounded bg-primary bg-opacity-10 border-primary border-opacity-25">
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="enable_late_wfo" value="0">
                                <input class="form-check-input toggle-late" type="checkbox" name="enable_late_wfo" value="1" id="chk_wfo" data-target="div_wfo" {{ $settings['enable_late_wfo'] == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-dark" for="chk_wfo">Enable WFO Lateness</label>
                            </div>
                            <div id="div_wfo" style="display: {{ $settings['enable_late_wfo'] == '1' ? 'block' : 'none' }};">
                                <label class="small fw-semibold text-secondary mt-2">WFO Lateness Tolerance (Minutes)</label>
                                <div class="input-group mt-1">
                                    <input type="number" name="late_threshold_wfo" class="form-control rounded-start" value="{{ $settings['late_threshold_wfo'] }}" min="0">
                                    <span class="input-group-text bg-white text-muted rounded-end">Mins</span>
                                </div>
                            </div>
                        </div>

                        {{-- WFH Settings --}}
                        <div class="mb-3 p-3 border rounded bg-success bg-opacity-10 border-success border-opacity-25">
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="enable_late_wfh" value="0">
                                <input class="form-check-input toggle-late" type="checkbox" name="enable_late_wfh" value="1" id="chk_wfh" data-target="div_wfh" {{ $settings['enable_late_wfh'] == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-dark" for="chk_wfh">Enable WFH Lateness</label>
                            </div>
                            <div id="div_wfh" style="display: {{ $settings['enable_late_wfh'] == '1' ? 'block' : 'none' }};">
                                <label class="small fw-semibold text-secondary mt-2">WFH Lateness Tolerance (Minutes)</label>
                                <div class="input-group mt-1">
                                    <input type="number" name="late_threshold_wfh" class="form-control rounded-start" value="{{ $settings['late_threshold_wfh'] }}" min="0">
                                    <span class="input-group-text bg-white text-muted rounded-end">Mins</span>
                                </div>
                            </div>
                        </div>

                        {{-- WFA Settings --}}
                        <div class="mb-0 p-3 border rounded bg-info bg-opacity-10 border-info border-opacity-25">
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="enable_late_wfa" value="0">
                                <input class="form-check-input toggle-late" type="checkbox" name="enable_late_wfa" value="1" id="chk_wfa" data-target="div_wfa" {{ $settings['enable_late_wfa'] == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-dark" for="chk_wfa">Enable WFA Lateness</label>
                            </div>
                            <div id="div_wfa" style="display: {{ $settings['enable_late_wfa'] == '1' ? 'block' : 'none' }};">
                                <label class="small fw-semibold text-secondary mt-2">WFA Lateness Tolerance (Minutes)</label>
                                <div class="input-group mt-1">
                                    <input type="number" name="late_threshold_wfa" class="form-control rounded-start" value="{{ $settings['late_threshold_wfa'] }}" min="0">
                                    <span class="input-group-text bg-white text-muted rounded-end">Mins</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Location & Network --}}
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 rounded h-100">
                    <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2 px-4">
                        <h5 class="card-title mb-0 fw-bold"><i class="bi bi-shield-lock me-2 text-primary"></i> Network & Location Security</h5>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted">
                            Configure specific IP addresses for each branch to prevent the use of third-party spoofing apps (e.g., Fake GPS).
                        </p>
                        
                        <div class="alert bg-warning bg-opacity-10 border border-warning border-opacity-50 rounded p-3 mt-4">
                            <h6 class="alert-heading fw-bold text-dark"><i class="bi bi-info-circle-fill text-warning me-1"></i> System Note:</h6>
                            <p class="mb-0 small text-secondary">WFO clock-ins are automatically rejected if the employee's IP address doesn't match the registered branch IP.</p>
                        </div>

                        <div class="d-grid mt-4 pt-2">
                            <a href="{{ route('office-locations.index') }}" class="btn btn-outline-primary py-3 fw-semibold rounded">
                                <i class="bi bi-geo-alt-fill me-2"></i> Manage Office Locations & IPs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Overtime Finance --}}
        <div class="card shadow-sm border-0 rounded mb-4">
            <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2 px-4">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-cash-stack me-2 text-primary"></i> Overtime Rate Settings</h5>
            </div>
            <div class="card-body p-4">
                <div class="form-group mb-0 w-100 w-md-50">
                    <label class="form-label fw-semibold text-secondary">Hourly Overtime Rate</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted rounded-start">Rp</span>
                        <input type="number" name="overtime_rate_per_hour" class="form-control rounded-end" value="{{ $settings['overtime_rate_per_hour'] ?? 0 }}">
                    </div>
                    <small class="text-muted">This rate is multiplied by the approved overtime hours during payroll calculation.</small>
                </div>
            </div>
        </div>

        {{-- Monthly WFO Quota --}}
        <div class="card shadow-sm border-0 rounded mb-4">
            <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2 px-4">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-building-gear me-2 text-primary"></i> Monthly WFO Quota</h5>
            </div>
            <div class="card-body p-4">
                <div class="row row-gap-3">
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="form-label fw-semibold text-secondary">Minimum WFO - Full Time (Days)</label>
                            <input type="number" name="min_wfo_full_time" class="form-control rounded" 
                                value="{{ old('min_wfo_full_time', \App\Models\Setting::where('key', 'min_wfo_full_time')->value('value') ?? 12) }}" required min="0">
                            <small class="text-muted">Full-time employees failing to meet this quota will face attendance deductions.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="form-label fw-semibold text-secondary">Minimum WFO - Part Time (Days)</label>
                            <input type="number" name="min_wfo_part_time" class="form-control rounded" 
                                value="{{ old('min_wfo_part_time', \App\Models\Setting::where('key', 'min_wfo_part_time')->value('value') ?? 6) }}" required min="0">
                            <small class="text-muted">Part-time employees failing to meet this quota will face attendance deductions.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Actions --}}
        <div class="d-flex flex-column flex-md-row justify-content-end gap-3 mt-4 mb-5">
            <a href="{{ route('presences.index') }}" class="btn btn-light shadow-sm rounded px-4 fw-semibold text-secondary order-2 order-md-1">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary shadow-sm rounded px-5 fw-bold order-1 order-md-2">
                <i class="bi bi-save me-1"></i> Save Settings
            </button>
        </div>
    </form>
</section>

<script>
    // Toggle threshold input visibility based on checkbox state
    document.querySelectorAll('.toggle-late').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            var targetId = this.getAttribute('data-target');
            document.getElementById(targetId).style.display = this.checked ? 'block' : 'none';
        });
    });
</script>
@endsection