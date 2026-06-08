@extends ('layouts.dashboard')

@section ('content')
    <div class="page-heading mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="d-flex align-items-center order-2 order-md-1 mt-3 mt-md-0">
                <a
                    href="{{ route('signatures.list', ['signable' => 'letter', 'id' => $id]) }}"
                    class="btn btn-secondary me-3"
                    title="Back"
                >
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>

                <div>
                    <h3 class="mb-0">Digital Signature</h3>
                    <p class="text-subtitle text-muted mb-0 mt-1">Please provide your signature to authenticate the document.</p>
                </div>
            </div>

            <nav aria-label="breadcrumb" class="breadcrumb-header order-1 order-md-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('letters.index') }}">Letters</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('letters.show', $id) }}">Letter Preview</a></li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('signatures.list', ['signable' => 'letter', 'id' => $id]) }}"
                            >Manage Signatures</a
                        >
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Digital Signature</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3 mb-4 position-sticky" style="top: 2rem">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-info-circle-fill text-primary me-2"></i> Document Information
                    </h5>

                    <div
                        class="alert alert-light-primary py-3 px-3 mb-4 rounded-3 d-flex align-items-start"
                        style="font-size: 0.85rem"
                    >
                        <i class="bi bi-shield-lock-fill text-primary me-2 fs-5" style="line-height: 1.2"></i>
                        <span
                            >This signature will be digitally encrypted and embedded directly into the related document
                            PDF file.</span
                        >
                    </div>

                    <div class="mb-3">
                        <span class="text-muted d-block" style="font-size: 0.85rem">Document Type</span>
                        <span class="badge bg-primary px-3 py-2 mt-1 rounded-3 fw-bold text-uppercase shadow-sm">
                            {{
                                request('signable') ??
                                    (request()->route('signable') ?? 'Document')
                            }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <span class="text-muted d-block" style="font-size: 0.85rem">System Reference ID</span>
                        <strong class="fw-bold" style="font-size: 1.1rem">
                            #{{ request('id') ?? (request()->route('id') ?? '-') }}
                        </strong>
                    </div>

                    <hr class="my-4" />

                    <h6 class="fw-bold mb-3"><i class="bi bi-journal-text me-2"></i> Input Guide:</h6>
                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.7">
                        <li>
                            Use your <strong>finger</strong> (on a mobile/tablet screen) or a <strong>mouse</strong> to
                            draw.
                        </li>
                        <li>
                            The system is equipped with <strong>Auto-Smooth AI</strong>. Jittery lines will be
                            automatically smoothed right after you lift your finger/mouse.
                        </li>
                        <li>Ensure the drawing is centered and not cut off at the edges.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-2">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-vector-pen text-primary me-2"></i> Signature Drawing Area
                    </h5>
                    <p class="small text-muted mb-0">Try to make your signature clear and similar to your wet signature on your ID card.</p>
                </div>

                <div class="card-body p-4 pt-2">
                    <form
                        id="signatureForm"
                        action="{{ route('signatures.store', ['signable' => request()->route('signable') ?? request('signable'), 'id' => request()->route('id') ?? request('id')]) }}"
                        method="POST"
                    >
                        @csrf
                        <input type="hidden" name="signature_image" id="signature_image" required />

                        <div class="signature-wrapper mb-4 shadow-sm">
                            <canvas id="signatureCanvas"></canvas>
                            <div class="canvas-placeholder" id="canvasPlaceholder">
                                <i class="bi bi-pencil-fill text-muted opacity-25" style="font-size: 3rem"></i><br />
                                <span class="text-muted opacity-50 small fw-semibold">Draw Your Signature Here</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                            <div class="d-flex gap-2 flex-wrap">
                                <button
                                    type="button"
                                    id="clearBtn"
                                    class="btn btn-danger fw-semibold px-4 py-2 rounded-3 shadow-sm"
                                >
                                    <i class="bi bi-eraser-fill me-1"></i> Clear
                                </button>

                                @if (isset($lastUsedSignature) && $lastUsedSignature->signature_image)
                                    <button
                                        type="button"
                                        id="useDefaultBtn"
                                        class="btn btn-primary fw-semibold px-3 py-2 rounded-3 shadow-sm"
                                    >
                                        <i class="bi bi-clock-history me-1"></i> Use Last Signature
                                    </button>
                                @endif
                            </div>

                            <button
                                type="button"
                                id="saveBtn"
                                class="btn btn-success fw-semibold px-5 py-2 rounded-3 shadow-sm flex-grow-1 flex-md-grow-0"
                            >
                                <i class="bi bi-check-circle me-1"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <style>
        .signature-wrapper {
            position: relative;
            width: 100%;
            height: 350px;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            background-color: #f8fafc;
            overflow: hidden;
        }

        .signature-wrapper canvas {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            touch-action: none;
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

        [data-bs-theme='dark'] .signature-wrapper {
            background-color: #1e1e2d;
            border-color: #323246;
        }

        [data-bs-theme='dark'] .signature-wrapper canvas {
            filter: invert(1);
        }
    </style>
@endsection

@push ('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('signatureCanvas');
            const placeholder = document.getElementById('canvasPlaceholder');
            const clearBtn = document.getElementById('clearBtn');
            const saveBtn = document.getElementById('saveBtn');
            const form = document.getElementById('signatureForm');
            const signatureInput = document.getElementById('signature_image');

            // tuned pad settings
            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(0, 0, 0, 0)',
                penColor: 'rgb(0, 0, 0)',
                minWidth: 1.0,
                maxWidth: 3.5,
                velocityFilterWeight: 0.8, // makes the thick-thin transition smooth
                minDistance: 1, // set small so auto-smooth takes over the fixes
            });

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

            // hide placeholder when drawing starts
            signaturePad.addEventListener('beginStroke', () => {
                placeholder.style.display = 'none';
            });

            // ==============================================================
            // auto-smooth algorithm (runs automatically upon mouse/finger release)
            // ==============================================================
            signaturePad.addEventListener('endStroke', () => {
                const data = signaturePad.toData();
                if (data.length === 0) return;

                // get the most recently drawn stroke
                const lastStroke = data[data.length - 1];

                // if there are too few points (just a dot), no need to smooth
                if (lastStroke.points.length <= 4) return;

                const smoothedPoints = [];
                smoothedPoints.push(lastStroke.points[0]); // secure the starting point

                let lastPoint = lastStroke.points[0];

                // check distance between points. ignore points that are too close (causes jitter)
                for (let i = 1; i < lastStroke.points.length - 1; i++) {
                    const point = lastStroke.points[i];
                    const dx = point.x - lastPoint.x;
                    const dy = point.y - lastPoint.y;
                    const distance = Math.sqrt(dx * dx + dy * dy);

                    // discard points with distance less than 8 pixels
                    if (distance > 8) {
                        smoothedPoints.push(point);
                        lastPoint = point;
                    }
                }

                smoothedPoints.push(lastStroke.points[lastStroke.points.length - 1]); // secure the ending point

                // overwrite old stroke data with the smoothed one
                data[data.length - 1].points = smoothedPoints;

                // disable pad for a second to avoid crashing, then redraw the canvas
                signaturePad.off();
                signaturePad.clear();
                signaturePad.fromData(data);
                signaturePad.on();
            });

            clearBtn.addEventListener('click', function () {
                signaturePad.clear();
                placeholder.style.display = 'block';
            });

            // load default signature feature
            @if (isset($lastUsedSignature) && $lastUsedSignature->signature_image)
            const useDefaultBtn = document.getElementById('useDefaultBtn');
            useDefaultBtn.addEventListener('click', function () {
                signaturePad.fromDataURL('{!! $lastUsedSignature->signature_image !!}');
                placeholder.style.display = 'none';
            });
            @endif

            saveBtn.addEventListener('click', function () {
                if (signaturePad.isEmpty()) {
                    alert('Signature cannot be empty!');
                    return;
                }

                const dataURL = signaturePad.toDataURL('image/png');
                signatureInput.value = dataURL;
                form.submit();
            });
        });
    </script>
@endpush
