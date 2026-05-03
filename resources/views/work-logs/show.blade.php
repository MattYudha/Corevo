@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Log Aktivitas</h3>
                <p class="text-subtitle text-muted">Melihat rincian aktivitas pekerjaan yang telah dilaporkan.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('work-logs.index') }}">My Activity</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail Log Aktivitas</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="card">
        <div class="card-header border-bottom">
            <h4 class="card-title mb-0"><i class="bi bi-journal-text me-2 text-primary"></i> Informasi Aktivitas</h4>
        </div>
        <div class="card-body pt-4">
            <div class="row">
                <div class="col-12 col-md-6 mb-4">
                    <h6 class="text-muted mb-1 fw-bold">Tanggal Aktivitas</h6>
                    <p class="fs-6 mb-0">{{ \Carbon\Carbon::parse($work_log->log_date)->translatedFormat('l, d F Y') }}</p>
                </div>
                <div class="col-12 col-md-6 mb-4">
                    <h6 class="text-muted mb-1 fw-bold">Nama Karyawan</h6>
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-light-primary me-2">
                            <span class="avatar-content">{{ substr($work_log->employee ? $work_log->employee->fullname : 'U', 0, 1) }}</span>
                        </div>
                        <p class="fs-6 mb-0 fw-bold">{{ $work_log->employee ? $work_log->employee->fullname : 'Tidak Diketahui' }}</p>
                    </div>
                </div>

                <div class="col-12 mb-4">
                    <h6 class="text-muted mb-2 fw-bold">Hubungan Tugas (Task)</h6>
                    @if($work_log->task)
                        <a href="{{ route('tasks.show', $work_log->task_id) }}" class="badge bg-light-info text-info text-decoration-none p-2 fs-6 border border-info d-inline-block text-wrap">
                            <i class="bi bi-list-check me-1"></i> {{ $work_log->task->title }}
                        </a>
                    @else
                        <span class="text-muted fst-italic">- Tidak terhubung dengan tugas apa pun -</span>
                    @endif
                </div>

                <div class="col-12 mb-4">
                    <h6 class="text-muted mb-2 fw-bold">Rincian Aktivitas Pekerjaan</h6>
                    <div class="p-3 bg-light rounded text-dark" style="min-height: 100px;">
                        {!! nl2br(e($work_log->description)) !!}
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <h6 class="text-muted mb-2 fw-bold">Lampiran / Bukti Kerja (Evidence)</h6>
                    @if($work_log->evidence_path)
                        <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center p-3 border rounded">
                            <div class="me-0 me-sm-3 mb-2 mb-sm-0">
                                <i class="bi bi-file-earmark-check fs-1 text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-2 mb-sm-1">Berkas Lampiran Tersedia</h6>
                                <a href="{{ asset('storage/'.$work_log->evidence_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="bi bi-download me-1"></i> Buka / Unduh Lampiran
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center p-3 bg-light rounded border border-light">
                            <i class="bi bi-file-earmark-x fs-3 text-muted me-0 me-sm-3 mb-2 mb-sm-0"></i>
                            <span class="text-muted fst-italic">Tidak ada berkas bukti yang dilampirkan pada log ini.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- Footer area: Tombol sekarang disejajarkan di sini --}}
        <div class="card-footer bg-white border-top d-flex justify-content-end flex-wrap gap-2 py-3">
            <a href="{{ route('work-logs.index') }}" class="btn btn-light-secondary d-inline-flex align-items-center">
                <i class="bi bi-arrow-left me-1 mb-2"></i> Kembali ke Daftar
            </a>
            
            @if(Auth::user()->isMasterAdmin() || Auth::user()->employee_id == $work_log->employee_id)
                <a href="{{ route('work-logs.edit', $work_log->id) }}" class="btn btn-primary d-inline-flex align-items-center">
                    <i class="bi bi-pencil me-1 mb-2"></i> Ubah Log
                </a>
            @endif
        </div>
    </div>
</section>
@endsection