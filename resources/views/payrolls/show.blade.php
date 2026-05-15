@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><i class="bi bi-eye"></i> Payroll Detail</h3>
                <p class="text-subtitle text-muted">View complete details of salary payment for period {{ $payroll->period_label }}</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('payrolls.index') }}">Payroll</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View Detail</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    {{-- action buttons --}}
    <div class="row mb-3">
        <div class="col-12 d-flex flex-wrap gap-2 justify-content-start justify-content-md-end">
            <a href="{{ route('payrolls.index') }}" class="btn btn-secondary btn-sm px-3 rounded-pill">
                <i class="bi bi-arrow-left"></i> Back
            </a>

            @if(\App\Constants\Roles::hasFullFinanceAccess(session('role')) && $payroll->status !== 'paid')
                <a href="{{ route('payrolls.edit', $payroll->id) }}" class="btn btn-warning btn-sm px-3 rounded-pill">
                    <i class="bi bi-pencil"></i> Edit Data
                </a>
            @endif

            @if($payroll->status === 'paid')
                <a href="{{ route('payrolls.print', $payroll->id) }}" target="_blank" class="btn btn-primary btn-sm px-3 shadow-sm rounded-pill">
                    <i class="bi bi-printer"></i> Print Payslip
                </a>
            @else
                <button class="btn btn-outline-secondary btn-sm px-3 rounded-pill" disabled data-bs-toggle="tooltip" title="Payslip can only be printed if status is 'Paid'.">
                    <i class="bi bi-lock-fill"></i> Print (Locked)
                </button>
            @endif
        </div>
    </div>
</div>

<section class="section">
    <div class="row">
        {{-- left card: profile & status --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body py-4">
                    <div class="text-center mb-4">
                        <div class="avatar avatar-xl bg-primary text-white mb-3 d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px; border-radius: 50%; font-size: 1.8rem; font-weight: bold;">
                            {{ strtoupper(substr($payroll->employee?->fullname, 0, 1)) }}
                        </div>
                        <h5 class="fw-bold mb-1">{{ $payroll->employee?->fullname }}</h5>
                        <p class="text-muted small mb-0">{{ $payroll->employee?->position?->name ?? 'Employee' }}</p>
                        <div class="mt-3">
                            <span class="badge {{ $payroll->status === 'paid' ? 'bg-success' : ($payroll->status === 'approved' ? 'bg-info' : 'bg-secondary') }} px-3 py-2 rounded-pill">
                                {{ strtoupper($payroll->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="divider border-bottom mb-3"></div>

                    <div class="list-info">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small text-uppercase fw-bold">NIK</span>
                            <span class="small fw-bold text-dark">{{ $payroll->employee?->nik ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small text-uppercase fw-bold">NPWP</span>
                            <span class="small fw-bold text-dark">{{ $payroll->employee?->npwp ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small text-uppercase fw-bold">Period</span>
                            <span class="small fw-bold text-dark">{{ $payroll->period_label }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small text-uppercase fw-bold">Payment Date</span>
                            <span class="small fw-bold text-dark">{{ $payroll->pay_date ? \Carbon\Carbon::parse($payroll->pay_date)->translatedFormat('d M Y') : '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-0">
                            <span class="text-muted small text-uppercase fw-bold">Ref No.</span>
                            <span class="small text-dark">#{{ str_pad($payroll->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- right card: earnings & deductions details --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom py-3">
                    <h5 class="card-title mb-0"><i class="bi bi-calculator me-2"></i> Payroll Calculation</h5>
                </div>
                <div class="card-body p-0">
                    <div class="row g-0">
                        {{-- earnings --}}
                        <div class="col-md-6 border-end p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-success text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi bi-plus-lg mb-2"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-success uppercase">Earnings</h6>
                            </div>
                            
                            <table class="table table-sm table-borderless small">
                                <tr>
                                    <td>Basic Salary</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($payroll->salary, 0, ',', '.') }}</td>
                                </tr>
                                @if($payroll->overtime_amount > 0)
                                <tr>
                                    <td>Overtime ({{ $payroll->overtime_hours }} Hours)</td>
                                    <td class="text-end text-dark">Rp {{ number_format($payroll->overtime_amount, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                @php 
                                    $tunjangan = $payroll->transport_allowance + $payroll->meal_allowance + $payroll->position_allowance;
                                    $bonus = $payroll->performance_bonus + $payroll->attendance_bonus + $payroll->other_bonus;
                                @endphp
                                @if($tunjangan > 0)
                                <tr>
                                    <td>Total Allowance</td>
                                    <td class="text-end text-dark">Rp {{ number_format($tunjangan, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                @if($bonus > 0)
                                <tr>
                                    <td>Bonus & Incentives</td>
                                    <td class="text-end text-dark">Rp {{ number_format($bonus, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                            </table>
                            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                                <span class="fw-bold small">Total Earnings (A)</span>
                                <span class="fw-bold text-success">Rp {{ number_format($payroll->total_earnings, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        {{-- deductions --}}
                        <div class="col-md-6 p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-danger text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="bi bi-dash-lg mb-2"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-danger uppercase">Deductions</h6>
                            </div>

                            <table class="table table-sm table-borderless small">
                                @if($payroll->late_deduction > 0)
                                <tr>
                                    <td>Lateness</td>
                                    <td class="text-end text-danger fw-bold">- Rp {{ number_format($payroll->late_deduction, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                @if($payroll->absent_deduction > 0)
                                <tr>
                                    <td>Absence</td>
                                    <td class="text-end text-danger fw-bold">- Rp {{ number_format($payroll->absent_deduction, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                @if($payroll->bpjs_kes + $payroll->bpjs_tk > 0)
                                <tr>
                                    <td>BPJS Contribution</td>
                                    <td class="text-end text-danger fw-bold">- Rp {{ number_format($payroll->bpjs_kes + $payroll->bpjs_tk, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                @php $potLain = $payroll->pph21 + $payroll->penalty_amount + $payroll->other_deduction; @endphp
                                @if($potLain > 0)
                                <tr>
                                    <td>Taxes & Others</td>
                                    <td class="text-end text-danger fw-bold">- Rp {{ number_format($potLain, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                            </table>
                            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                                <span class="fw-bold small">Total Deductions (B)</span>
                                <span class="fw-bold text-danger">Rp {{ number_format($payroll->total_deductions, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- footer card: take home pay (new polish) --}}
                    <div class="card-footer p-4 border-top-0" style="background-color: #f0f5ff; border-left: 6px solid #0d6efd; border-radius: 0 0 0.7rem 0.7rem;">
                        <div class="row align-items-center">
                            <div class="col-md-7 mb-3 mb-md-0">
                                <h6 class="fw-bold text-uppercase small text-primary mb-1">
                                    <i class="bi bi-cash-stack me-2"></i> Net Salary Received (Take Home Pay) (A - B)
                                </h6>
                                @if(class_exists('App\Helpers\Terbilang'))
                                    <p class="mb-0 small fst-italic text-secondary" style="letter-spacing: 0.5px;">
                                        "{{ \App\Helpers\Terbilang::make($payroll->net_salary) }} Rupiah"
                                    </p>
                                @endif
                            </div>
                            <div class="col-md-5 text-md-end">
                                <h2 class="fw-bold text-primary mb-0" style="text-shadow: 1px 1px 1px rgba(0,0,0,0.05);">
                                    Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush