@extends ('layouts.dashboard')

@section ('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Letter Configuration</h3>
                    <p class="text-subtitle text-muted">Configure letterhead, company identity, and automatic document numbering format.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Letter Configuration</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="page-content">
        <section class="section">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible shadow-sm border-0 rounded-3 show fade mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('letter-configurations.update') }}" method="POST">
                @csrf
                <div class="row mb-4">
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0 rounded-3 mb-4">
                            <div class="card-header bg-transparent border-bottom-0 pt-4 px-4">
                                <h5 class="mb-0 fw-bold"><i class="bi bi-building text-primary me-2"></i>Letterhead</h5>
                            </div>
                            <div class="card-body px-4 pb-4">
                                <div class="row row-gap-3">
                                    <div class="col-12">
                                        <div class="form-group mb-0">
                                            <label class="form-label fw-semibold"
                                                >Company Name <span class="text-danger">*</span></label
                                            >
                                            <input
                                                type="text"
                                                id="input_company_name"
                                                name="company_name"
                                                class="form-control"
                                                value="{{ old('company_name', $config->company_name) }}"
                                                required
                                                placeholder="Example: PT. ARATECH NUSANTARA INDONESIA"
                                            />
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group mb-0">
                                            <label class="form-label fw-semibold">Full Address</label>
                                            <textarea
                                                id="input_company_address"
                                                name="company_address"
                                                class="form-control"
                                                rows="3"
                                                placeholder="Example: The Plaza Office Tower 41st Floor..."
                                                >{{ old('company_address', $config->company_address) }}</textarea
                                            >
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label class="form-label fw-semibold">Phone Number</label>
                                            <input
                                                type="text"
                                                id="input_company_phone"
                                                name="company_phone"
                                                class="form-control"
                                                value="{{ old('company_phone', $config->company_phone) }}"
                                                placeholder="021-XXXXX"
                                            />
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label class="form-label fw-semibold">Official Email</label>
                                            <input
                                                type="email"
                                                id="input_company_email"
                                                name="company_email"
                                                class="form-control"
                                                value="{{ old('company_email', $config->company_email) }}"
                                                placeholder="info@company.com"
                                            />
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label class="form-label fw-semibold">Website</label>
                                            <input
                                                type="text"
                                                id="input_company_website"
                                                name="company_website"
                                                class="form-control"
                                                value="{{ old('company_website', $config->company_website) }}"
                                                placeholder="www.company.com"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0 rounded-3 mb-4 h-100">
                            <div class="card-header bg-transparent border-bottom-0 pt-4 px-4">
                                <h5 class="mb-0 fw-bold">
                                    <i class="bi bi-file-earmark-code text-primary me-2"></i> Format & System
                                </h5>
                            </div>
                            <div class="card-body px-4 pb-4">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-semibold"
                                        >Letter Numbering Format <span class="text-danger">*</span></label
                                    >
                                    <input
                                        type="text"
                                        id="input_letter_number"
                                        name="letter_number_format"
                                        class="form-control font-monospace"
                                        value="{{ old('letter_number_format', $config->letter_number_format) }}"
                                        required
                                    />
                                    <small class="text-muted mt-2 d-block">
                                        Automatic variables:<br />
                                        <code class="bg-light px-1 rounded">{NUMBER}</code> = Sequence Number<br />
                                        <code class="bg-light px-1 rounded">{DEPT}</code> = Department<br />
                                        <code class="bg-light px-1 rounded">{MONTH}</code> = Month (I-XII)<br />
                                        <code class="bg-light px-1 rounded">{YEAR}</code> = Year
                                    </small>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label fw-semibold">Document Footer Text</label>
                                    <textarea
                                        id="input_letterhead_footer"
                                        name="letterhead_footer"
                                        class="form-control"
                                        rows="2"
                                        placeholder="Small text at the bottom of the document..."
                                        >{{ old('letterhead_footer', $config->letterhead_footer) }}</textarea
                                    >
                                </div>

                                <div class="d-grid mt-auto">
                                    <button type="submit" class="btn btn-primary fw-semibold shadow-sm py-2">
                                        <i class="bi bi-save me-1"></i> Save Configuration
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            @php
                // prepare dynamic live preview data
                $romanMonths = [
                    1 => 'I',
                    2 => 'II',
                    3 => 'III',
                    4 => 'IV',
                    5 => 'V',
                    6 => 'VI',
                    7 => 'VII',
                    8 => 'VIII',
                    9 => 'IX',
                    10 => 'X',
                    11 => 'XI',
                    12 => 'XII',
                ];
                $currentRomanMonth = $romanMonths[now()->month];
                $currentDept = strtoupper(Auth::user()->employee->department->name ?? 'GENERAL');

                $dummyNumber = str_replace(
                    ['{NUMBER}', '{DEPT}', '{MONTH}', '{YEAR}'],
                    ['056', $currentDept, $currentRomanMonth, now()->year],
                    $config->letter_number_format ?? '{NUMBER}/{DEPT}/{MONTH}/{YEAR}',
                );
            @endphp

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-2">
                            <h5 class="fw-bold mb-4"><i class="bi bi-display text-primary me-2"></i> Web Preview</h5>
                            <div
                                class="alert alert-light-secondary py-2 px-3 d-flex align-items-start"
                                style="font-size: 0.85rem"
                            >
                                <i class="bi bi-info-circle-fill text-secondary me-3 fs-5" style="line-height: 0.8"></i>
                                <span class="mb-0">
                                    The preview below has been adjusted to exactly match the physical PDF printout.
                                </span>
                            </div>
                        </div>

                        <div
                            class="card-body p-0"
                            style="
                                overflow-x: auto;
                                -webkit-overflow-scrolling: touch;
                                border-radius: 0 0 1rem 1rem;
                                border-top: 1px solid #eee;
                            "
                        >
                            <div style="background-color: #e9ecef; padding: 20px 0; min-width: 210mm">
                                <div
                                    id="pdf-preview-container"
                                    style="
                                        background: #ffffff;
                                        width: 210mm;
                                        min-height: 297mm;
                                        margin: 0 auto;
                                        padding: 15mm 20mm 30mm 20mm;
                                        box-sizing: border-box;
                                        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
                                        color: #000;
                                        font-family: 'Helvetica', 'Arial', sans-serif;
                                        font-size: 11px;
                                        line-height: 1.15;
                                        position: relative;
                                    "
                                >
                                    <table style="width: 100%; margin-bottom: 5px; border-collapse: collapse">
                                        <tr>
                                            <td style="width: 25%; text-align: left; vertical-align: middle">
                                                <img
                                                    src="{{ asset('img/logo-aratech-document.png') }}"
                                                    alt="Logo"
                                                    style="max-height: 65px; max-width: 170px; object-fit: contain"
                                                />
                                            </td>

                                            <td style="width: 88%; text-align: right; vertical-align: middle">
                                                <h1
                                                    id="preview_company_name"
                                                    style="
                                                        margin: 0 0 4px 0;
                                                        font-size: 16px;
                                                        font-weight: bold;
                                                        text-transform: uppercase;
                                                        letter-spacing: 0.5px;
                                                        color: #000;
                                                    "
                                                >
                                                    {{
                                                        $config->company_name ??
                                                            'PT. ARATECH NUSANTARA INDONESIA'
                                                    }}
                                                </h1>
                                                <p
                                                    id="preview_company_address"
                                                    style="margin: 0; font-size: 9px; color: #222; line-height: 1.3"
                                                >
                                                    {!!
                                                        nl2br(
                                                            e($config->company_address ?? 'The Plaza Office Tower 41st Floor, Jl. M.H. Thamrin No.Kav. 28-30, DKI Jakarta'),
                                                        )
                                                    !!}
                                                </p>
                                                <p
                                                    style="
                                                        margin: 2px 0 0 0;
                                                        font-size: 9px;
                                                        color: #222;
                                                        line-height: 1.3;
                                                    "
                                                >
                                                    Website:
                                                    <span
                                                        id="preview_website"
                                                        >{{ $config->company_website ?? '-' }}</span
                                                    >
                                                    | Email:
                                                    <span id="preview_email">{{ $config->company_email ?? '-' }}</span>
                                                    | Phone:
                                                    <span id="preview_phone">{{ $config->company_phone ?? '-' }}</span>
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                    <div style="border-bottom: 3px solid #000; margin-bottom: 1px"></div>
                                    <div style="border-bottom: 1px solid #000; margin-bottom: 15px"></div>

                                    <table style="width: 100%; margin-bottom: 20px; font-size: 11px; color: #000">
                                        <tr>
                                            <td
                                                style="
                                                    width: 15%;
                                                    font-weight: bold;
                                                    vertical-align: top;
                                                    padding: 2px 0;
                                                "
                                            >
                                                Number
                                            </td>
                                            <td
                                                style="
                                                    width: 2%;
                                                    text-align: center;
                                                    vertical-align: top;
                                                    padding: 2px 0;
                                                "
                                            >
                                                :
                                            </td>
                                            <td
                                                style="width: 48%; vertical-align: top; padding: 2px 0"
                                                id="preview_letter_number"
                                            >
                                                {{ $dummyNumber }}
                                            </td>
                                            <td
                                                style="
                                                    width: 35%;
                                                    text-align: right;
                                                    vertical-align: top;
                                                    padding: 2px 0;
                                                "
                                            >
                                                Jakarta, {{ now()->format('F d, Y') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold; vertical-align: top; padding: 2px 0">
                                                Attachment
                                            </td>
                                            <td style="text-align: center; vertical-align: top; padding: 2px 0">:</td>
                                            <td colspan="2" style="vertical-align: top; padding: 2px 0">
                                                <em style="color: #aaa">(Calculated upon download)</em>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold; vertical-align: top; padding: 2px 0">
                                                Subject
                                            </td>
                                            <td style="text-align: center; vertical-align: top; padding: 2px 0">:</td>
                                            <td colspan="2" style="vertical-align: top; padding: 2px 0">
                                                <strong style="text-decoration: underline"
                                                    >Missed Attendance Letter</strong
                                                >
                                            </td>
                                        </tr>
                                    </table>

                                    <div
                                        style="
                                            text-align: justify;
                                            margin-bottom: 20px;
                                            font-size: 12px;
                                            color: #000000;
                                            line-height: 1.15;
                                            word-wrap: break-word;
                                            overflow-wrap: break-word;
                                        "
                                    >
                                        Dear Sir/Madam,<br /><br />
                                        In accordance with the company terms and policies outlined in the Employee
                                        Handbook, this letter serves to inform you that the system has recorded an
                                        anomaly in your daily attendance.<br /><br />
                                        This letter is automatically generated and this preview is an accurate
                                        representation of the PDF document that will be downloaded by employees or
                                        external parties. All line spacing, margins, and text styling adjustments follow
                                        formal letterhead standards.
                                    </div>

                                    <div
                                        style="
                                            margin-top: 30px;
                                            padding-bottom: 10mm;
                                            page-break-inside: avoid;
                                            color: #000;
                                        "
                                    >
                                        <table style="width: 100%; margin-top: 20px">
                                            <tr>
                                                <td style="width: 60%"></td>
                                                <td style="width: 40%; text-align: center; vertical-align: bottom">
                                                    <span style="font-size: 11px; color: #000">
                                                        HR Administrator<br />
                                                        <span id="preview_sig_company">{{
                                                            $config->company_name ??
                                                                'PT. ARATECH NUSANTARA INDONESIA'
                                                        }}</span> </span
                                                    ><br />

                                                    <div
                                                        style="
                                                            height: 60px;
                                                            margin: 10px 0;
                                                            border: 1px dashed #ccc;
                                                            display: flex;
                                                            align-items: center;
                                                            justify-content: center;
                                                            color: #aaa;
                                                            font-style: italic;
                                                            font-size: 10px;
                                                        "
                                                    >
                                                        (Signature Area)
                                                    </div>
                                                    <br />

                                                    <strong style="font-size: 12px; text-decoration: underline">
                                                        {{ Auth::user()->name ?? 'Administrator' }} </strong
                                                    ><br />
                                                    <span style="font-size: 8px; color: #999">Signed via OpenSSL</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <div
                                        style="
                                            position: absolute;
                                            bottom: 10mm;
                                            left: 20mm;
                                            right: 20mm;
                                            font-size: 8px;
                                            color: #777;
                                            border-top: 1px dashed #ccc;
                                            padding-top: 5px;
                                        "
                                    >
                                        <table style="width: 100%">
                                            <tr>
                                                <td style="text-align: left; vertical-align: bottom">
                                                    <em
                                                        id="preview_footer"
                                                        >{!! nl2br(e($config->letterhead_footer ?? '')) !!}</em
                                                    ><br />
                                                    <strong style="color: #444">COREVO Digital Document</strong> -
                                                    System preview.<br />
                                                    Print Time: {{ now()->format('d/m/Y H:i') }}
                                                </td>
                                                <td style="text-align: right; vertical-align: bottom">
                                                    <div style="display: inline-block; text-align: center">
                                                        <div
                                                            style="
                                                                width: 50px;
                                                                height: 50px;
                                                                border: 1px solid #eee;
                                                                padding: 2px;
                                                                background: #fff;
                                                                display: flex;
                                                                align-items: center;
                                                                justify-content: center;
                                                                font-size: 8px;
                                                                color: #aaa;
                                                            "
                                                        >
                                                            [ QR ]
                                                        </div>
                                                        <p
                                                            style="font-size: 7px; color: #aaa; margin: 3px 0 0 0"
                                                        >Scan to Verify</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push ('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputName = document.getElementById('input_company_name');
            const inputAddress = document.getElementById('input_company_address');
            const inputPhone = document.getElementById('input_company_phone');
            const inputEmail = document.getElementById('input_company_email');
            const inputWebsite = document.getElementById('input_company_website');
            const inputFooter = document.getElementById('input_letterhead_footer');
            const inputNumber = document.getElementById('input_letter_number');

            const prevName = document.getElementById('preview_company_name');
            const prevAddress = document.getElementById('preview_company_address');
            const prevPhone = document.getElementById('preview_phone');
            const prevEmail = document.getElementById('preview_email');
            const prevWebsite = document.getElementById('preview_website');
            const prevFooter = document.getElementById('preview_footer');
            const prevSigCompany = document.getElementById('preview_sig_company');
            const prevNumber = document.getElementById('preview_letter_number');

            function updatePreview() {
                prevName.innerText = inputName.value || 'PT. ARATECH NUSANTARA INDONESIA';
                prevSigCompany.innerText = inputName.value || 'PT. ARATECH NUSANTARA INDONESIA';
                prevAddress.innerHTML = (
                    inputAddress.value || 'The Plaza Office Tower 41st Floor, Jl. M.H. Thamrin No.Kav. 28-30, DKI Jakarta'
                ).replace(/\n/g, '<br>');
                prevPhone.innerText = inputPhone.value || '-';
                prevEmail.innerText = inputEmail.value || '-';
                prevWebsite.innerText = inputWebsite.value || '-';
                prevFooter.innerHTML = (inputFooter.value || '').replace(/\n/g, '<br>');

                let format = inputNumber.value || '{NUMBER}/{DEPT}/{MONTH}/{YEAR}';
                format = format
                    .replace('{NUMBER}', '056')
                    .replace('{DEPT}', '{{ $currentDept }}')
                    .replace('{MONTH}', '{{ $currentRomanMonth }}')
                    .replace('{YEAR}', '{{ now()->year }}');
                prevNumber.innerText = format;
            }

            inputName.addEventListener('input', updatePreview);
            inputAddress.addEventListener('input', updatePreview);
            inputPhone.addEventListener('input', updatePreview);
            inputEmail.addEventListener('input', updatePreview);
            inputWebsite.addEventListener('input', updatePreview);
            inputFooter.addEventListener('input', updatePreview);
            inputNumber.addEventListener('input', updatePreview);
        });
    </script>
@endpush
