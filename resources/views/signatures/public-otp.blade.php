<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verifikasi OTP | PT Aratech Nusantara Indonesia</title>

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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .bg-aratech {
            background-color: var(--aratech-color) !important;
            color: white;
        }
        .text-aratech {
            color: var(--aratech-color) !important;
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
        .btn-aratech:hover {
            background-color: var(--aratech-hover);
            border-color: var(--aratech-hover);
            color: white;
        }

        .otp-input {
            font-size: 2.5rem;
            letter-spacing: 0.5em;
            font-weight: 700;
            text-align: center;
            border: 2px solid #cbd5e1;
            padding-left: 0.8em; /* Offset for letter spacing */
        }
        .otp-input:focus {
            border-color: var(--aratech-color);
            box-shadow: 0 0 0 0.25rem rgba(68, 192, 181, 0.25);
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
                Portal Tanda Tangan Digital
            </a>
            <span class="navbar-text text-white opacity-75 d-none d-md-block fw-medium">
                PT Aratech Nusantara Indonesia
            </span>
        </div>
    </nav>

    <div class="container flex-grow-1 d-flex align-items-center justify-content-center py-5">
        <div class="row w-100 justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div
                                class="bg-light-aratech rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 80px; height: 80px"
                            >
                                <i class="bi bi-shield-lock-fill fs-1"></i>
                            </div>
                            <h4 class="fw-bold text-dark">Verifikasi Keamanan</h4>
                            <p class="text-muted small mt-2">Halo <strong>{{ $signature->external_name }}</strong>,<br />
                            Sistem membutuhkan 6 digit Kode OTP untuk memverifikasi identitas Anda sebelum membuka dokumen.</p>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger py-2 small text-center fw-semibold rounded-3">
                                <i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('signatures.public.verify-otp', $signature->token) }}" method="POST">
                            @csrf
                            <div class="mb-4 d-flex justify-content-center">
                                <input
                                    type="text"
                                    name="otp_code"
                                    class="form-control form-control-lg otp-input text-aratech rounded-3"
                                    maxlength="6"
                                    autocomplete="off"
                                    required
                                    placeholder="••••••"
                                />
                            </div>

                            <button type="submit" class="btn btn-aratech w-100 py-3 fw-bold rounded-3 shadow-sm fs-5">
                                <i class="bi bi-check2-circle me-2"></i> Verifikasi & Masuk
                            </button>
                        </form>

                        <div class="text-center mt-4 pt-3 border-top">
                            <p
                                class="text-muted"
                                style="font-size: 0.75rem"
                            >Kode OTP dikirimkan bersamaan dengan link akses dokumen oleh sistem <strong>PT Aratech Nusantara Indonesia</strong>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
