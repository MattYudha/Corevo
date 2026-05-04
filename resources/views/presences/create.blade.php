@extends('layouts.dashboard')

@section('content')

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>New Presence</h3>
                <p class="text-subtitle text-muted">Monitor presences data.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('presences.index') }}">Presences</a></li>
                        <li class="breadcrumb-item active" aria-current="page">New Presences</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
    <section class="section">
        <div class="card">
            
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning">{{ session('warning') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card-body">

                @if (session('role') == 'HR Administrator') 
                    <form action="{{ route('presences.store') }}" method="POST">
                        @csrf
            
                        <div class="mb-3">
                            <label for="employee_id" class="form-label">Employee</label>
                            <select name="employee_id" class="form-control" id="employee_id" required>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->fullname }}</option>
                                @endforeach
                            </select>
                        </div>
            
                        <div class="mb-3">
                            <label for="check_in" class="form-label">Check In</label>
                            <input type="datetime-local" name="check_in" class="form-control datetime" id="check_in" required>
                        </div>
            
                        <div class="mb-3">
                            <label for="check_out" class="form-label">Check Out</label>
                            <input type="datetime-local" name="check_out" class="form-control datetime" id="check_out">
                        </div>
            
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" class="form-control" id="status" required>
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="leave">Leave</option>
                            </select>
                        </div>
            
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>

                @else
                    <div id="step-choose-type">
                        <h5 class="mb-3">📍 Select Today's Work Type</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card border-primary h-100" style="cursor: pointer;" onclick="selectWorkType('WFO')">
                                    <div class="card-body text-center">
                                        <span class="badge bg-primary mb-2" style="font-size: 1.2rem;">WFO</span>
                                        <h6>Work From Office</h6>
                                        <p class="text-muted small">Work from the office<br>(GPS + IP Network + Face + Fingerprint)</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-success h-100" style="cursor: pointer;" onclick="selectWorkType('WFH')">
                                    <div class="card-body text-center">
                                        <span class="badge bg-success mb-2" style="font-size: 1.2rem;">WFH</span>
                                        <h6>Work From Home</h6>
                                        <p class="text-muted small">Work from home<br>(GPS + Face + Fingerprint)</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-info h-100" style="cursor: pointer;" onclick="selectWorkType('WFA')">
                                    <div class="card-body text-center">
                                        <span class="badge bg-info mb-2" style="font-size: 1.2rem;">WFA</span>
                                        <h6>Work From Anywhere</h6>
                                        <p class="text-muted small">Work from anywhere<br>(GPS + Face + Fingerprint)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="form-wfo" style="display: none;">
                        <h5 class="mb-3">
                            <span class="badge bg-primary">WFO</span> Work From Office
                            <button type="button" class="btn btn-sm btn-outline-secondary float-end" onclick="backToChooseType()">← Change Type</button>
                        </h5>
                        
                        <form action="{{ route('presences.store') }}" method="POST" id="form-wfo-submit">
                            @csrf
                            <input type="hidden" name="work_type" value="WFO">
                            <input type="hidden" name="fingerprint" id="fingerprint-wfo">
                            <input type="hidden" name="is_mobile" id="is_mobile-wfo">
                            <input type="hidden" name="latitude" id="latitude-wfo">
                            <input type="hidden" name="longitude" id="longitude-wfo">
                            <input type="hidden" name="accuracy" id="accuracy-wfo">
                            <input type="hidden" name="photo_data" id="photo_data-wfo">

                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-building"></i> <strong>WFO Office Site</strong></label>
                                <select class="form-select" name="office_location_id" id="office-location-wfo" {{ !empty($wfoOfficeLocations) ? 'required' : 'disabled' }}>
                                    <option value="">-- Select Nearest Office --</option>
                                    @forelse($wfoOfficeLocations as $officeLocation)
                                        <option value="{{ $officeLocation['id'] }}" {{ (string) old('office_location_id', $selectedWfoOfficeLocation['id'] ?? '') === (string) $officeLocation['id'] ? 'selected' : '' }}>
                                            {{ $officeLocation['name'] }}
                                        </option>
                                    @empty
                                        <option value="">No active office location</option>
                                    @endforelse
                                </select>
                                <small class="text-muted">Select an office site to match your GPS Distance and IP Network.</small>
                            </div>

                            <div class="alert alert-info">
                                <strong>📋 WFO Validation:</strong> Select office site + GPS + IP Network + Face Verification + Fingerprint
                                <div class="small mt-2">
                                    Active work location: <strong id="wfo-office-name">{{ $selectedWfoOfficeLocation['name'] ?? 'No active office location' }}</strong>
                                    <span id="wfo-office-address-wrapper" @if(empty($selectedWfoOfficeLocation['address'])) style="display: none;" @endif>
                                        <br><span id="wfo-office-address">{{ $selectedWfoOfficeLocation['address'] ?? '' }}</span>
                                    </span>
                                    <br>Validation radius: <strong><span id="wfo-office-radius">{{ $selectedWfoOfficeLocation['radius'] ?? 0 }}</span> meters</strong>
                                </div>
                            </div>

                            <div class="card mb-3 border-primary">
                                <div class="card-body">
                                    <h6><i class="bi bi-shield-check text-primary"></i> Network Security (IP)</h6>
                                    
                                    <div id="network-status-wfo" class="mb-2">
                                        <span class="badge bg-warning">⏳ Checking Network...</span>
                                    </div>
                                    
                                    <div class="mb-2" id="network-details-wfo" style="display: none;">
                                        <ul class="list-group list-group-flush mb-2">
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-1">
                                                <small class="text-muted">Device Network IP</small>
                                                <span id="ip-display-wfo" class="badge bg-secondary">{{ request()->ip() }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-1 border-bottom-0">
                                                <small class="text-muted">Security Status</small>
                                                <span id="ip-status-text-wfo">-</span>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshNetwork(true)">
                                        <i class="bi bi-arrow-clockwise"></i> Refresh Network
                                    </button>
                                </div>
                            </div>

                            <div class="card mb-3 bg-light">
                                <div class="card-body">
                                    <h6><i class="bi bi-geo-alt"></i> GPS Location</h6>
                                    <div id="gps-status-wfo" class="mb-2">
                                        <span class="badge bg-warning">Loading GPS...</span>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">Latitude: <span id="lat-display-wfo">-</span></small><br>
                                        <small class="text-muted">Longitude: <span id="lon-display-wfo">-</span></small><br>
                                        <small class="text-muted">Distance: <span id="dist-display-wfo">-</span> meters</small><br>
                                        <small class="text-muted">Reference site: <span id="wfo-distance-office-name">{{ $selectedWfoOfficeLocation['name'] ?? '-' }}</span></small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshGPS()">
                                        <i class="bi bi-arrow-clockwise"></i> Refresh GPS
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-camera"></i> <strong>Face Verification</strong></label>
                                
                                <div class="alert alert-light-info py-2 px-3 mb-3 small border-info border-start border-3">
                                    <i class="bi bi-info-circle-fill text-info me-1"></i>
                                    <strong>Info:</strong> A photo will be automatically taken once your face is verified and saved as proof of attendance.
                                </div>

                                <div id="video-container-wfo" class="position-relative text-center bg-dark rounded-3 overflow-hidden mb-2" style="width: 100%; max-width: 400px; margin: auto;">
                                    <video id="video-wfo" autoplay muted playsinline style="width: 100%; max-height: 300px; object-fit: cover; transform: scaleX(-1);"></video>
                                    
                                    <div class="position-absolute top-50 start-50 translate-middle pe-none" style="width: 180px; height: 220px; border: 2px dashed rgba(255,255,255,0.4); border-radius: 50%;"></div>
                                </div>

                                <div id="preview-container-wfo" class="text-center mb-2" style="display: none;">
                                    <p class="text-success fw-bold small mb-2"><i class="bi bi-check-circle-fill"></i> This photo will be saved</p>
                                    <img id="preview-img-wfo" class="rounded-3 shadow-sm border border-3 border-success mb-3" style="width: 100%; max-width: 400px; max-height: 300px; object-fit: cover;">
                                    
                                    <div>
                                        <button type="button" class="btn btn-sm btn-warning rounded-pill px-4 shadow-sm" onclick="retakePhoto('wfo')">
                                            <i class="bi bi-arrow-counterclockwise"></i> Retake Photo
                                        </button>
                                    </div>
                                </div>

                                <div id="face-status-wfo" class="text-center mt-2">
                                    <span class="badge bg-secondary">Waiting for Camera...</span>
                                </div>
                            </div>

                            <div class="card mb-3 bg-light">
                                <div class="card-body text-center">
                                    <div id="fingerprint-status-wfo" class="mb-2">
                                        <span class="badge bg-warning">Loading Fingerprint...</span>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" id="btn-submit-wfo" class="btn btn-primary btn-lg w-100" disabled>
                                <i class="bi bi-check-circle"></i> Present (WFO)
                            </button>
                        </form>
                    </div>

                    <div id="form-wfh" style="display: none;">
                        <h5 class="mb-3">
                            <span class="badge bg-success">WFH</span> Work From Home
                            <button type="button" class="btn btn-sm btn-outline-secondary float-end" onclick="backToChooseType()">← Change Type</button>
                        </h5>
                        
                        <form action="{{ route('presences.store') }}" method="POST" id="form-wfh-submit">
                            @csrf
                            <input type="hidden" name="work_type" value="WFH">
                            <input type="hidden" name="fingerprint" id="fingerprint-wfh">
                            <input type="hidden" name="is_mobile" id="is_mobile-wfh">
                            <input type="hidden" name="latitude" id="latitude-wfh">
                            <input type="hidden" name="longitude" id="longitude-wfh">
                            <input type="hidden" name="accuracy" id="accuracy-wfh">
                            <input type="hidden" name="photo_data" id="photo_data-wfh">

                            <div class="alert alert-success">
                                <strong>📋 WFH Validation:</strong> GPS + Face Verification + Fingerprint
                            </div>

                            <div class="card mb-3 bg-light">
                                <div class="card-body">
                                    <h6><i class="bi bi-geo-alt"></i> GPS Location</h6>
                                    <div id="gps-status-wfh" class="mb-2">
                                        <span class="badge bg-warning">Loading GPS...</span>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">Latitude: <span id="lat-display-wfh">-</span></small><br>
                                        <small class="text-muted">Longitude: <span id="lon-display-wfh">-</span></small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="refreshGPSFree('wfh')">
                                        <i class="bi bi-arrow-clockwise"></i> Refresh GPS
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-camera"></i> <strong>Face Verification</strong></label>
                                
                                <div class="alert alert-light-info py-2 px-3 mb-3 small border-info border-start border-3">
                                    <i class="bi bi-info-circle-fill text-info me-1"></i>
                                    <strong>Info:</strong> A photo will be automatically taken once your face is verified and saved as proof of attendance.
                                </div>

                                <div id="video-container-wfh" class="position-relative text-center bg-dark rounded-3 overflow-hidden mb-2" style="width: 100%; max-width: 400px; margin: auto;">
                                    <video id="video-wfh" autoplay muted playsinline style="width: 100%; max-height: 300px; object-fit: cover; transform: scaleX(-1);"></video>
                                    
                                    <div class="position-absolute top-50 start-50 translate-middle pe-none" style="width: 180px; height: 220px; border: 2px dashed rgba(255,255,255,0.4); border-radius: 50%;"></div>
                                </div>

                                <div id="preview-container-wfh" class="text-center mb-2" style="display: none;">
                                    <p class="text-success fw-bold small mb-2"><i class="bi bi-check-circle-fill"></i> This photo will be saved</p>
                                    <img id="preview-img-wfh" class="rounded-3 shadow-sm border border-3 border-success mb-3" style="width: 100%; max-width: 400px; max-height: 300px; object-fit: cover;">
                                    
                                    <div>
                                        <button type="button" class="btn btn-sm btn-warning rounded-pill px-4 shadow-sm" onclick="retakePhoto('wfh')">
                                            <i class="bi bi-arrow-counterclockwise"></i> Retake Photo
                                        </button>
                                    </div>
                                </div>

                                <div id="face-status-wfh" class="text-center mt-2">
                                    <span class="badge bg-secondary">Waiting for Camera...</span>
                                </div>
                            </div>

                            <div class="card mb-3 bg-light">
                                <div class="card-body text-center">
                                    <div id="fingerprint-status-wfh" class="mb-2">
                                        <span class="badge bg-warning">Loading Fingerprint...</span>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" id="btn-submit-wfh" class="btn btn-success btn-lg w-100" disabled>
                                <i class="bi bi-check-circle"></i> Present (WFH)
                            </button>
                        </form>
                    </div>

                    <div id="form-wfa" style="display: none;">
                        <h5 class="mb-3">
                            <span class="badge bg-info">WFA</span> Work From Anywhere
                            <button type="button" class="btn btn-sm btn-outline-secondary float-end" onclick="backToChooseType()">← Change Type</button>
                        </h5>
                        
                        <form action="{{ route('presences.store') }}" method="POST" id="form-wfa-submit">
                            @csrf
                            <input type="hidden" name="work_type" value="WFA">
                            <input type="hidden" name="fingerprint" id="fingerprint-wfa">
                            <input type="hidden" name="is_mobile" id="is_mobile-wfa">
                            <input type="hidden" name="latitude" id="latitude-wfa">
                            <input type="hidden" name="longitude" id="longitude-wfa">
                            <input type="hidden" name="accuracy" id="accuracy-wfa">
                            <input type="hidden" name="photo_data" id="photo_data-wfa">

                            <div class="alert alert-info">
                                <strong>📋 WFA Validation:</strong> GPS + Face Verification + Fingerprint
                            </div>

                            <div class="card mb-3 bg-light">
                                <div class="card-body">
                                    <h6><i class="bi bi-geo-alt"></i> GPS Location</h6>
                                    <div id="gps-status-wfa" class="mb-2">
                                        <span class="badge bg-warning">Loading GPS...</span>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">Latitude: <span id="lat-display-wfa">-</span></small><br>
                                        <small class="text-muted">Longitude: <span id="lon-display-wfa">-</span></small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-info" onclick="refreshGPSFree('wfa')">
                                        <i class="bi bi-arrow-clockwise"></i> Refresh GPS
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-camera"></i> <strong>Face Verification</strong></label>
                                
                                <div class="alert alert-light-info py-2 px-3 mb-3 small border-info border-start border-3">
                                    <i class="bi bi-info-circle-fill text-info me-1"></i>
                                    <strong>Info:</strong> A photo will be automatically taken once your face is verified and saved as proof of attendance.
                                </div>

                                <div id="video-container-wfa" class="position-relative text-center bg-dark rounded-3 overflow-hidden mb-2" style="width: 100%; max-width: 400px; margin: auto;">
                                    <video id="video-wfa" autoplay muted playsinline style="width: 100%; max-height: 300px; object-fit: cover; transform: scaleX(-1);"></video>
                                    
                                    <div class="position-absolute top-50 start-50 translate-middle pe-none" style="width: 180px; height: 220px; border: 2px dashed rgba(255,255,255,0.4); border-radius: 50%;"></div>
                                </div>

                                <div id="preview-container-wfa" class="text-center mb-2" style="display: none;">
                                    <p class="text-success fw-bold small mb-2"><i class="bi bi-check-circle-fill"></i> This photo will be saved</p>
                                    <img id="preview-img-wfa" class="rounded-3 shadow-sm border border-3 border-success mb-3" style="width: 100%; max-width: 400px; max-height: 300px; object-fit: cover;">
                                    
                                    <div>
                                        <button type="button" class="btn btn-sm btn-warning rounded-pill px-4 shadow-sm" onclick="retakePhoto('wfa')">
                                            <i class="bi bi-arrow-counterclockwise"></i> Retake Photo
                                        </button>
                                    </div>
                                </div>

                                <div id="face-status-wfa" class="text-center mt-2">
                                    <span class="badge bg-secondary">Waiting for Camera...</span>
                                </div>
                            </div>

                            <div class="card mb-3 bg-light">
                                <div class="card-body text-center">
                                    <div id="fingerprint-status-wfa" class="mb-2">
                                        <span class="badge bg-warning">Loading Fingerprint...</span>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" id="btn-submit-wfa" class="btn btn-info btn-lg w-100" disabled>
                                <i class="bi bi-check-circle"></i> Present (WFA)
                            </button>
                        </form>
                    </div>

                @endif

            </div>
        </div>
    </section>
</div>

<script src="{{ asset('vendor/fingerprintjs/fp.min.js') }}"></script>
<script src="{{ asset('vendor/face-api/face-api.min.js') }}"></script>

<script>
    const userIp = "{{ request()->ip() }}";
    const wfoOfficeLocations = @json($wfoOfficeLocations);

    // FIX: SSID removed from WFH and WFA! Only requires GPS, Face, and Fingerprint.
    const modeState = {
        wfo: { gps: false, fingerprint: false, network: false, face: false },
        wfh: { gps: false, fingerprint: false, face: false },
        wfa: { gps: false, fingerprint: false, face: false }
    };

    let currentWorkType = null;
    let gpsWatchId = null;
    let gpsWatchIds = { wfh: null, wfa: null };
    let videoStream = null;
    let videoStreams = { wfh: null, wfa: null };
    let faceDetectionInterval = null;
    let faceDetectionIntervals = { wfh: null, wfa: null };

    // ============ WFO OFFICE & NETWORK LOGIC ============
    function getSelectedWfoOffice() {
        const select = document.getElementById('office-location-wfo');
        if (!select || !select.value) {
            return null;
        }
        const selectedId = Number(select.value);
        return wfoOfficeLocations.find((officeLocation) => Number(officeLocation.id) === selectedId) ?? null;
    }

    function refreshNetwork(isManualRefresh = false) {
        const office = getSelectedWfoOffice();
        const statusEl = document.getElementById('network-status-wfo');
        const detailsEl = document.getElementById('network-details-wfo');
        const textEl = document.getElementById('ip-status-text-wfo');

        if (isManualRefresh) {
            statusEl.style.display = 'block';
            statusEl.innerHTML = '<span class="badge bg-warning">⏳ Checking Network...</span>';
            detailsEl.style.display = 'none';
            modeState.wfo.network = false;
            checkReady('wfo');
        }

        setTimeout(() => {
            statusEl.style.display = 'none';
            detailsEl.style.display = 'block';

            if (!office) {
                textEl.innerHTML = '<span class="text-warning small"><i class="bi bi-exclamation-triangle"></i> Select office site first</span>';
                modeState.wfo.network = false;
            } else {
                const allowedIps = office.allowed_ips || [];
                
                if (allowedIps.length === 0) {
                    textEl.innerHTML = '<span class="text-info small"><i class="bi bi-info-circle"></i> Office does not restrict IPs</span>';
                    modeState.wfo.network = true;
                } else if (allowedIps.includes(userIp)) {
                    textEl.innerHTML = '<span class="text-success fw-bold small"><i class="bi bi-check-circle-fill"></i> Verified (Secure)</span>';
                    modeState.wfo.network = true;
                } else {
                    textEl.innerHTML = '<span class="text-danger fw-bold small"><i class="bi bi-x-circle-fill"></i> Unknown Network</span>';
                    modeState.wfo.network = false;
                }
            }
            checkReady('wfo');
        }, isManualRefresh ? 800 : 0);
    }

    function renderWfoOfficeDetails() {
        const office = getSelectedWfoOffice();
        const officeName = office ? office.name : 'No active office location';
        const officeAddress = office?.address ?? '';
        const officeRadius = office?.radius ?? 0;

        document.getElementById('wfo-office-name').textContent = officeName;
        document.getElementById('wfo-office-radius').textContent = officeRadius;
        document.getElementById('wfo-distance-office-name').textContent = office ? office.name : '-';

        const addressWrapper = document.getElementById('wfo-office-address-wrapper');
        const addressEl = document.getElementById('wfo-office-address');
        if (officeAddress) {
            addressEl.textContent = officeAddress;
            addressWrapper.style.display = 'inline';
        } else {
            addressEl.textContent = '';
            addressWrapper.style.display = 'none';
        }

        refreshNetwork(true);

        const latitude = document.getElementById('latitude-wfo').value;
        const longitude = document.getElementById('longitude-wfo').value;
        const accuracy = document.getElementById('accuracy-wfo').value;

        if (office && latitude && longitude) {
            updateWfoDistanceStatus(Number(latitude), Number(longitude), Number(accuracy || 0));
        } else if (!office) {
            document.getElementById('gps-status-wfo').innerHTML = '<span class="badge bg-danger">❌ No active office location</span>';
            document.getElementById('dist-display-wfo').textContent = '-';
            modeState.wfo.gps = false;
            checkReady('wfo');
        }
    }

    function calculateDistanceMeters(lat, lon, officeLat, officeLon) {
        const distDegrees = Math.sqrt(Math.pow(lat - officeLat, 2) + Math.pow(lon - officeLon, 2));
        return Math.round(distDegrees * 111320);
    }

    function updateWfoDistanceStatus(lat, lon, acc) {
        const office = getSelectedWfoOffice();
        if (!office) {
            document.getElementById('gps-status-wfo').innerHTML = '<span class="badge bg-danger">❌ No active office location</span>';
            document.getElementById('dist-display-wfo').textContent = '-';
            modeState.wfo.gps = false;
            checkReady('wfo');
            return;
        }

        document.getElementById('latitude-wfo').value = lat;
        document.getElementById('longitude-wfo').value = lon;
        document.getElementById('accuracy-wfo').value = acc;

        document.getElementById('lat-display-wfo').textContent = Number(lat).toFixed(6);
        document.getElementById('lon-display-wfo').textContent = Number(lon).toFixed(6);

        const distMeters = calculateDistanceMeters(Number(lat), Number(lon), Number(office.latitude), Number(office.longitude));
        document.getElementById('dist-display-wfo').textContent = distMeters;

        if (distMeters <= Number(office.radius)) {
            document.getElementById('gps-status-wfo').innerHTML = '<span class="badge bg-success">✅ GPS OK (' + distMeters + 'm)</span>';
            modeState.wfo.gps = true;
        } else {
            document.getElementById('gps-status-wfo').innerHTML = '<span class="badge bg-danger">❌ Too far (' + distMeters + 'm)</span>';
            modeState.wfo.gps = false;
        }

        checkReady('wfo');
    }

    // ============ UI NAVIGATION ============
    function selectWorkType(type) {
        currentWorkType = type;
        document.getElementById('step-choose-type').style.display = 'none';
        document.getElementById('form-' + type.toLowerCase()).style.display = 'block';

        if (type === 'WFO') {
            initWFO();
        } else if (type === 'WFH') {
            initWFH();
        } else if (type === 'WFA') {
            initWFA();
        }
    }

    function backToChooseType() {
        if (gpsWatchId) {
            navigator.geolocation.clearWatch(gpsWatchId);
            gpsWatchId = null;
        }

        ['wfh', 'wfa'].forEach(mode => {
            if (gpsWatchIds[mode]) {
                navigator.geolocation.clearWatch(gpsWatchIds[mode]);
                gpsWatchIds[mode] = null;
            }
            if (videoStreams[mode]) {
                videoStreams[mode].getTracks().forEach(track => track.stop());
                videoStreams[mode] = null;
            }
            if (faceDetectionIntervals[mode]) {
                clearInterval(faceDetectionIntervals[mode]);
                faceDetectionIntervals[mode] = null;
            }
        });

        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }
        if (faceDetectionInterval) {
            clearInterval(faceDetectionInterval);
            faceDetectionInterval = null;
        }

        document.getElementById('form-wfo').style.display = 'none';
        document.getElementById('form-wfh').style.display = 'none';
        document.getElementById('form-wfa').style.display = 'none';
        document.getElementById('step-choose-type').style.display = 'block';
        currentWorkType = null;
    }

    // ============ GENERIC READY CHECK ============
    function checkReady(mode) {
        const state = modeState[mode];
        let isReady = false;
        
        if (mode === 'wfo') {
            isReady = state.gps && state.fingerprint && state.network && state.face;
        } else {
            // FIX: WFH & WFA no longer need SSID!
            isReady = state.gps && state.fingerprint && state.face;
        }
        
        const button = document.getElementById('btn-submit-' + mode);
        if (button) {
            button.disabled = !isReady;
        }
        console.log(mode.toUpperCase() + ' Ready Check:', state, 'isReady:', isReady);
    }

    // ============ WFO MODE ============
    async function initWFO() {
        console.log('Initializing WFO mode...');
        document.getElementById('network-status-wfo').style.display = 'block';
        document.getElementById('network-details-wfo').style.display = 'none';
        
        initFingerprintForMode('wfo');
        renderWfoOfficeDetails(); 
        startGPSForWFO();
        setTimeout(() => initFaceDetectionForMode('wfo'), 1000);
    }

    function startGPSForWFO() {
        const office = getSelectedWfoOffice();
        if (!office) {
            renderWfoOfficeDetails();
            return;
        }

        document.getElementById('gps-status-wfo').innerHTML = '<span class="badge bg-warning">🔍 Locating GPS...</span>';

        if (!navigator.geolocation) {
            document.getElementById('gps-status-wfo').innerHTML = '<span class="badge bg-danger">❌ GPS is not supported</span>';
            return;
        }

        const onSuccess = (position) => {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;
            const acc = position.coords.accuracy;
            updateWfoDistanceStatus(lat, lon, acc);
        };

        const onError = (error) => {
            let msg = 'GPS Error: ';
            if (error.code === error.PERMISSION_DENIED) msg += 'Permission denied';
            else if (error.code === error.POSITION_UNAVAILABLE) msg += 'Unavailable';
            else if (error.code === error.TIMEOUT) msg += 'Timeout';

            document.getElementById('gps-status-wfo').innerHTML = '<span class="badge bg-danger">❌ ' + msg + '</span>';
        };

        gpsWatchId = navigator.geolocation.watchPosition(onSuccess, onError, {
            enableHighAccuracy: true,
            timeout: 30000,
            maximumAge: 5000
        });
    }

    function refreshGPS() {
        if (gpsWatchId) {
            navigator.geolocation.clearWatch(gpsWatchId);
        }
        modeState.wfo.gps = false;
        startGPSForWFO();
    }

    // ============ WFH MODE ============
    async function initWFH() {
        console.log('Initializing WFH mode...');
        initFingerprintForMode('wfh');
        startGPSFree('wfh');
        setTimeout(() => initFaceDetectionForMode('wfh'), 1000);
    }

    // ============ WFA MODE ============
    async function initWFA() {
        console.log('Initializing WFA mode...');
        initFingerprintForMode('wfa');
        startGPSFree('wfa');
        setTimeout(() => initFaceDetectionForMode('wfa'), 1000);
    }

    // ============ GPS FREE (WFH/WFA) ============
    function startGPSFree(mode) {
        document.getElementById('gps-status-' + mode).innerHTML = '<span class="badge bg-warning">🔍 Locating GPS...</span>';

        if (!navigator.geolocation) {
            document.getElementById('gps-status-' + mode).innerHTML = '<span class="badge bg-danger">❌ GPS is not supported</span>';
            return;
        }

        const onSuccess = (position) => {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;
            const acc = position.coords.accuracy;

            document.getElementById('latitude-' + mode).value = lat;
            document.getElementById('longitude-' + mode).value = lon;
            document.getElementById('accuracy-' + mode).value = acc;

            document.getElementById('lat-display-' + mode).textContent = lat.toFixed(6);
            document.getElementById('lon-display-' + mode).textContent = lon.toFixed(6);

            document.getElementById('gps-status-' + mode).innerHTML = '<span class="badge bg-success">✅ GPS OK</span>';
            modeState[mode].gps = true;
            checkReady(mode);
        };

        const onError = (error) => {
            let msg = 'GPS Error: ';
            if (error.code === error.PERMISSION_DENIED) msg += 'Permission denied';
            else if (error.code === error.POSITION_UNAVAILABLE) msg += 'Unavailable';
            else if (error.code === error.TIMEOUT) msg += 'Timeout';

            document.getElementById('gps-status-' + mode).innerHTML = '<span class="badge bg-danger">❌ ' + msg + '</span>';
        };

        gpsWatchIds[mode] = navigator.geolocation.watchPosition(onSuccess, onError, {
            enableHighAccuracy: true,
            timeout: 30000,
            maximumAge: 5000
        });
    }

    function refreshGPSFree(mode) {
        if (gpsWatchIds[mode]) {
            navigator.geolocation.clearWatch(gpsWatchIds[mode]);
        }
        modeState[mode].gps = false;
        startGPSFree(mode);
    }

    // ============ FACE DETECTION (All Modes) ============
    async function initFaceDetectionForMode(mode) {
        const statusEl = document.getElementById('face-status-' + mode);
        const videoContainer = document.getElementById('video-container-' + mode);
        const previewContainer = document.getElementById('preview-container-' + mode);
        const previewImg = document.getElementById('preview-img-' + mode);

        statusEl.innerHTML = '<span class="badge bg-warning">Loading AI Model...</span>';

        try {
            const MODEL_URL = '{{ asset("vendor/face-api/weights") }}';
            await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
            
            // Check if the camera is already on to avoid double requesting camera access
            let stream = mode === 'wfo' ? videoStream : videoStreams[mode];
            if (!stream) {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } });
                if (mode === 'wfo') videoStream = stream;
                else videoStreams[mode] = stream;
            }

            const videoEl = document.getElementById('video-' + mode);
            videoEl.srcObject = stream;

            statusEl.innerHTML = '<span class="badge bg-info">Waiting for face movement...</span>';

            // Call the detection loop function
            startFaceDetectionLoop(mode, videoEl, statusEl, videoContainer, previewContainer, previewImg);

        } catch (err) {
            statusEl.innerHTML = '<span class="badge bg-danger">Camera failed: ' + err.message + '</span>';
        }
    }

    function startFaceDetectionLoop(mode, videoEl, statusEl, videoContainer, previewContainer, previewImg) {
        let detectionCount = 0;
        const interval = setInterval(async () => {
            const detections = await faceapi.detectAllFaces(videoEl, new faceapi.TinyFaceDetectorOptions());
            
            if (detections.length > 0) {
                detectionCount++;
                if (detectionCount >= 10) {
                    clearInterval(interval); // Stop detection
                    statusEl.innerHTML = '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Face Verified!</span>';

                    // --- CAPTURE LOGIC (RESIZE 480p, MIRROR, COMPRESS) ---
                    const canvas = document.createElement('canvas');
                    const targetWidth = 480; 
                    const scale = targetWidth / videoEl.videoWidth;
                    const targetHeight = videoEl.videoHeight * scale;

                    canvas.width = targetWidth;
                    canvas.height = targetHeight;
                    const ctx = canvas.getContext('2d');

                    // Mirror the image on the canvas so the saved image matches the screen display
                    ctx.translate(targetWidth, 0);
                    ctx.scale(-1, 1);
                    ctx.drawImage(videoEl, 0, 0, targetWidth, targetHeight);

                    // Compress to lightweight JPEG (70% quality)
                    const photoData = canvas.toDataURL('image/jpeg', 0.7);

                    const photoInput = document.getElementById('photo_data-' + mode);
                    if (photoInput) photoInput.value = photoData;

                    // --- CHANGE DISPLAY TO PREVIEW ---
                    previewImg.src = photoData; // Paste base64 image to img tag
                    videoContainer.style.display = 'none'; // Hide live camera
                    previewContainer.style.display = 'block'; // Show photo result

                    // Pass Face Validation
                    modeState[mode].face = true;
                    checkReady(mode); // <--- THIS IS THE FIX BRO
                }
            }
        }, 500);

        if (mode === 'wfo') faceDetectionInterval = interval;
        else faceDetectionIntervals[mode] = interval;
    }

    // Function 3: Retake Button
    function retakePhoto(mode) {
        // Reset state & clear old photo data
        modeState[mode].face = false;
        document.getElementById('photo_data-' + mode).value = '';
        checkReady(mode); 

        // Revert UI from Preview mode to Video (Camera) mode
        document.getElementById('preview-container-' + mode).style.display = 'none';
        document.getElementById('video-container-' + mode).style.display = 'block';
        
        const videoEl = document.getElementById('video-' + mode);
        const statusEl = document.getElementById('face-status-' + mode);
        const videoContainer = document.getElementById('video-container-' + mode);
        const previewContainer = document.getElementById('preview-container-' + mode);
        const previewImg = document.getElementById('preview-img-' + mode);
        
        statusEl.innerHTML = '<span class="badge bg-info">Waiting for face movement...</span>';

        // Restart detection loop
        startFaceDetectionLoop(mode, videoEl, statusEl, videoContainer, previewContainer, previewImg);
    }

    // ============ FINGERPRINT (All Modes) ============
    async function initFingerprintForMode(mode) {
        try {
            const statusEl = document.getElementById('fingerprint-status-' + mode);
            if (statusEl) {
                statusEl.innerHTML = '<span class="badge bg-warning">⏳ Loading Fingerprint...</span>';
            }

            const fp = await FingerprintJS.load();
            const result = await fp.get();

            document.getElementById('fingerprint-' + mode).value = result.visitorId;

            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            document.getElementById('is_mobile-' + mode).value = isMobile ? '1' : '0';

            if (statusEl) {
                statusEl.innerHTML = '<span class="badge bg-success">✅ Fingerprint Ready</span>';
            }

            console.log('Fingerprint ready for ' + mode + ':', result.visitorId);

            modeState[mode].fingerprint = true;
            checkReady(mode);
        } catch (err) {
            console.error('Fingerprint error:', err);
            const statusEl = document.getElementById('fingerprint-status-' + mode);
            if (statusEl) {
                statusEl.innerHTML = '<span class="badge bg-danger">❌ Error: ' + err.message + '</span>';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const officeSelect = document.getElementById('office-location-wfo');
        if (officeSelect) {
            officeSelect.addEventListener('change', function() {
                modeState.wfo.network = false; 
                renderWfoOfficeDetails();
                refreshGPS();
            });
        }
        
        renderWfoOfficeDetails();
    });
</script>

@endsection