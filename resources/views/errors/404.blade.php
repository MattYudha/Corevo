<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #1a1f3c;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, sans-serif;
            color: #fff;
            margin: 0;
            overflow: hidden;
        }
        .error-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 3rem;
            max-width: 500px;
            width: 90%;
            text-align: center;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 900;
            line-height: 1;
            background: linear-gradient(135deg, #11cdef 0%, #1171ef 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        .error-title { font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; }
        .error-desc { color: rgba(255, 255, 255, 0.6); font-size: 0.95rem; margin-bottom: 2rem; }
        .btn-premium {
            background: linear-gradient(135deg, #5e72e4 0%, #825ee4 100%);
            border: none;
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 12px;
            font-weight: 700;
            transition: all 0.3s;
            text-decoration: none;
        }
        .btn-premium:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(94, 114, 228, 0.3); color: #fff; }
        .logo-box { margin-bottom: 2rem; }
        .logo-box img { height: 60px; filter: brightness(0) invert(1); opacity: 0.8; }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="logo-box">
            <img src="{{ asset('corevo-logo.png') }}" alt="Logo">
        </div>
        <div class="error-code">404</div>
        <h2 class="error-title">Halaman Tidak Ditemukan</h2>
        <p class="error-desc">
            Maaf, halaman yang Anda cari tidak dapat ditemukan atau telah dipindahkan.
        </p>
        <a href="{{ url('/dashboard') }}" class="btn btn-premium">
            <i class="bi bi-house-door me-2"></i> Kembali ke Dashboard
        </a>
    </div>
</body>
</html>
