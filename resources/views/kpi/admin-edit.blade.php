@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3><i class="bi bi-pencil-square text-primary me-2"></i>Admin Edit KPI</h3>
            <p class="text-muted">{{ $employee->fullname }} — {{ $record->kpi->name ?? 'N/A' }}</p>
        </div>
        <a href="{{ route('kpi.show', $employee->id) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="page-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                @endif

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white d-flex align-items-center gap-2">
                        <i class="bi bi-sliders"></i>
                        <span class="fw-bold">Input KPI Manual (Admin Override)</span>
                    </div>
                    <div class="card-body p-4">

                        {{-- KPI Info --}}
                        <div class="alert alert-light border mb-4">
                            <div class="row g-2 text-sm">
                                <div class="col-6"><span class="text-muted">Metrik:</span> <strong>{{ $record->kpi->name ?? '-' }}</strong></div>
                                <div class="col-6"><span class="text-muted">Periode:</span> <strong>{{ $record->period }}</strong></div>
                                <div class="col-6"><span class="text-muted">Target:</span> <strong>{{ $record->target_value }} {{ $record->kpi->unit ?? '' }}</strong></div>
                                <div class="col-6"><span class="text-muted">Actual saat ini:</span> <strong>{{ $record->actual_value ?? '-' }}</strong></div>
                            </div>
                        </div>

                        <form action="{{ route('kpi.admin-update', [$employee->id, $record->id]) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label fw-bold">Actual Value <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="actual_value" class="form-control @error('actual_value') is-invalid @enderror"
                                    value="{{ old('actual_value', $record->actual_value) }}" required>
                                @error('actual_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="achieved" {{ old('status', $record->status) === 'achieved' ? 'selected' : '' }}>✅ Achieved</option>
                                        <option value="warning" {{ old('status', $record->status) === 'warning' ? 'selected' : '' }}>⚠️ Warning</option>
                                        <option value="critical" {{ old('status', $record->status) === 'critical' ? 'selected' : '' }}>🔴 Critical</option>
                                    </select>
                                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Performance Level <span class="text-danger">*</span></label>
                                    <select name="performance_level" class="form-select @error('performance_level') is-invalid @enderror" required>
                                        <option value="excellent" {{ old('performance_level', $record->performance_level) === 'excellent' ? 'selected' : '' }}>⭐ Excellent</option>
                                        <option value="good" {{ old('performance_level', $record->performance_level) === 'good' ? 'selected' : '' }}>👍 Good</option>
                                        <option value="satisfactory" {{ old('performance_level', $record->performance_level) === 'satisfactory' ? 'selected' : '' }}>😐 Satisfactory</option>
                                        <option value="needs_improvement" {{ old('performance_level', $record->performance_level) === 'needs_improvement' ? 'selected' : '' }}>📈 Needs Improvement</option>
                                        <option value="poor" {{ old('performance_level', $record->performance_level) === 'poor' ? 'selected' : '' }}>❌ Poor</option>
                                    </select>
                                    @error('performance_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Catatan Admin</label>
                                <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                                    placeholder="Alasan edit manual, konteks, dll.">{{ old('notes', $record->notes) }}</textarea>
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="alert alert-warning py-2 small mb-4">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                Perubahan ini akan otomatis berstatus <strong>Approved</strong> dan menimpa nilai kalkulasi otomatis.
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('kpi.show', $employee->id) }}" class="btn btn-outline-secondary px-4">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
