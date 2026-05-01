@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Pengajuan Lembur</h3>
                <p class="text-subtitle text-muted">Perbaiki data lembur Anda yang masih berstatus pending.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('overtimes.index') }}">Lembur Saya</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Pengajuan Lembur</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="card">
        <div class="card-body pt-4">
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('overtimes.update', $overtime->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Tanggal Lembur</label>
                        <input type="date" name="date" class="form-control" value="{{ $overtime->date }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Waktu Mulai</label>
                        <input type="time" name="start_time" class="form-control" value="{{ \Carbon\Carbon::parse($overtime->start_time)->format('H:i') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Waktu Selesai</label>
                        <input type="time" name="end_time" class="form-control" value="{{ \Carbon\Carbon::parse($overtime->end_time)->format('H:i') }}" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Rincian Kegiatan</label>
                        <textarea name="description" class="form-control" rows="4" required placeholder="Jelaskan apa yang Anda kerjakan...">{{ $overtime->description }}</textarea>
                    </div>
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-bold">Lampiran Bukti Baru (Opsional)</label>
                        <input type="file" name="evidence" class="form-control">
                        @if($overtime->evidence_path)
                            <div class="mt-2 p-2 bg-light-primary rounded border d-inline-block">
                                <small class="text-primary fw-bold"><i class="bi bi-paperclip"></i> Bukti saat ini:</small>
                                <a href="{{ asset('storage/'.$overtime->evidence_path) }}" target="_blank" class="small text-decoration-none ms-2">Buka Lampiran Lama</a>
                            </div>
                        @endif
                        <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengganti lampiran yang sudah ada.</small>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    <a href="{{ route('overtimes.index') }}" class="btn btn-light-secondary">Batal / Kembali</a>
                    <button type="submit" class="btn btn-primary px-4 d-flex align-items-center">
                        <i class="bi bi-save me-2 mb-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection