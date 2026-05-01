@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Pengajuan Lembur</h3>
                <p class="text-subtitle text-muted">Informasi lengkap mengenai pengajuan lembur.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('overtimes.index') }}">Lembur Saya</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail Pengajuan Lembur</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="card">
        <div class="card-header border-bottom">
            <h4 class="card-title mb-0"><i class="bi bi-file-earmark-text me-2"></i> Laporan Lembur</h4>
        </div>
        <div class="card-body pt-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="text-muted small d-block">Nama Karyawan</label>
                    <span class="fw-bold fs-5 text-dark">{{ $overtime->employee->fullname }}</span>
                </div>
                <div class="col-md-6 mb-3 text-md-end">
                    <label class="text-muted small d-block">Status</label>
                    @if($overtime->status == 'approved') <span class="badge bg-success">Disetujui</span>
                    @elseif($overtime->status == 'pending') <span class="badge bg-warning text-dark">Menunggu Persetujuan</span>
                    @else <span class="badge bg-danger">Ditolak</span> @endif
                </div>
                <div class="col-md-4 mb-3">
                    <label class="text-muted small d-block">Tanggal</label>
                    <span class="fw-bold">{{ \Carbon\Carbon::parse($overtime->date)->format('d F Y') }}</span>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="text-muted small d-block">Waktu</label>
                    <span class="fw-bold">{{ substr($overtime->start_time, 0, 5) }} - {{ substr($overtime->end_time, 0, 5) }}</span>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="text-muted small d-block">Total Durasi</label>
                    <span class="fw-bold">{{ round($overtime->duration_minutes / 60, 2) }} Jam</span>
                </div>
                <div class="col-12 mb-4">
                    <label class="text-muted small d-block">Deskripsi Kegiatan</label>
                    <div class="p-3 bg-light rounded border">{{ $overtime->description }}</div>
                </div>
                @if($overtime->evidence_path)
                <div class="col-12">
                    <label class="text-muted small d-block mb-2">Lampiran Bukti</label>
                    <a href="{{ asset('storage/'.$overtime->evidence_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-download me-1"></i> Lihat / Unduh Lampiran
                    </a>
                </div>
                @endif
            </div>
        </div>
        <div class="card-footer bg-light border-top text-end py-3">
            <a href="{{ route('overtimes.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</section>
@endsection