<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verifikasi Dokumen | PT Aratech Nusantara Indonesia</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />

    <style>
        :root {
            --aratech-color: #44c0b5;
            --aratech-hover: #37a097;
        }

        body {
            background-color: #f3f4f6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .text-aratech {
            color: var(--aratech-color) !important;
        }
        .bg-aratech {
            background-color: var(--aratech-color) !important;
            color: white;
        }
        .bg-light-aratech {
            background-color: #e0f6f4 !important;
            color: var(--aratech-color) !important;
        }
        .btn-aratech {
            background-color: var(--aratech-color);
            border-color: var(--aratech-color);
            color: white;
        }
        .btn-aratech:hover,
        .btn-aratech:focus {
            background-color: var(--aratech-hover);
            border-color: var(--aratech-hover);
            color: white;
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-aratech shadow-sm py-3">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center m-0" href="#">
                <div
                    class="bg-white rounded-3 p-1 me-3 d-flex align-items-center justify-content-center shadow-sm"
                    style="width: 45px; height: 45px"
                >
                    <img
                        src="{{ asset('img/aratech-logo-only.png') }}"
                        alt="Logo Aratech"
                        style="max-width: 100%; max-height: 100%; object-fit: contain"
                    />
                </div>
                Portal Verifikasi Dokumen
            </a>
            <span class="navbar-text text-white opacity-75 d-none d-md-block fw-medium">
                PT Aratech Nusantara Indonesia
            </span>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-10">
                <div class="card shadow-sm border-0 rounded-3 bg-white">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center @if($isValid ?? true) mb-4 @endif">
                            @if ($isValid ?? true)
                                <div class="icon-circle bg-light-aratech text-aratech mb-3">
                                    <i class="bi bi-patch-check-fill" style="font-size: 3rem"></i>
                                </div>
                                <h4 class="fw-bold text-dark">Dokumen Tervalidasi</h4>
                                <p class="text-muted mb-0">Tanda tangan digital pada dokumen ini sah dan terverifikasi oleh sistem.</p>
                            @else
                                <div class="icon-circle bg-danger bg-opacity-10 text-danger mb-3">
                                    <i class="bi bi-x-octagon-fill" style="font-size: 3rem"></i>
                                </div>
                                <h4 class="fw-bold text-dark">Dokumen Tidak Valid</h4>
                                <p class="text-muted mb-0">Dokumen ini tidak dikenali, palsu, atau telah dimodifikasi setelah ditandatangani.</p>
                            @endif
                        </div>

                        @if ($isValid ?? true)
                            <hr class="my-4" style="opacity: 0.1" />
                            <div class="mb-5">
                                <h6
                                    class="text-muted text-uppercase fw-bold mb-3"
                                    style="letter-spacing: 1px; font-size: 0.8rem"
                                >
                                    Informasi Dokumen
                                </h6>
                                <div class="p-4 bg-light rounded-3 border">
                                    <h5 class="fw-bold text-dark mb-2">
                                        {{ $document->subject ?? 'Dokumen Tanpa Judul' }}
                                    </h5>
                                    <span class="badge bg-white text-secondary border fw-medium mb-3">
                                        No: {{ $document->letter_number ?? '-' }}
                                    </span>

                                    <div class="d-flex flex-column gap-2 text-muted small mt-1">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-calendar3 me-2 text-aratech"></i>
                                            Diterbitkan:
                                            <span class="ms-1 text-dark fw-medium">
                                                {{
                                                    isset($document->created_at)
                                                        ? \Carbon\Carbon::parse($document->created_at)->translatedFormat('d F Y, H:i')
                                                        : '-'
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h6
                                    class="text-muted text-uppercase fw-bold mb-3"
                                    style="letter-spacing: 1px; font-size: 0.8rem"
                                >
                                    Status Penandatanganan
                                </h6>

                                <div class="list-group list-group-flush border rounded-3 overflow-hidden">
                                    @forelse ($signatures ?? [] as $sig)
                                        <div class="list-group-item bg-white p-3 p-md-4 border-bottom">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="mt-1">
                                                    @if (($sig->status ?? 'signed') === 'signed')
                                                        <i class="bi bi-check-circle-fill text-aratech fs-4"></i>
                                                    @elseif (($sig->status ?? '') === 'pending')
                                                        <i class="bi bi-clock-fill text-warning fs-4"></i>
                                                    @else
                                                        <i class="bi bi-x-circle-fill text-danger fs-4"></i>
                                                    @endif
                                                </div>

                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 fw-bold text-dark">
                                                        {{
                                                            $sig->user_id
                                                                ? $sig->signer->fullname ?? $sig->signer->name
                                                                : $sig->external_name ?? 'Pihak Eksternal'
                                                        }}
                                                    </h6>
                                                    <p class="mb-1 text-muted small">
                                                        {{
                                                            $sig->user_id
                                                                ? $sig->signer->email ?? '-'
                                                                : $sig->external_email ?? '-'
                                                        }}
                                                    </p>

                                                    @if (($sig->status ?? 'signed') === 'signed')
                                                        <div
                                                            class="d-flex align-items-center mt-2 text-aratech small fw-semibold bg-light-aratech d-inline-flex px-2 py-1 rounded"
                                                        >
                                                            <i class="bi bi-shield-check me-1"></i> Ditandatangani: {{
                                                                isset($sig->signed_date)
                                                                    ? \Carbon\Carbon::parse($sig->signed_date)->translatedFormat('d M Y, H:i')
                                                                    : '-'
                                                            }}
                                                        </div>
                                                    @elseif (($sig->status ?? '') === 'pending')
                                                        <div
                                                            class="d-flex align-items-center mt-2 text-warning small fw-semibold d-inline-flex px-2 py-1 rounded"
                                                            style="background-color: rgba(255, 193, 7, 0.15)"
                                                        >
                                                            <i class="bi bi-hourglass-split me-1"></i> Menunggu Tanda
                                                            Tangan
                                                        </div>
                                                    @else
                                                        <div
                                                            class="d-flex align-items-center mt-2 text-danger small fw-semibold bg-danger bg-opacity-10 d-inline-flex px-2 py-1 rounded"
                                                        >
                                                            <i class="bi bi-x-circle me-1"></i> Ditolak / Dibatalkan
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="list-group-item bg-white p-4 text-center">
                                            <div class="text-muted py-3">
                                                <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                                                <span class="fw-medium">Belum ada data penandatanganan.</span>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            @if (isset($document->file_path))
                                <div class="mt-5 text-center">
                                    <a
                                        href="{{ asset('storage/' . $document->file_path) }}"
                                        target="_blank"
                                        class="btn btn-aratech px-4 py-2 rounded-pill fw-semibold shadow-sm"
                                    >
                                        <i class="bi bi-file-earmark-pdf-fill me-2"></i>Lihat Dokumen Asli
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="text-muted small fw-medium">
                        <i class="bi bi-shield-check text-aratech fs-6 align-middle me-1"></i>
                        Dilindungi oleh sistem enkripsi PT Aratech Nusantara Indonesia.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
