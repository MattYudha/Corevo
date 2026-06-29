@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <div class="page-title mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3>Edit Master KPI: {{ $kpi_master->name }}</h3>
            </div>
            <div class="col-md-6 text-md-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-md-end">
                        <li class="breadcrumb-item"><a href="{{ route('kpi-masters.index') }}">Master KPI</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card shadow-sm">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('kpi-masters.update', $kpi_master->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">KPI Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $kpi_master->name) }}" required>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Default Target Value</label>
                            <input type="number" step="0.01" name="target_value" class="form-control" value="{{ old('target_value', $kpi_master->target_value) }}" required>
                            <small class="text-muted">Target achievement standard</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Weight (%)</label>
                            <input type="number" step="0.01" name="weight" class="form-control" value="{{ old('weight', $kpi_master->weight) }}" required>
                            <small class="text-muted">Contribution to final composite score</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Unit</label>
                            <input type="text" name="unit" class="form-control" value="{{ old('unit', $kpi_master->unit) }}" required>
                            <small class="text-muted">e.g., %, Hari, Logs, Dokumen</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label d-block fw-bold border-bottom pb-2">Assigned Roles</label>
                        <p class="text-muted small mb-3">Pilih jabatan mana saja yang akan dikenakan aturan KPI ini. Jika tidak ada yang dipilih, KPI ini tidak akan diberlakukan kepada siapapun.</p>
                        <div class="row">
                            @foreach($roles as $role)
                                <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}" 
                                            {{ $kpi_master->roles->contains($role->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_{{ $role->id }}">
                                            {{ $role->title }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> <strong>Peringatan:</strong> Mengubah Target atau Bobot di sini akan secara otomatis menerapkan perubahan tersebut ke SEMUA Role (Jabatan) yang memiliki KPI ini.
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('kpi-masters.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
