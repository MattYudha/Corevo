@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Master Presence</h3>
                <p class="text-subtitle text-muted">Manage the work time, late time, and the network security presence</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('presences.index') }}">Presences</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Master Presences</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    {{-- Menampilkan pesan sukses atau galat --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error) 
                    <li><i class="bi bi-exclamation-triangle me-1"></i> {{ $error }}</li> 
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('master-presences.settings.update') }}" method="POST">
        @csrf
        <div class="row">
            {{-- Aturan Waktu & Keterlambatan --}}
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header border-bottom">
                        <h4 class="card-title mb-0"><i class="bi bi-clock-history me-2 text-primary"></i> Aturan Keterlambatan</h4>
                    </div>
                    <div class="card-body pt-4">
                        <div class="form-group mb-4">
                            <label class="form-label fw-bold">Jam Masuk Kerja Default</label>
                            <input type="time" name="work_start_time" class="form-control" value="{{ $settings['work_start_time'] }}" required>
                            <small class="text-muted">Batas waktu normal sebelum perhitungan keterlambatan dimulai.</small>
                        </div>
                        <hr>
                        
                        {{-- Pengaturan WFO --}}
                        <div class="mb-3 p-3 border rounded bg-light-primary">
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="enable_late_wfo" value="0">
                                <input class="form-check-input toggle-late" type="checkbox" name="enable_late_wfo" value="1" id="chk_wfo" data-target="div_wfo" {{ $settings['enable_late_wfo'] == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="chk_wfo">Aktifkan Terlambat WFO</label>
                            </div>
                            <div id="div_wfo" style="display: {{ $settings['enable_late_wfo'] == '1' ? 'block' : 'none' }};">
                                <label class="small fw-bold mt-2">Toleransi Keterlambatan WFO (Menit)</label>
                                <div class="input-group input-group-sm mt-1">
                                    <input type="number" name="late_threshold_wfo" class="form-control" value="{{ $settings['late_threshold_wfo'] }}" min="0">
                                    <span class="input-group-text">Menit</span>
                                </div>
                            </div>
                        </div>

                        {{-- Pengaturan WFH --}}
                        <div class="mb-3 p-3 border rounded bg-light-success">
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="enable_late_wfh" value="0">
                                <input class="form-check-input toggle-late" type="checkbox" name="enable_late_wfh" value="1" id="chk_wfh" data-target="div_wfh" {{ $settings['enable_late_wfh'] == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="chk_wfh">Aktifkan Terlambat WFH</label>
                            </div>
                            <div id="div_wfh" style="display: {{ $settings['enable_late_wfh'] == '1' ? 'block' : 'none' }};">
                                <label class="small fw-bold mt-2">Toleransi Keterlambatan WFH (Menit)</label>
                                <div class="input-group input-group-sm mt-1">
                                    <input type="number" name="late_threshold_wfh" class="form-control" value="{{ $settings['late_threshold_wfh'] }}" min="0">
                                    <span class="input-group-text">Menit</span>
                                </div>
                            </div>
                        </div>

                        {{-- Pengaturan WFA --}}
                        <div class="mb-0 p-3 border rounded bg-light-info">
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="enable_late_wfa" value="0">
                                <input class="form-check-input toggle-late" type="checkbox" name="enable_late_wfa" value="1" id="chk_wfa" data-target="div_wfa" {{ $settings['enable_late_wfa'] == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="chk_wfa">Aktifkan Terlambat WFA</label>
                            </div>
                            <div id="div_wfa" style="display: {{ $settings['enable_late_wfa'] == '1' ? 'block' : 'none' }};">
                                <label class="small fw-bold mt-2">Toleransi Keterlambatan WFA (Menit)</label>
                                <div class="input-group input-group-sm mt-1">
                                    <input type="number" name="late_threshold_wfa" class="form-control" value="{{ $settings['late_threshold_wfa'] }}" min="0">
                                    <span class="input-group-text">Menit</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Navigasi Lokasi & Jaringan --}}
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header border-bottom">
                        <h4 class="card-title mb-0"><i class="bi bi-shield-lock me-2 text-primary"></i> Keamanan Jaringan & Lokasi</h4>
                    </div>
                    <div class="card-body pt-4">
                        <p class="text-muted">
                            Atur Alamat IP secara spesifik untuk masing-masing cabang kantor guna mencegah karyawan menggunakan aplikasi pihak ketiga seperti <strong>Fake GPS</strong>.
                        </p>
                        
                        <div class="alert alert-light-warning">
                            <h6 class="alert-heading"><i class="bi bi-info-circle"></i> Catatan Sistem:</h6>
                            <p class="mb-0 small">Sistem presensi WFO telah dikonfigurasi untuk menolak akses masuk jika alamat IP peramban karyawan tidak terdaftar di dalam pengaturan lokasi cabang mereka.</p>
                        </div>

                        <div class="d-grid mt-4">
                            <a href="{{ route('office-locations.index') }}" class="btn btn-outline-primary btn-lg d-flex align-items-center justify-content-center">
                                <i class="bi bi-geo-alt-fill me-2 mb-3"></i> Kelola Lokasi & IP Kantor
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header border-bottom">
                <h4 class="card-title mb-0"><i class="bi bi-cash-stack me-2 text-primary"></i> Pengaturan Keuangan Lembur</h4>
            </div>
            <div class="card-body pt-4">
                <div class="form-group mb-0">
                    <label class="form-label fw-bold">Upah Lembur Per Jam (Rp)</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="overtime_rate_per_hour" class="form-control" value="{{ $settings['overtime_rate_per_hour'] ?? 0 }}">
                    </div>
                    <small class="text-muted">Nominal ini akan dikalikan dengan total jam lembur yang telah disetujui pada laporan penggajian.</small>
                </div>
            </div>
        </div>
        
        {{-- Card Footer Action --}}
        <div class="card mt-3">
            <div class="card-body d-flex justify-content-end gap-2">
                <a href="{{ route('presences.index') }}" class="btn btn-light-secondary">
                    Batal / Kembali
                </a>
                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center">
                    Simpan Pengaturan
                </button>
            </div>
        </div>
    </form>
</section>

<script>
    // Script untuk memunculkan input batas menit saat toggle dinyalakan
    document.querySelectorAll('.toggle-late').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            var targetId = this.getAttribute('data-target');
            // Menambahkan sedikit animasi transisi bawaan jquery/bootstrap jika ingin lebih halus, 
            // namun display block/none juga sudah cukup responsif.
            document.getElementById(targetId).style.display = this.checked ? 'block' : 'none';
        });
    });
</script>
@endsection