@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Pengajuan Lembur</h3>
                <p class="text-subtitle text-muted">Lengkapi formulir di bawah untuk mengajukan lembur.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('overtimes.index') }}">Lembur Saya</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pengajuan Lembur</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="card">
        <div class="card-body pt-4">
            <form action="{{ route('overtimes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Tanggal Lembur</label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Waktu Mulai</label>
                        <input type="time" name="start_time" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Waktu Selesai</label>
                        <input type="time" name="end_time" class="form-control" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Rincian Kegiatan</label>
                        <textarea name="description" class="form-control" rows="4" required placeholder="Jelaskan apa yang Anda kerjakan..."></textarea>
                    </div>
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-bold">Lampiran Bukti (Opsional)</label>
                        <input type="file" name="evidence" class="form-control">
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    <a href="{{ route('overtimes.index') }}" class="btn btn-light-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection