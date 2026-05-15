<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $payroll->employee?->fullname }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            box-sizing: border-box;
        }

        body { 
            background-color: #525659;
            margin: 0;
            padding: 20px;
            display: flex; 
            justify-content: center; 
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        #slip-padd {
            width: 800px;
            min-height: 1130px;
            background-color: #ffffff;
            padding: 45px;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            color: #333333;
        }

        /* corevo primary colors */
        .text-teal { color: #44c0b5 !important; }
        .bg-teal { background-color: #44c0b5 !important; }
        .bg-teal-light { background-color: #eff9f8 !important; }
        .border-teal { border-color: #44c0b5 !important; }
        
        .border-soft { border-color: #eef0f2 !important; }
        .text-dark-gray { color: #4b5563 !important; }

        /* grid lock */
        .row { display: flex !important; flex-wrap: wrap !important; }
        .col-6 { flex: 0 0 auto !important; width: 50% !important; }
        .col-7 { flex: 0 0 auto !important; width: 58.33% !important; }
        .col-5 { flex: 0 0 auto !important; width: 41.66% !important; }
        .col-8 { flex: 0 0 auto !important; width: 66.66% !important; }
        .col-4 { flex: 0 0 auto !important; width: 33.33% !important; }
        .col-9 { flex: 0 0 auto !important; width: 75% !important; }
        .col-3 { flex: 0 0 auto !important; width: 25% !important; }

        .border-end { border-right: 1px solid #eef0f2 !important; }
        .text-end { text-align: right !important; }
        .text-start { text-align: left !important; }
        
        /* table modifications */
        .table-details td { padding: 4px 0; font-size: 0.85rem; }
    </style>
</head>
<body>

    <div id="slip-padd">
        {{-- letterhead --}}
        <div class="row align-items-center mb-4 pb-4 border-bottom border-soft border-2">
            <div class="col-9 d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center me-3 flex-shrink-0 text-teal" style="width: 55px; height: 55px; border-radius: 12px;">
                    <img src="{{ asset('img/aratech-logo-only.png') }}" id="sidebar-logo" class="logo-light" style="height:70px; width:auto; max-width:150px; object-fit:contain;">
                </div>
                <div>
                    <h4 class="fw-bolder mb-0 text-dark text-nowrap" style="letter-spacing: 0.5px; font-size: 1.4rem;">PT. ARATECH NUSANTARA INDONESIA</h4>
                    <p class="mb-0 text-muted" style="font-size: 0.8rem;">The Plaza Office Tower 41st Floor, Jl. M.H. Thamrin No.Kav. 28-30, DKI Jakarta</p>
                    <p class="mb-0 text-muted" style="font-size: 0.8rem;">Web: www.aratechnology.id | Email: office@aratechnology.id </p>
                </div>
            </div>
            
            <div class="col-3 text-end">
                <h5 class="fw-bolder mb-2 text-black" style="text-transform: uppercase; letter-spacing: 1px;">Salary <br> Slip</h5>
                <span class="badge border border-teal text-teal bg-white px-3 py-1" style="font-size: 0.75rem; border-radius: 20px;">
                    {{ strtoupper($payroll->status) }}
                </span>
            </div>
        </div>

        {{-- employee information (elegant with teal border-left) --}}
        <div class="p-3 mb-4 rounded bg-teal-light border-start border-teal" style="border-left-width: 4px !important;">
            <div class="row g-3">
                <div class="col-6">
                    <table class="table-details w-100 text-dark-gray">
                        <tr><td style="width: 110px;">Reference No.</td><td width="10">:</td><td class="fw-bold text-dark">PAY/{{ date('Y', strtotime($payroll->pay_date ?? now())) }}/{{ str_pad($payroll->id, 5, '0', STR_PAD_LEFT) }}</td></tr>
                        <tr><td>Employee Name</td><td>:</td><td class="fw-bold text-dark">{{ $payroll->employee?->fullname }}</td></tr>
                        <tr><td>Employee NIK</td><td>:</td><td class="text-dark">{{ $payroll->employee?->nik ?? '-' }}</td></tr>
                    </table>
                </div>
                <div class="col-6">
                    <table class="table-details w-100 text-dark-gray">
                        <tr><td style="width: 110px;">Payroll Period</td><td width="10">:</td><td class="fw-bold text-dark">{{ $payroll->period_label ?? DateTime::createFromFormat('!m', $payroll->period_month)->format('F').' '.$payroll->period_year }}</td></tr>
                        <tr><td>Print Date</td><td>:</td><td class="text-dark">{{ date('d M Y') }}</td></tr>
                        <tr><td>Payment Date</td><td>:</td><td class="text-dark">{{ $payroll->pay_date ? date('d M Y', strtotime($payroll->pay_date)) : '-' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- earnings & deductions details --}}
        <div class="row g-0 mb-4 border border-soft rounded overflow-hidden">
            
            {{-- left: earnings --}}
            <div class="col-6 border-end">
                <div class="p-2 px-3 border-bottom border-soft">
                    <h6 class="fw-bold mb-0 small text-teal text-uppercase" style="letter-spacing: 0.5px;">Earnings</h6>
                </div>
                <div class="p-3">
                    <table class="table table-sm table-borderless mb-0 small text-dark-gray">
                        <tr><td class="py-1">Basic Salary</td><td class="py-1 text-end text-dark">Rp {{ number_format($payroll->salary, 0, ',', '.') }}</td></tr>
                        @if($payroll->overtime_amount > 0) <tr><td class="py-1">Overtime</td><td class="py-1 text-end text-dark">Rp {{ number_format($payroll->overtime_amount, 0, ',', '.') }}</td></tr> @endif
                        @php $tunjangan = $payroll->transport_allowance + $payroll->meal_allowance + $payroll->position_allowance; @endphp
                        @if($tunjangan > 0) <tr><td class="py-1">Total Allowance</td><td class="py-1 text-end text-dark">Rp {{ number_format($tunjangan, 0, ',', '.') }}</td></tr> @endif
                        @php $bonus = $payroll->performance_bonus + $payroll->attendance_bonus + $payroll->other_bonus; @endphp
                        @if($bonus > 0) <tr><td class="py-1">Bonus & Incentives</td><td class="py-1 text-end text-dark">Rp {{ number_format($bonus, 0, ',', '.') }}</td></tr> @endif
                    </table>
                </div>
                <div class="p-2 px-3 border-top border-soft" style="background-color: #fafafa;">
                    <div class="d-flex justify-content-between fw-bold small">
                        <span class="text-dark">Total Earnings (A)</span><span class="text-teal">Rp {{ number_format($payroll->total_earnings, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- right: deductions --}}
            <div class="col-6">
                <div class="p-2 px-3 border-bottom border-soft">
                    <h6 class="fw-bold mb-0 small text-secondary text-uppercase" style="letter-spacing: 0.5px;">Deductions</h6>
                </div>
                <div class="p-3">
                    <table class="table table-sm table-borderless mb-0 small text-dark-gray">
                        @if($payroll->late_deduction > 0) <tr><td class="py-1">Lateness</td><td class="py-1 text-end text-dark">Rp {{ number_format($payroll->late_deduction, 0, ',', '.') }}</td></tr> @endif
                        @if($payroll->absent_deduction > 0) <tr><td class="py-1">Absence</td><td class="py-1 text-end text-dark">Rp {{ number_format($payroll->absent_deduction, 0, ',', '.') }}</td></tr> @endif
                        @if($payroll->bpjs_kes + $payroll->bpjs_tk > 0) <tr><td class="py-1">BPJS Contribution</td><td class="py-1 text-end text-dark">Rp {{ number_format($payroll->bpjs_kes + $payroll->bpjs_tk, 0, ',', '.') }}</td></tr> @endif
                        @php $potLain = $payroll->pph21 + $payroll->penalty_amount + $payroll->other_deduction; @endphp
                        @if($potLain > 0) <tr><td class="py-1">Others / PPh21</td><td class="py-1 text-end text-dark">Rp {{ number_format($potLain, 0, ',', '.') }}</td></tr> @endif
                    </table>
                </div>
                <div class="p-2 px-3 border-top border-soft" style="background-color: #fafafa;">
                    <div class="d-flex justify-content-between fw-bold small">
                        <span class="text-dark">Total Deductions (B)</span><span class="text-danger">Rp {{ number_format($payroll->total_deductions, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- take home pay --}}
        <div class="border-bottom border-soft rounded-4 px-4 py-4 mb-5 bg-white">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div 
                            class="rounded-circle bg-success"
                            style="width: 10px; height: 10px;">
                        </div>

                        <span 
                            class="fw-semibold text-muted text-uppercase"
                            style="font-size: 0.75rem; letter-spacing: 1px;">
                            Take Home Pay
                        </span>
                    </div>

                    @if(class_exists('App\Helpers\Terbilang'))
                        <p class="mb-0 text-secondary fst-italic" style="font-size: 0.85rem;">
                            "{{ \App\Helpers\Terbilang::make($payroll->net_salary) }} Rupiah"
                        </p>
                    @endif
                </div>

                <div class="text-end">
                    <h2 class="fw-bold mb-0 text-dark">
                        Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}
                    </h2>
                </div>

            </div>

        </div>

        {{-- signatures --}}
        <div class="row mt-5 pt-3">
            <div class="col-6 text-center">
                <p class="mb-5 pb-4 text-dark-gray small">Received by,</p>
                <h6 class="fw-bold mb-0 text-dark">{{ $payroll->employee?->fullname }}</h6>
                <div class="mx-auto border-top border-soft my-2" style="width: 200px; border-top-width: 2px !important;"></div>
                <p class="text-muted small">Employee</p>
            </div>
            <div class="col-6 text-center">
                <p class="mb-5 pb-4 text-dark-gray small">Acknowledged by,</p>
                <h6 class="fw-bold mb-0 text-dark">{{ auth()->user()->employee?->fullname ?? auth()->user()->name }}</h6>
                <div class="mx-auto border-top border-soft my-2" style="width: 200px; border-top-width: 2px !important;"></div>
                <p class="text-muted small">{{ session('role') ?? 'Finance Dept.' }}</p>
            </div>
        </div>

        {{-- footer notes --}}
        <div class="mt-5 pt-4 text-center">
            <p class="text-muted mb-0" style="font-size: 0.75rem;">
                This document is an official proof of employee salary payment for PT. Aratech Nusantara Indonesia.<br>
                Automatically printed on {{ date('d M Y H:i') }}.
            </p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('auto_pdf') === 'true') {
                
                document.body.style.backgroundColor = '#ffffff';
                document.body.style.padding = '0';
                
                const element = document.getElementById('slip-padd');
                element.style.boxShadow = 'none';

                const opt = {
                    margin:       0,
                    filename:     'payslip_{{ str_replace(" ", "_", $payroll->employee?->fullname ?? "Employee") }}_{{ DateTime::createFromFormat("!m", $payroll->period_month)->format("F") }}_{{ $payroll->period_year }}.pdf',
                    image:        { type: 'jpeg', quality: 1 },
                    html2canvas:  { scale: 2, windowWidth: 800, x: 0, y: 0 },
                    jsPDF:        { unit: 'px', format: [800, 1130], orientation: 'portrait' } 
                };

                html2pdf().set(opt).from(element).save().then(() => {
                    window.parent.postMessage('pdf_selesai', '*');
                });
            }
        }
    </script>
</body>
</html>