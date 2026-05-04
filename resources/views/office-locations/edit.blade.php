@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <div class="page-title mb-4">
        <div class="row">
            <div class="col-md-6">
                <h3>Edit Office Location</h3>
                <p class="text-subtitle text-muted">Perbarui area kerja, radius, dan daftar WiFi lokasi kantor.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <nav aria-label="breadcrumb" class="breadcrumb-header">
                    <ol class="breadcrumb justify-content-md-end">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('office-locations.index') }}">Office Locations</a></li>
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

                <form action="{{ route('office-locations.update', $officeLocation) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lokasi</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $officeLocation->name) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tipe Lokasi</label>
                            <select name="location_type" class="form-select" required>
                                <option value="head_office" {{ old('location_type', $officeLocation->location_type) == 'head_office' ? 'selected' : '' }}>Pusat</option>
                                <option value="branch" {{ old('location_type', $officeLocation->location_type) == 'branch' ? 'selected' : '' }}>Cabang</option>
                                <option value="other" {{ old('location_type', $officeLocation->location_type) == 'other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active" {{ old('status', $officeLocation->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $officeLocation->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="address" rows="3" class="form-control">{{ old('address', $officeLocation->address) }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Latitude</label>
                            <input type="number" name="latitude" class="form-control" value="{{ old('latitude', $officeLocation->latitude) }}" step="0.0000001" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Longitude</label>
                            <input type="number" name="longitude" class="form-control" value="{{ old('longitude', $officeLocation->longitude) }}" step="0.0000001" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Radius WFO (meter)</label>
                            <input type="number" name="radius" class="form-control" value="{{ old('radius', $officeLocation->radius) }}" min="10" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat IP yang Diizinkan (Opsional)</label>
                            <textarea name="allowed_ips_text" class="form-control" rows="3">{{ old('allowed_ips_text', implode("\n", $officeLocation->allowed_ips ?? [])) }}</textarea>
                            <small class="text-muted">Masukkan satu Alamat IP per baris untuk membatasi akses.</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea name="notes" rows="3" class="form-control">{{ old('notes', $officeLocation->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('office-locations.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Location
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
