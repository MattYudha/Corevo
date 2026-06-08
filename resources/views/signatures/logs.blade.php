@extends ('layouts.dashboard')

@section ('content')
    <div class="page-heading mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h3 class="mb-0">Riwayat Log Tanda Tangan</h3>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Signature Logs</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="page-content">
        <div class="container-fluid">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-transparent border-bottom pt-4 pb-3">
                    <h5 class="card-title mb-0">Daftar Validasi Tanda Tangan</h5>
                    <p class="text-muted small mb-0">Kelola dan pantau seluruh aktivitas tanda tangan internal maupun eksternal dengan cepat.</p>
                </div>

                <div class="card-body">
                    <table id="signatureLogsTable" class="table table-striped table-hover align-middle w-100">
                        <thead>
                            <tr>
                                <th class="ps-3">Detail Penandatangan</th>
                                <th>Dokumen Terkait</th>
                                <th>Waktu & IP Address</th>
                                <th>Status Approval</th>
                                <th>Keamanan (OpenSSL)</th>
                                <th class="text-center pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($signatures as $signature)
                                <tr>
                                    <td class="ps-3">
                                        <strong>
                                            @if ($signature->signer)
                                                {{ $signature->signer->name }}
                                                <span class="badge bg-primary ms-1" style="font-size: 0.7em"
                                                    >Internal</span
                                                >
                                            @else
                                                {{ $signature->external_name }}
                                                <span class="badge bg-secondary ms-1" style="font-size: 0.7em"
                                                    >Eksternal</span
                                                >
                                            @endif
                                        </strong>

                                        <small class="d-block text-muted mt-1" style="font-size: 0.85em">
                                            @if ($signature->signer)
                                                <i class="bi bi-person-badge"></i>
                                                {{
                                                    $signature->signer->employee->employeePositions
                                                        ->where('is_active', true)
                                                        ->first()->position->title ??
                                                        ($signature->signer->employee->role->title ?? 'Staff')
                                                }}
                                                |
                                                <i class="bi bi-envelope"></i>
                                                {{ $signature->signer->email }}
                                            @else
                                                <i class="bi bi-person-badge"></i>
                                                {{ $signature->external_title ?? 'Jabatan' }} di {{ $signature->external_company ?? 'Klien/Vendor' }}
                                                @if ($signature->external_email)
                                                    |
                                                    <i class="bi bi-envelope"></i>
                                                    {{ $signature->external_email }}
                                                @endif
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        @if ($signature->signable instanceof App\Models\Letter)
                                            <span class="d-block fw-bold">{{
                                                $signature->signable->subject ??
                                                    $signature->signable->letter_number
                                            }}</span>
                                            <span class="badge bg-info text-dark mt-1" style="font-size: 0.7em"
                                                >Surat (Letter)</span
                                            >
                                        @else
                                            <span
                                                class="badge bg-secondary"
                                                >{{ class_basename($signature->signable) }}</span
                                            >
                                        @endif
                                    </td>
                                    <td>
                                        <span class="d-none">{{ $signature->signed_date->timestamp }}</span>
                                        <small class="text-muted d-block mb-1">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $signature->signed_date->format('d M Y, H:i') }}
                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="bi bi-laptop me-1"></i>
                                            {{ $signature->ip_address ?? 'N/A' }}
                                        </small>
                                    </td>
                                    <td>
                                        @if ($signature->is_verified)
                                            <span class="badge bg-success mb-1">Terverifikasi</span>
                                        @else
                                            <span class="badge bg-warning text-dark mb-1">Menunggu</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($signature->isValid())
                                            <span class="badge bg-info text-dark">
                                                <i class="bi bi-shield-check me-1"></i> Enkripsi Valid
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="bi bi-shield-exclamation me-1"></i> Invalid / Rusak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="btn-group btn-group-sm">
                                            @if ($signature->signable instanceof App\Models\Letter)
                                                <a
                                                    href="{{ route('letters.show', $signature->signable) }}"
                                                    class="btn btn-outline-primary"
                                                    title="Lihat Dokumen"
                                                >
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            @endif

                                            <a
                                                href="{{ route('signatures.validate', $signature) }}"
                                                class="btn {{ $signature->isValid() ? 'btn-outline-success' : 'btn-outline-danger' }}"
                                                onclick="validateSignature(event, this)"
                                                title="Cek Validasi"
                                            >
                                                <i class="bi bi-check2-circle"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        // DataTables cukup dipanggil seperti biasa
        $(document).ready(function () {
            $('#signatureLogsTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
                },
                order: [[2, 'desc']], // Urut berdasarkan Waktu terbaru
            });
        });

        function validateSignature(event, element) {
            event.preventDefault();
            const url = element.href;

            Swal.fire({
                title: 'Memvalidasi Enkripsi',
                text: 'Sedang mengecek keaslian tanda tangan via OpenSSL...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            fetch(url)
                .then((response) => response.json())
                .then((data) => {
                    Swal.close();
                    if (data.valid) {
                        Swal.fire('Valid!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Invalid!', data.message, 'error').then(() => location.reload());
                    }
                })
                .catch((error) => {
                    Swal.close();
                    Swal.fire('Error', 'Gagal memvalidasi: ' + error.message, 'error');
                });
        }
    </script>
@endsection
