@extends('layouts.dashboard')

@section('content')

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><i class="bi bi-plus-circle"></i> Create New Payroll</h3>
                <p class="text-subtitle text-muted">Calculate employee salary with complete details.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('payrolls.index') }}">Payrolls</a></li>
                        <li class="breadcrumb-item active">Create New Payroll</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('payrolls.store') }}" method="POST" id="payroll-form">
        @csrf

        {{-- section 1: employee info & period --}}
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light py-3">
                <h5 class="mb-0"><i class="bi bi-person-badge"></i> Employee & Period Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="employee_id" class="form-label fw-bold">Employee <span class="text-danger">*</span></label>
                            <select name="employee_id" id="employee_id" class="form-select" required>
                                <option value="">-- Select Employee --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" data-salary="{{ $emp->salary }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->fullname }} {{ $emp->emp_code ? '('.$emp->emp_code.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @php
                        // get jakarta time for default month & year
                        $now = \Carbon\Carbon::now('Asia/Jakarta');
                        $currentMonth = $now->format('n');
                        $currentYear = $now->format('Y');
                    @endphp

                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="period_month" class="form-label fw-bold">Month <span class="text-danger">*</span></label>
                            <select name="period_month" id="period_month" class="form-select" required>
                                @php $months = ['January','February','March','April','May','June','July','August','September','October','November','December']; @endphp
                                @foreach($months as $i => $m)
                                    <option value="{{ $i+1 }}" {{ old('period_month', $currentMonth) == ($i+1) ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-3">
                            <label for="period_year" class="form-label fw-bold">Year <span class="text-danger">*</span></label>
                            <select name="period_year" id="period_year" class="form-select" required>
                                @for($y = $currentYear - 1; $y <= $currentYear + 1; $y++)
                                    <option value="{{ $y }}" {{ old('period_year', $currentYear) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">&nbsp;</label>
                            <button type="button" id="btn-fetch-attendance" class="btn btn-outline-primary d-block w-100" disabled>
                                <i class="bi bi-cloud-download"></i> Calculate Attendance
                            </button>
                        </div>
                    </div>
                </div>
                <div id="attendance-info" class="alert alert-info d-none">
                    <i class="bi bi-info-circle"></i> <span id="attendance-info-text"></span>
                </div>
            </div>
        </div>

        <div id="payroll-details" style="display: none;">
            {{-- section 2: earnings --}}
            <div class="card shadow-sm mb-3 border-start border-success border-4">
                <div class="card-header py-3" style="background: #e8f5e9;">
                    <h5 class="mb-0 text-success"><i class="bi bi-wallet2"></i> Earnings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="salary" class="form-label fw-bold">Basic Salary <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" inputmode="numeric" name="salary" id="salary" class="form-control calc-earning format-rupiah" value="{{ old('salary', 0) }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="transport_allowance" class="form-label">Transport Allowance</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" inputmode="numeric" name="transport_allowance" id="transport_allowance" class="form-control calc-earning format-rupiah" value="{{ old('transport_allowance', 0) }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="meal_allowance" class="form-label">Meal Allowance</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" inputmode="numeric" name="meal_allowance" id="meal_allowance" class="form-control calc-earning format-rupiah" value="{{ old('meal_allowance', 0) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="position_allowance" class="form-label">Position Allowance</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" inputmode="numeric" name="position_allowance" id="position_allowance" class="form-control calc-earning format-rupiah" value="{{ old('position_allowance', 0) }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="overtime_hours" class="form-label">Overtime Hours</label>
                                <div class="input-group">
                                    <input type="number" name="overtime_hours" id="overtime_hours" class="form-control" value="{{ old('overtime_hours', 0) }}" min="0" step="0.5">
                                    <span class="input-group-text">Hours</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="overtime_amount" class="form-label">Overtime Pay</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" inputmode="numeric" name="overtime_amount" id="overtime_amount" class="form-control calc-earning format-rupiah" value="{{ old('overtime_amount', 0) }}">
                                </div>
                                <small class="text-muted">Rate: {{ $config['overtime_rate_multiplier'] ?? 1.5 }}× hourly salary</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h6 class="text-success fw-bold"><i class="bi bi-star"></i> Bonus</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="performance_bonus" class="form-label">Performance Bonus</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" inputmode="numeric" name="performance_bonus" id="performance_bonus" class="form-control calc-earning format-rupiah" value="{{ old('performance_bonus', 0) }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="attendance_bonus" class="form-label">Attendance Bonus</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" inputmode="numeric" name="attendance_bonus" id="attendance_bonus" class="form-control calc-earning format-rupiah" value="{{ old('attendance_bonus', 0) }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="other_bonus" class="form-label">Other Bonus</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" inputmode="numeric" name="other_bonus" id="other_bonus" class="form-control calc-earning format-rupiah" value="{{ old('other_bonus', 0) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="bonus_notes" class="form-label">Bonus Notes</label>
                        <input type="text" name="bonus_notes" id="bonus_notes" class="form-control" value="{{ old('bonus_notes') }}" placeholder="Bonus details (optional)">
                    </div>
                    <div class="alert mb-0" style="background: #c8e6c9; border: 1px solid #66bb6a;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-success"><i class="bi bi-calculator"></i> Earnings Subtotal</span>
                            <span class="fs-5 fw-bold text-success" id="display-total-earnings">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- section 3: deductions --}}
            <div class="card shadow-sm mb-3 border-start border-danger border-4">
                <div class="card-header py-3" style="background: #ffebee;">
                    <h5 class="mb-0 text-danger"><i class="bi bi-scissors"></i> Deductions</h5>
                </div>
                <div class="card-body">
                    <h6 class="text-danger fw-bold"><i class="bi bi-clock-history"></i> Attendance</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="working_days" class="form-label">Working Days</label>
                                <input type="number" name="working_days" id="working_days" class="form-control" value="{{ old('working_days', 0) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="days_present" class="form-label">Days Present</label>
                                <input type="number" name="days_present" id="days_present" class="form-control" value="{{ old('days_present', 0) }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="late_count" class="form-label">Late Count</label>
                                <div class="input-group">
                                    <input type="number" name="late_count" id="late_count" class="form-control" value="{{ old('late_count', 0) }}" min="0">
                                    <span class="input-group-text">Times</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="late_deduction" class="form-label">Late Deduction</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" inputmode="numeric" name="late_deduction" id="late_deduction" class="form-control calc-deduction format-rupiah" value="{{ old('late_deduction', 0) }}">
                                </div>
                                <small class="text-muted">1% of Basic Salary / late</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="absent_count" class="form-label">Absent Count</label>
                                <div class="input-group">
                                    <input type="number" name="absent_count" id="absent_count" class="form-control" value="{{ old('absent_count', 0) }}" min="0">
                                    <span class="input-group-text">Days</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="absent_deduction" class="form-label">Absent Deduction</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" inputmode="numeric" name="absent_deduction" id="absent_deduction" class="form-control calc-deduction format-rupiah" value="{{ old('absent_deduction', 0) }}">
                                </div>
                                <small class="text-muted">1% of Basic Salary / absent</small>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6 class="text-danger fw-bold"><i class="bi bi-exclamation-triangle"></i> Fines / Penalties</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="penalty_amount" class="form-label">Penalty Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" inputmode="numeric" name="penalty_amount" id="penalty_amount" class="form-control calc-deduction format-rupiah" value="{{ old('penalty_amount', 0) }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label for="penalty_notes" class="form-label">Penalty Details</label>
                                <input type="text" name="penalty_notes" id="penalty_notes" class="form-control" value="{{ old('penalty_notes') }}" placeholder="Example: SOP violation, inventory damage, etc.">
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6 class="text-danger fw-bold"><i class="bi bi-shield-check"></i> BPJS & Taxes</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="bpjs_kes" class="form-label">Health BPJS</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" inputmode="numeric" name="bpjs_kes" id="bpjs_kes" class="form-control calc-deduction format-rupiah" value="{{ old('bpjs_kes', 0) }}">
                                </div>
                                <small class="text-muted">{{ ($config['bpjs_kes_employee_rate'] ?? 0.01) * 100 }}% of Basic Salary</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="bpjs_tk" class="form-label">Employment BPJS</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" inputmode="numeric" name="bpjs_tk" id="bpjs_tk" class="form-control calc-deduction format-rupiah" value="{{ old('bpjs_tk', 0) }}">
                                </div>
                                <small class="text-muted">{{ ($config['bpjs_tk_employee_rate'] ?? 0.02) * 100 }}% of Basic Salary</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="pph21" class="form-label">Income Tax (PPh 21)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" inputmode="numeric" name="pph21" id="pph21" class="form-control calc-deduction format-rupiah" value="{{ old('pph21', 0) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6 class="text-danger fw-bold"><i class="bi bi-dash-circle"></i> Other Deductions</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="other_deduction" class="form-label">Other Deductions</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" inputmode="numeric" name="other_deduction" id="other_deduction" class="form-control calc-deduction format-rupiah" value="{{ old('other_deduction', 0) }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label for="deduction_notes" class="form-label">Deduction Notes</label>
                                <input type="text" name="deduction_notes" id="deduction_notes" class="form-control" value="{{ old('deduction_notes') }}" placeholder="Other deduction details (optional)">
                            </div>
                        </div>
                    </div>

                    <div class="alert mb-0" style="background: #ffcdd2; border: 1px solid #ef5350;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-danger"><i class="bi bi-calculator"></i> Deductions Subtotal</span>
                            <span class="fs-5 fw-bold text-danger" id="display-total-deductions">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- section 4: summary & submit --}}
            <div class="card shadow-sm mb-3 border-start border-primary border-4">
                <div class="card-header py-3" style="background: #e3f2fd;">
                    <h5 class="mb-0 text-primary"><i class="bi bi-receipt-cutoff"></i> Salary Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <table class="table table-borderless mb-4">
                                <tr>
                                    <td class="fw-bold fs-6">Total Earnings</td>
                                    <td class="text-end fs-6 text-success fw-bold" id="summary-earnings">Rp 0</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold fs-6">Total Deductions</td>
                                    <td class="text-end fs-6 text-danger fw-bold" id="summary-deductions">Rp 0</td>
                                </tr>
                                <tr class="border-top border-2">
                                    <td class="fw-bold fs-4">Net Salary</td>
                                    <td class="text-end fw-bold fs-4 text-primary" id="summary-net">Rp 0</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-2">
                        <a href="{{ route('payrolls.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Payroll</button>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const empSelect = document.getElementById('employee_id');
    const monthSelect = document.getElementById('period_month');
    const yearSelect = document.getElementById('period_year');
    const btnFetch = document.getElementById('btn-fetch-attendance');
    const payrollDetails = document.getElementById('payroll-details');
    const overtimeHoursEl = document.getElementById('overtime_hours');

    // helper: format number to rupiah string (example: 1000000 -> 1.000.000)
    function formatRibuan(angka) {

        // keep empty value if input is cleared
        if (angka === '' || angka === null || angka === undefined) {
            return '';
        }

        // remove all non numeric characters
        let number_string = angka.toString().replace(/[^,\d]/g, '');

        if (number_string === '') {
            return '';
        }

        // remove leading zero
        number_string = parseInt(number_string, 10).toString();

        let split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';

            rupiah += separator + ribuan.join('.');
        }

        return rupiah;
    }

    // helper: convert rupiah string back to raw number
    // example: 1.000.000 -> 1000000
    function parseRupiah(text) {

        if (!text) {
            return 0;
        }

        let parsed = parseFloat(
            text.toString().replace(/\./g, '')
        );

        return isNaN(parsed) ? 0 : parsed;
    }

    // format input on typing
    document.querySelectorAll('.format-rupiah').forEach(el => {

        el.addEventListener('input', function(e) {

            // store current cursor position and text length
            let cursorPosition = this.selectionStart;
            let originalLength = this.value.length;

            // format value with thousand separator
            this.value = formatRibuan(this.value);

            // adjust cursor position after formatting
            let newLength = this.value.length;

            cursorPosition = cursorPosition + (newLength - originalLength);

            // restore cursor position
            this.setSelectionRange(cursorPosition, cursorPosition);

            // recalculate payroll summary
            recalculate();
        });

        // clear input if value is only zero
        el.addEventListener('focus', function(e) {

            if (this.value === '0') {
                this.value = '';
            }
        });

        // restore zero if input is left empty
        el.addEventListener('blur', function(e) {

            if (this.value === '') {
                this.value = '0';

                recalculate();
            }
        });
    });

    // event: remove formatting right before form submission so laravel gets raw numbers
    document.getElementById('payroll-form').addEventListener('submit', function(e) {
        document.querySelectorAll('.format-rupiah').forEach(el => {
            el.value = parseRupiah(el.value);
        });
    });

    // auto check and fetch payroll data
    function checkAndFetchData() {
        const empId = empSelect.value;
        const month = monthSelect.value;
        const year = yearSelect.value;

        if (empId && month && year) {
            payrollDetails.style.display = 'block';
            
            const salary = empSelect.options[empSelect.selectedIndex].dataset.salary || 0;
            // set and format base salary
            document.getElementById('salary').value = formatRibuan(Math.round(parseFloat(salary)));
            
            fetchAttendanceData(empId, month, year);
        } else {
            payrollDetails.style.display = 'none';
        }
    }

    empSelect.addEventListener('change', checkAndFetchData);
    monthSelect.addEventListener('change', checkAndFetchData);
    yearSelect.addEventListener('change', checkAndFetchData);

    // main fetch function
    function fetchAttendanceData(empId, month, year) {
        btnFetch.disabled = true;
        btnFetch.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Auto Calculating...';

        fetch(`{{ route('payrolls.attendance-data') }}?employee_id=${empId}&month=${month}&year=${year}`)
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    const d = res.data;
                    document.getElementById('salary').value = formatRibuan(Math.round(d.base_salary));
                    
                    document.getElementById('working_days').value = d.working_days;
                    document.getElementById('days_present').value = d.days_present;
                    document.getElementById('late_count').value = d.late_count;
                    document.getElementById('late_deduction').value = formatRibuan(d.late_deduction);
                    document.getElementById('absent_count').value = d.absent_count;
                    document.getElementById('absent_deduction').value = formatRibuan(Math.round(d.absent_deduction));
                    
                    document.getElementById('overtime_hours').value = d.overtime_hours || 0;
                    document.getElementById('overtime_amount').value = formatRibuan(d.overtime_amount || 0);

                    const infoEl = document.getElementById('attendance-info');
                    const infoText = document.getElementById('attendance-info-text');
                    infoEl.classList.remove('d-none');
                    infoText.textContent = `Working days: ${d.working_days} | Present: ${d.days_present} | Late: ${d.late_count} | Absent: ${d.absent_count} | Leave: ${d.leave_count}`;

                    recalculate();
                }
            })
            .catch(err => {
                if(typeof Swal !== 'undefined') {
                    Swal.fire('Error', 'Failed to fetch system data.', 'error');
                } else {
                    alert('Failed to fetch system data.');
                }
            })
            .finally(() => {
                btnFetch.disabled = false;
                btnFetch.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Manual Recalculate';
            });
    }

    btnFetch.addEventListener('click', function() {
        if(empSelect.value && monthSelect.value && yearSelect.value) {
            fetchAttendanceData(empSelect.value, monthSelect.value, yearSelect.value);
        }
    });

    // auto calculate overtime amount
    overtimeHoursEl.addEventListener('input', function() {
        const salary = parseRupiah(document.getElementById('salary').value);
        const workingDays = {{ $config['default_working_days'] ?? 22 }};
        const hoursPerDay = {{ $config['working_hours_per_day'] ?? 8 }};
        const multiplier = {{ $config['overtime_rate_multiplier'] ?? 1.5 }};
        const hourlyRate = salary / (workingDays * hoursPerDay);
        const hours = parseFloat(this.value) || 0;

        document.getElementById('overtime_amount').value = formatRibuan(Math.round(hourlyRate * multiplier * hours));

        recalculate();
    });

    // auto calculate late deduction
    document.getElementById('late_count').addEventListener('input', function() {
        const salary = parseRupiah(document.getElementById('salary').value);
        const count = parseFloat(this.value) || 0;
        document.getElementById('late_deduction').value = formatRibuan(Math.round(count * (salary * 0.01)));
        recalculate();
    });

    // auto calculate absent deduction
    document.getElementById('absent_count').addEventListener('input', function() {
        const salary = parseRupiah(document.getElementById('salary').value);
        const count = parseFloat(this.value) || 0;
        document.getElementById('absent_deduction').value = formatRibuan(Math.round(count * (salary * 0.01)));
        recalculate();
    });

    // core recalculate logic
    function recalculate() {
        const v = id => parseRupiah(document.getElementById(id).value);
        const fmt = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');

        const totalEarnings = v('salary') + v('transport_allowance') + v('meal_allowance')
            + v('position_allowance') + v('overtime_amount')
            + v('performance_bonus') + v('attendance_bonus') + v('other_bonus');

        const totalDeductions = v('late_deduction') + v('absent_deduction') + v('penalty_amount')
            + v('bpjs_kes') + v('bpjs_tk') + v('pph21') + v('other_deduction');

        const net = totalEarnings - totalDeductions;

        document.getElementById('display-total-earnings').textContent = fmt(totalEarnings);
        document.getElementById('display-total-deductions').textContent = fmt(totalDeductions);
        document.getElementById('summary-earnings').textContent = fmt(totalEarnings);
        document.getElementById('summary-deductions').textContent = fmt(totalDeductions);
        document.getElementById('summary-net').textContent = fmt(net);
        document.getElementById('summary-net').className = 'text-end fw-bold fs-4 ' + (net >= 0 ? 'text-primary' : 'text-danger');
    }

    if(empSelect.value) {
        checkAndFetchData();
    }
});
</script>
@endpush
@endsection