@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <div class="page-title mb-4">
        <div class="row">
            <div class="col-md-6">
                <h3>Office Locations</h3>
                <p class="text-subtitle text-muted">Kelola area kerja untuk kebutuhan WFO dan presensi.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <nav aria-label="breadcrumb" class="breadcrumb-header">
                    <ol class="breadcrumb justify-content-md-end">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Office Locations</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card shadow-sm">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-1">Daftar Lokasi Kantor</h5>
                        <small class="text-muted">Setiap lokasi dapat memiliki radius dan daftar SSID WiFi yang berbeda.</small>
                    </div>
                    <a href="{{ route('office-locations.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add Location
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama</th>
                                <th>Tipe</th>
                                <th>Alamat</th>
                                <th>Koordinat</th>
                                {{-- <th>WiFi SSID</th> --}}
                                <th>Karyawan</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @forelse($officeLocations as $officeLocation)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $officeLocation->name }}</div>
                                        @if($officeLocation->notes)
                                            <small class="text-muted">{{ $officeLocation->notes }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $officeLocation->type_label }}</span>
                                    </td>
                                    <td>{{ $officeLocation->address ?: '-' }}</td>
                                    <td>
                                        @if(!is_null($officeLocation->latitude) && !is_null($officeLocation->longitude))
                                            <div>{{ $officeLocation->latitude }}, {{ $officeLocation->longitude }}</div>
                                            <small class="text-muted">Radius: {{ number_format($officeLocation->radius) }} m</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    {{-- <td style="min-width: 220px;">
                                        @forelse($officeLocation->allowed_ssids ?? [] as $ssid)
                                            <span class="badge bg-light text-dark border mb-1">{{ $ssid }}</span>
                                        @empty
                                            <span class="text-muted">Mengikuti konfigurasi default</span>
                                        @endforelse
                                    </td> --}}
                                    <td>{{ number_format($officeLocation->employees_count) }}</td>
                                    <td>
                                        @if($officeLocation->status === 'active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('office-locations.edit', $officeLocation) }}" class="btn btn-outline-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('office-locations.destroy', $officeLocation) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Belum ada lokasi kantor yang tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
document.querySelectorAll('.delete-form').forEach((form) => {
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        window.confirmDelete(this, 'Hapus lokasi kantor ini?');
    });
});
</script>
@endpush
@endsection
