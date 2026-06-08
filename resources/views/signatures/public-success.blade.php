<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Penandatanganan Berhasil | PT Aratech Nusantara Indonesia</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />

    <style>
        :root {
            --aratech-color: #44c0b5;
        }
        body {
            background-color: #f3f4f6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .success-card {
            max-width: 500px;
            width: 100%;
        }
        .icon-circle {
            width: 100px;
            height: 100px;
            background-color: #e0f6f4;
            color: var(--aratech-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container px-4">
        <div class="card shadow border-0 rounded-3 success-card mx-auto">
            <div class="card-body p-5 text-center">
                <div class="icon-circle mb-4 shadow-sm">
                    <i class="bi bi-check-circle-fill" style="font-size: 3.5rem"></i>
                </div>

                <h3 class="fw-bold text-dark mb-3">Tanda Tangan Berhasil!</h3>
                <p
                    class="text-muted mb-4"
                    style="line-height: 1.6"
                >Terima kasih, dokumen telah berhasil disahkan secara digital. Sistem kami telah mengamankan tanda tangan Anda menggunakan teknologi enkripsi terkini.</p>

                <div class="bg-light rounded-3 p-3 mb-4 border">
                    <p class="small text-muted mb-0">
                        <i class="bi bi-shield-check text-success me-1"></i>
                        Anda kini dapat menutup tab atau halaman ini dengan aman.
                    </p>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <img
                        src="{{ asset('img/aratech-logo-only.png') }}"
                        alt="Logo Aratech"
                        style="height: 35px; object-fit: contain; margin-bottom: 5px"
                    />
                    <p class="small text-muted mb-0 fw-medium">PT Aratech Nusantara Indonesia</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
