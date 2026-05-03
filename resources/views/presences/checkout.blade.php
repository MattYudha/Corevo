@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Check Out</h3>
                <p class="text-subtitle text-muted">Record your check-out time and location.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('presences.index') }}">Presences</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Check Out</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
    <section class="section">
        <div class="card">
            <div class="card-body">
                @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
                @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

                <form action="{{ route('presences.checkout.process') }}" method="POST" id="checkout-form">
                    @csrf
                    
                    <div id="gps-section" class="mb-3" style="display: none;">
                        <div id="location-warning" class="alert d-none"></div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Latitude</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-light" name="latitude" id="latitude" readonly>
                                    <button type="button" class="btn btn-outline-secondary" onclick="getGPSLocation()">
                                        <i class="bi bi-geo-alt"></i> Get Location
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Longitude</label>
                                <input type="text" class="form-control bg-light" name="longitude" id="longitude" readonly>
                            </div>
                        </div>
                        
                        <div class="mb-3" id="distance-wrapper">
                            <label class="form-label"><b>Distance to Office</b></label>
                            <div class="input-group">
                                <input type="text" class="form-control font-monospace" id="distance-display" readonly placeholder="Locating...">
                                <span class="input-group-text">Meters</span>
                            </div>
                            <small class="text-muted" id="distance-info">Allowed radius for {{ $officeLocationConfig['name'] ?? 'Office' }}: {{ $officeLocationConfig['radius'] ?? 0 }}m</small>
                        </div>
                        <input type="hidden" name="accuracy" id="accuracy">
                    </div>

                    <div class="mb-4 p-3 bg-light rounded">
                        <p class="mb-1"><strong>Check-in Time:</strong> {{ $checkInTime ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Work Type:</strong> <span class="badge bg-primary">{{ $presence->work_type ?? 'WFO' }}</span></p>
                        @if(($presence->work_type ?? 'WFO') === 'WFO')
                            <p class="mb-1"><strong>Office Location:</strong> {{ $officeLocationConfig['name'] ?? 'N/A' }}</p>
                        @endif
                        <p class="mb-0"><strong>Current Time:</strong> <span id="current-time" class="text-danger fw-bold"></span></p>
                    </div>

                    <button type="submit" id="btn-checkout" class="btn btn-success px-4" disabled>
                        <i class="bi bi-box-arrow-right me-1"></i> Confirm Check Out
                    </button>
                    <a href="{{ route('presences.index') }}" class="btn btn-light-secondary ms-2">Cancel</a>
                </form>
            </div>
        </div>
    </section>
</div>

<script>
    const officeLat = @json($officeLocationConfig['latitude'] ?? 0);
    const officeLon = @json($officeLocationConfig['longitude'] ?? 0);
    const thresholdMeters = @json($officeLocationConfig['radius'] ?? 0);
    const workType = @json($presence->work_type ?? 'WFO');

    setInterval(() => {
        document.getElementById('current-time').textContent = new Date().toLocaleTimeString();
    }, 1000);

    function getGPSLocation() {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by your browser.');
            return;
        }

        document.getElementById('btn-checkout').disabled = true; 

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                document.getElementById('latitude').value = pos.coords.latitude;
                document.getElementById('longitude').value = pos.coords.longitude;
                document.getElementById('accuracy').value = pos.coords.accuracy;
                
                document.getElementById('btn-checkout').disabled = false; 

                if (workType === 'WFO') {
                    const distDegrees = Math.sqrt(Math.pow(pos.coords.latitude - officeLat, 2) + Math.pow(pos.coords.longitude - officeLon, 2));
                    const distMeters = Math.round(distDegrees * 111320);
                    document.getElementById('distance-display').value = distMeters;
                    
                    const warningEl = document.getElementById('location-warning');
                    if (distMeters <= thresholdMeters) {
                        warningEl.classList.add('d-none');
                    } else {
                        warningEl.classList.remove('d-none');
                        warningEl.className = 'alert alert-danger';
                        warningEl.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i> <b>Out of Radius</b>: You are <b>${distMeters}m</b> away from the office. You may still check out, but it will be flagged.`;
                    }
                }
            },
            (err) => {
                console.error('GPS Error:', err.message);
                alert('Failed to get location: ' + err.message);
            },
            { enableHighAccuracy: true, timeout: 30000, maximumAge: 0 }
        );
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (workType === 'WFO' || workType === 'WFA') {
            document.getElementById('gps-section').style.display = 'block';
            
            if (workType === 'WFA') {
                document.getElementById('distance-wrapper').style.display = 'none'; 
            }
            
            getGPSLocation();
        } else {
            document.getElementById('gps-section').style.display = 'none';
            document.getElementById('btn-checkout').disabled = false;
        }
    });
</script>
@endsection