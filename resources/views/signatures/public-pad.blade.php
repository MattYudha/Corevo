<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Portal Penandatanganan Dokumen | PT Aratech Nusantara Indonesia</title>

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

        /* CUSTOM THEME ARATECH */
        .text-aratech {
            color: var(--aratech-color) !important;
        }
        .bg-aratech {
            background-color: var(--aratech-color) !important;
            color: white;
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
        .bg-light-aratech {
            background-color: #e0f6f4 !important;
            color: var(--aratech-color) !important;
        }

        /* Styling Canvas TTD */
        .signature-wrapper {
            position: relative;
            width: 100%;
            height: 300px;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            background-color: #ffffff;
            overflow: hidden;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .signature-wrapper canvas {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            touch-action: none; /* KUNCI ANTI-SCROLL HP */
            cursor: crosshair;
            z-index: 10;
        }

        .canvas-placeholder {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 1;
            pointer-events: none;
        }
    </style>
</head>
<body>
    @php
        // LOGIKA RENDER PDF OTOMATIS
        $document = $signature->signable ?? null;
        $pdfHtml = '';

        if ($document && class_basename($document) === 'Letter') {
            $letter = $document;
            $config = \App\Models\LetterConfiguration::first();
            $pdfHtml = view('letters.pdf', compact('letter', 'config'))->render();

            $pdfHtml = str_replace(
                public_path('img/logo-aratech-document.png'),
                asset('img/logo-aratech-document.png'),
                $pdfHtml,
            );
            $pdfHtml = str_replace(
                '{TOTAL_PAGES_PLACEHOLDER}',
                '<em style="color:#aaa;">(Dihitung saat diunduh)</em>',
                $pdfHtml,
            );

            $injectedCSS = '<style>
                                                @media screen {
                                                    html { background-color: #e9ecef; padding: 20px 0; }
                                                    body {
                                                        background-color: #ffffff; width: 210mm; min-height: 297mm;
                                                        margin: 0 auto !important; padding: 15mm 20mm 30mm 20mm !important;
                                                        box-sizing: border-box; box-shadow: 0 5px 15px rgba(0,0,0,0.2); position: relative;
                                                    }
                                                    .footer { position: absolute !important; bottom: 10mm !important; left: 20mm !important; right: 20mm !important; }
                                                }
                                            </style></head>';
            $pdfHtml = str_replace('</head>', $injectedCSS, $pdfHtml);
        }
    @endphp

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

    <div class="container-fluid px-4 py-4">
        <div class="row g-4">
            <div class="col-lg-7 col-xl-8">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-2">
                        <h5 class="fw-bold mb-1">
                            <i class="bi bi-file-earmark-text text-aratech me-2"></i> Pratinjau Dokumen
                        </h5>
                        <p class="small text-muted mb-0">Harap baca dan periksa dokumen ini sebelum Anda membubuhkan tanda tangan.</p>
                    </div>
                    <div class="card-body p-0 border-top bg-light" style="border-radius: 0 0 1rem 1rem">
                        <div
                            style="
                                overflow-y: auto;
                                overflow-x: auto;
                                -webkit-overflow-scrolling: touch;
                                height: 100% !important;
                                min-height: 600px;
                                background-color: #e9ecef;
                            "
                        >
                            <div class="d-flex justify-content-center" style="min-width: 210mm">
                                @if ($pdfHtml)
                                    <iframe
                                        src="data:text/html;charset=utf-8;base64,{{ base64_encode($pdfHtml) }}"
                                        style="
                                            width: 100%;
                                            height: 100%;
                                            min-height: 1200px;
                                            border: none;
                                            background-color: #e9ecef;
                                        "
                                        title="Pratinjau Dokumen"
                                        scrolling="yes"
                                    ></iframe>
                                @else
                                    <div class="text-center align-self-center text-muted mt-5 pt-5">
                                        <i class="bi bi-file-x fs-1 mb-2"></i>
                                        <p>Pratinjau dokumen tidak tersedia.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-xl-4">
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-body p-4">
                        <h6
                            class="fw-bold mb-3 text-muted text-uppercase"
                            style="font-size: 0.8rem; letter-spacing: 1px"
                        >
                            Data Penandatangan
                        </h6>

                        <div class="d-flex align-items-center mb-3">
                            <div
                                class="bg-light-aratech rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm"
                                style="width: 48px; height: 48px"
                            >
                                <i class="bi bi-person-fill fs-4"></i>
                            </div>
                            <div>
                                <strong
                                    class="d-block text-dark"
                                    style="font-size: 1.1rem"
                                    >{{ $signature->external_name ?? 'Pihak Eksternal' }}</strong
                                >
                                <span class="small text-muted">{{ $signature->external_email ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="bg-light rounded-3 p-3 border">
                            <div class="row g-2 small">
                                <div class="col-5 text-muted">Perusahaan</div>
                                <div class="col-7 fw-bold text-end text-dark">
                                    {{ $signature->external_company ?? '-' }}
                                </div>

                                <div class="col-5 text-muted">Jabatan</div>
                                <div class="col-7 fw-bold text-end text-dark">
                                    {{ $signature->external_title ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <hr class="my-4" />

                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-journal-text text-aratech me-2"></i> Panduan Pengisian:
                        </h6>
                        <ul class="text-muted small ps-3 mb-0" style="line-height: 1.7">
                            <li>Gunakan <strong>jari</strong> atau <strong>mouse</strong> untuk menggambar.</li>
                            <li>
                                Sistem dilengkapi <strong>Auto-Smooth AI</strong>. Garis yang bergetar akan otomatis
                                diperhalus sesaat setelah Anda mengangkat jari/mouse.
                            </li>
                            <li>Pastikan gambar tidak terpotong tepi kotak.</li>
                        </ul>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold mb-1 text-dark">
                            <i class="bi bi-vector-pen text-aratech me-2"></i> Tanda Tangan Anda
                        </h5>
                        <p class="small text-muted mb-0">Goreskan tanda tangan Anda pada kotak di bawah ini.</p>
                    </div>
                    <div class="card-body p-4 pt-3">
                        <form
                            id="signatureForm"
                            action="{{ route('signatures.public.submit', $signature->token ?? '') }}"
                            method="POST"
                        >
                            @csrf
                            <input type="hidden" name="signature_image" id="signature_image" required />

                            <div class="signature-wrapper mb-4">
                                <canvas id="signatureCanvas"></canvas>
                                <div class="canvas-placeholder" id="canvasPlaceholder">
                                    <i class="bi bi-pen text-muted opacity-25" style="font-size: 2.5rem"></i><br />
                                    <span class="text-muted small fw-medium mt-2 d-block"
                                        >Mulai menggambar di sini</span
                                    >
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="button" id="saveBtn" class="btn btn-aratech fw-bold py-2 shadow-sm fs-6">
                                    <i class="bi bi-check2-circle me-1"></i> Selesaikan Tanda Tangan
                                </button>
                                <button
                                    type="button"
                                    id="clearBtn"
                                    class="btn btn-light text-danger fw-semibold py-2 border shadow-sm"
                                >
                                    <i class="bi bi-eraser-fill me-1"></i> Ulangi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="text-muted small">
                        <i class="bi bi-shield-check text-aratech fs-6 align-middle me-1"></i>
                        Dilindungi oleh sistem enkripsi PT Aratech Nusantara Indonesia.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('signatureCanvas');
            const placeholder = document.getElementById('canvasPlaceholder');
            const clearBtn = document.getElementById('clearBtn');
            const saveBtn = document.getElementById('saveBtn');
            const form = document.getElementById('signatureForm');
            const signatureInput = document.getElementById('signature_image');

            // Inisialisasi Signature Pad (Setting Auto-Smooth)
            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(0, 0, 0, 0)',
                penColor: 'rgb(0, 0, 0)',
                minWidth: 1.0,
                maxWidth: 3.5,
                velocityFilterWeight: 0.8, // Bikin transisi tebal-tipisnya kalem
                minDistance: 1, // Kita set kecil, biar Auto-Smooth yang ambil alih
            });

            // FUNGSI FIX DPI SCALING
            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext('2d').scale(ratio, ratio);

                signaturePad.clear();
                placeholder.style.display = 'block';
            }

            window.addEventListener('resize', resizeCanvas);
            resizeCanvas();

            // Hilangkan tulisan pas mulai coret
            signaturePad.addEventListener('beginStroke', () => {
                placeholder.style.display = 'none';
            });

            // ==============================================================
            // ALGORITMA AUTO-SMOOTH (JALAN OTOMATIS SAAT LEPAS MOUSE/JARI)
            // ==============================================================
            signaturePad.addEventListener('endStroke', () => {
                const data = signaturePad.toData();
                if (data.length === 0) return;

                // Ambil garis (stroke) yang paling terakhir dibikin
                const lastStroke = data[data.length - 1];

                // Kalau titiknya kedikitan (cuma titik doang), ga usah di-smooth
                if (lastStroke.points.length <= 4) return;

                const smoothedPoints = [];
                smoothedPoints.push(lastStroke.points[0]); // Amankan titik awal

                let lastPoint = lastStroke.points[0];

                // Cek jarak antar titik. Abaikan titik-titik yang terlalu mepet (penyebab getar)
                for (let i = 1; i < lastStroke.points.length - 1; i++) {
                    const point = lastStroke.points[i];
                    const dx = point.x - lastPoint.x;
                    const dy = point.y - lastPoint.y;
                    const distance = Math.sqrt(dx * dx + dy * dy);

                    // Buang titik yang jaraknya kurang dari 8 pixel
                    if (distance > 8) {
                        smoothedPoints.push(point);
                        lastPoint = point;
                    }
                }

                smoothedPoints.push(lastStroke.points[lastStroke.points.length - 1]); // Amankan titik akhir

                // Timpa data stroke lama dengan yang udah di-smooth
                data[data.length - 1].points = smoothedPoints;

                // Disable pad sedetik biar ga crash, terus gambar ulang kanvasnya
                signaturePad.off();
                signaturePad.clear();
                signaturePad.fromData(data);
                signaturePad.on();
            });

            // Tombol Ulangi
            clearBtn.addEventListener('click', function () {
                signaturePad.clear();
                placeholder.style.display = 'block';
            });

            // Tombol Simpan
            saveBtn.addEventListener('click', function () {
                if (signaturePad.isEmpty()) {
                    alert('Maaf, tanda tangan tidak boleh kosong!');
                    return;
                }

                if (
                    confirm(
                        'Apakah Anda yakin bentuk tanda tangan sudah benar dan sesuai? Dokumen akan langsung disahkan.',
                    )
                ) {
                    const dataURL = signaturePad.toDataURL('image/png');
                    signatureInput.value = dataURL;

                    // Ganti teks tombol saat proses loading
                    saveBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';
                    saveBtn.disabled = true;
                    clearBtn.disabled = true;

                    form.submit();
                }
            });
        });
    </script>
</body>
</html>
