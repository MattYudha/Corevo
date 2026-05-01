@extends('layouts.dashboard')

@section('content')



<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><i class="bi bi-plus-circle"></i> Buat Payroll Baru</h3>
                <p class="text-subtitle text-muted">Hitung gaji karyawan dengan detail lengkap.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('payrolls.index') }}">Payroll</a></li>
                        <li class="breadcrumb-item active">Buat Baru</li>
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

        {{-- SECTION 1: Info Karyawan & Periode --}}
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light py-3">
                <h5 class="mb-0"><i class="bi bi-person-badge"></i> Informasi Karyawan & Periode</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="employee_id" class="form-label fw-bold">Karyawan <span class="text-danger">*</span></label>
                            <select name="employee_id" id="employee_id" class="form-select" required>
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" data-salary="{{ $emp->salary }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->fullname }} {{ $emp->emp_code ? '('.$emp->emp_code.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="period_month" class="form-label fw-bold">Bulan <span class="text-danger">*</span></label>
                            <select name="period_month" id="period_month" class="form-select" required>
                                @php $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
                                @foreach($months as $i => $m)
                                    <option value="{{ $i+1 }}" {{ old('period_month', date('n')) == ($i+1) ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-3">
                            <label for="period_year" class="form-label fw-bold">Tahun <span class="text-danger">*</span></label>
                            <select name="period_year" id="period_year" class="form-select" required>
                                @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                                    <option value="{{ $y }}" {{ old('period_year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">&nbsp;</label>
                            <button type="button" id="btn-fetch-attendance" class="btn btn-outline-primary d-block w-100" disabled>
                                <i class="bi bi-cloud-download"></i> Hitung Kehadiran
                            </button>
                        </div>
                    </div>
                </div>
                <div id="attendance-info" class="alert alert-info d-none">
                    <i class="bi bi-info-circle"></i> <span id="attendance-info-text"></span>
                </div>
            </div>
        </div>

        {{-- SECTION 2: Pendapatan --}}
        <div class="card shadow-sm mb-3 border-start border-success border-4">
            <div class="card-header py-3" style="background: #e8f5e9;">
                <h5 class="mb-0 text-success"><i class="bi bi-wallet2"></i> Pendapatan (Earnings)</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="salary" class="form-label fw-bold">Gaji Pokok <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="salary" id="salary" class="form-control calc-earning" value="{{ old('salary', 0) }}" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="transport_allowance" class="form-label">Tunjangan Transport</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="transport_allowance" id="transport_allowance" class="form-control calc-earning" value="{{ old('transport_allowance', 0) }}" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="meal_allowance" class="form-label">Tunjangan Makan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="meal_allowance" id="meal_allowance" class="form-control calc-earning" value="{{ old('meal_allowance', 0) }}" min="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="position_allowance" class="form-label">Tunjangan Jabatan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="position_allowance" id="position_allowance" class="form-control calc-earning" value="{{ old('position_allowance', 0) }}" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="overtime_hours" class="form-label">Jam Lembur</label>
                            <div class="input-group">
                                <input type="number" name="overtime_hours" id="overtime_hours" class="form-control" value="{{ old('overtime_hours', 0) }}" min="0" step="0.5">
                                <span class="input-group-text">jam</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="overtime_amount" class="form-label">Uang Lembur</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="overtime_amount" id="overtime_amount" class="form-control calc-earning" value="{{ old('overtime_amount', 0) }}" min="0">
                            </div>
                            <small class="text-muted">Rate: {{ $config['overtime_rate_multiplier'] ?? 1.5 }}× gaji/jam</small>
                        </div>
                    </div>
                </div>
                <hr>
                <h6 class="text-success fw-bold"><i class="bi bi-star"></i> Bonus</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="performance_bonus" class="form-label">Bonus Kinerja</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="performance_bonus" id="performance_bonus" class="form-control calc-earning" value="{{ old('performance_bonus', 0) }}" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="attendance_bonus" class="form-label">Bonus Kehadiran</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="attendance_bonus" id="attendance_bonus" class="form-control calc-earning" value="{{ old('attendance_bonus', 0) }}" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="other_bonus" class="form-label">Bonus Lainnya</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="other_bonus" id="other_bonus" class="form-control calc-earning" value="{{ old('other_bonus', 0) }}" min="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="bonus_notes" class="form-label">Catatan Bonus</label>
                    <input type="text" name="bonus_notes" id="bonus_notes" class="form-control" value="{{ old('bonus_notes') }}" placeholder="Keterangan bonus (opsional)">
                </div>
                <div class="alert mb-0" style="background: #c8e6c9; border: 1px solid #66bb6a;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-success"><i class="bi bi-calculator"></i> Subtotal Pendapatan</span>
                        <span class="fs-5 fw-bold text-success" id="display-total-earnings">Rp 0</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 3: Potongan --}}
        <div class="card shadow-sm mb-3 border-start border-danger border-4">
            <div class="card-header py-3" style="background: #ffebee;">
                <h5 class="mb-0 text-danger"><i class="bi bi-scissors"></i> Potongan (Deductions)</h5>
            </div>
            <div class="card-body">
                <h6 class="text-danger fw-bold"><i class="bi bi-clock-history"></i> Kehadiran</h6>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="working_days" class="form-label">Hari Kerja</label>
                            <input type="number" name="working_days" id="working_days" class="form-control" value="{{ old('working_days', 0) }}" min="0">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="days_present" class="form-label">Hari Hadir</label>
                            <input type="number" name="days_present" id="days_present" class="form-control" value="{{ old('days_present', 0) }}" min="0">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="late_count" class="form-label">Jumlah Telat</label>
                            <div class="input-group">
                                <input type="number" name="late_count" id="late_count" class="form-control" value="{{ old('late_count', 0) }}" min="0">
                                <span class="input-group-text">kali</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="late_deduction" class="form-label">Potongan Telat</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="late_deduction" id="late_deduction" class="form-control calc-deduction" value="{{ old('late_deduction', 0) }}" min="0">
                            </div>
                            <small class="text-muted">Rp {{ number_format($config['late_penalty_per_incident'] ?? 50000, 0, ',', '.') }}/telat</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="absent_count" class="form-label">Jumlah Absen</label>
                            <div class="input-group">
                                <input type="number" name="absent_count" id="absent_count" class="form-control" value="{{ old('absent_count', 0) }}" min="0">
                                <span class="input-group-text">hari</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label for="absent_deduction" class="form-label">Potongan Absen</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="absent_deduction" id="absent_deduction" class="form-control calc-deduction" value="{{ old('absent_deduction', 0) }}" min="0">
                            </div>
                            <small class="text-muted">{{ $config['absent_penalty_multiplier'] ?? 1 }}× gaji harian</small>
                        </div>
                    </div>
                </div>

                <hr>
                <h6 class="text-danger fw-bold"><i class="bi bi-exclamation-triangle"></i> Denda / Penalti</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="penalty_amount" class="form-label">Jumlah Denda</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="penalty_amount" id="penalty_amount" class="form-control calc-deduction" value="{{ old('penalty_amount', 0) }}" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label for="penalty_notes" class="form-label">Rincian Denda</label>
                            <input type="text" name="penalty_notes" id="penalty_notes" class="form-control" value="{{ old('penalty_notes') }}" placeholder="Contoh: Pelanggaran SOP, kerusakan inventaris, dll">
                        </div>
                    </div>
                </div>

                <hr>
                <h6 class="text-danger fw-bold"><i class="bi bi-shield-check"></i> BPJS & Pajak</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="bpjs_kes" class="form-label">BPJS Kesehatan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="bpjs_kes" id="bpjs_kes" class="form-control calc-deduction" value="{{ old('bpjs_kes', 0) }}" min="0">
                            </div>
                            <small class="text-muted">{{ ($config['bpjs_kes_employee_rate'] ?? 0.01) * 100 }}% dari gaji pokok</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="bpjs_tk" class="form-label">BPJS Ketenagakerjaan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="bpjs_tk" id="bpjs_tk" class="form-control calc-deduction" value="{{ old('bpjs_tk', 0) }}" min="0">
                            </div>
                            <small class="text-muted">{{ ($config['bpjs_tk_employee_rate'] ?? 0.02) * 100 }}% dari gaji pokok</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="pph21" class="form-label">PPh 21</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="pph21" id="pph21" class="form-control calc-deduction" value="{{ old('pph21', 0) }}" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <h6 class="text-danger fw-bold"><i class="bi bi-dash-circle"></i> Potongan Lain</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="other_deduction" class="form-label">Potongan Lainnya</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="other_deduction" id="other_deduction" class="form-control calc-deduction" value="{{ old('other_deduction', 0) }}" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label for="deduction_notes" class="form-label">Catatan Potongan</label>
                            <input type="text" name="deduction_notes" id="deduction_notes" class="form-control" value="{{ old('deduction_notes') }}" placeholder="Keterangan potongan lain (opsional)">
                        </div>
                    </div>
                </div>

                <div class="alert mb-0" style="background: #ffcdd2; border: 1px solid #ef5350;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-danger"><i class="bi bi-calculator"></i> Subtotal Potongan</span>
                        <span class="fs-5 fw-bold text-danger" id="display-total-deductions">Rp 0</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 4: Ringkasan & Submit --}}
        <div class="card shadow-sm mb-3 border-start border-primary border-4">
            <div class="card-header py-3" style="background: #e3f2fd;">
                <h5 class="mb-0 text-primary"><i class="bi bi-receipt-cutoff"></i> Ringkasan Gaji</h5>
            </div>
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <table class="table table-borderless mb-4">
                            <tr>
                                <td class="fw-bold fs-6">Total Pendapatan</td>
                                <td class="text-end fs-6 text-success fw-bold" id="summary-earnings">Rp 0</td>
                            </tr>
                            <tr>
                                <td class="fw-bold fs-6">Total Potongan</td>
                                <td class="text-end fs-6 text-danger fw-bold" id="summary-deductions">Rp 0</td>
                            </tr>
                            <tr class="border-top border-2">
                                <td class="fw-bold fs-4">GAJI BERSIH</td>
                                <td class="text-end fw-bold fs-4 text-primary" id="summary-net">Rp 0</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>📝 Draft</option>
                                <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                                <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>💰 Paid</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="pay_date" class="form-label fw-bold">Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" name="pay_date" id="pay_date" class="form-control" value="{{ old('pay_date', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="notes" class="form-label">Catatan</label>
                            <input type="text" name="notes" id="notes" class="form-control" value="{{ old('notes') }}" placeholder="Catatan umum (opsional)">
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-2">
                    <a href="{{ route('payrolls.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Payroll</button>
                </div>
            </div>
        </div>

    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const empSelect = document.getElementById('employee_id');
    const btnFetch = document.getElementById('btn-fetch-attendance');
    const overtimeHoursEl = document.getElementById('overtime_hours');

    // Enable/disable fetch button
    empSelect.addEventListener('change', function() {
        btnFetch.disabled = !this.value;
        if (this.value) {
            const salary = this.options[this.selectedIndex].dataset.salary || 0;
            document.getElementById('salary').value = Math.round(parseFloat(salary));
            recalculate();
        }
    });

    // Fetch attendance data
    btnFetch.addEventListener('click', function() {
        const empId = empSelect.value;
        const month = document.getElementById('period_month').value;
        const year = document.getElementById('period_year').value;
        if (!empId) return;

        btnFetch.disabled = true;
        btnFetch.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menghitung...';

        fetch(`{{ route('payrolls.attendance-data') }}?employee_id=${empId}&month=${month}&year=${year}`)
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    const d = res.data;
                    document.getElementById('salary').value = Math.round(d.base_salary);
                    document.getElementById('transport_allowance').value = d.transport_allowance;
                    document.getElementById('meal_allowance').value = d.meal_allowance;
                    document.getElementById('working_days').value = d.working_days;
                    document.getElementById('days_present').value = d.days_present;
                    document.getElementById('late_count').value = d.late_count;
                    document.getElementById('late_deduction').value = d.late_deduction;
                    document.getElementById('absent_count').value = d.absent_count;
                    document.getElementById('absent_deduction').value = Math.round(d.absent_deduction);
                    document.getElementById('bpjs_kes').value = d.bpjs_kes;
                    document.getElementById('bpjs_tk').value = d.bpjs_tk;
                    // document.getElementById('overtime_hours').value = data.overtime_hours || 0;
                    // document.getElementById('overtime_amount').value = data.overtime_amount || 0;

                    const infoEl = document.getElementById('attendance-info');
                    const infoText = document.getElementById('attendance-info-text');
                    infoEl.classList.remove('d-none');
                    infoText.textContent = `Hari kerja: ${d.working_days} | Hadir: ${d.days_present} | Telat: ${d.late_count} | Absen: ${d.absent_count} | Cuti: ${d.leave_count}`;

                    recalculate();
                }
            })
            .catch(err => Swal.fire('Error', 'Gagal mengambil data kehadiran.', 'error'))
            .finally(() => {
                btnFetch.disabled = false;
                btnFetch.innerHTML = '<i class="bi bi-cloud-download"></i> Hitung Kehadiran';
            });
    });

    // Auto-calculate overtime amount
    overtimeHoursEl.addEventListener('input', function() {
        const salary = parseFloat(document.getElementById('salary').value) || 0;
        const workingDays = {{ $config['default_working_days'] ?? 22 }};
        const hoursPerDay = {{ $config['working_hours_per_day'] ?? 8 }};
        const multiplier = {{ $config['overtime_rate_multiplier'] ?? 1.5 }};
        const hourlyRate = salary / (workingDays * hoursPerDay);
        const hours = parseFloat(this.value) || 0;
        document.getElementById('overtime_amount').value = Math.round(hourlyRate * multiplier * hours);
        recalculate();
    });

    // Listen to all input changes
    document.querySelectorAll('.calc-earning, .calc-deduction').forEach(el => {
        el.addEventListener('input', recalculate);
    });

    function recalculate() {
        const v = id => parseFloat(document.getElementById(id).value) || 0;
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

    // Initial calculation
    recalculate();
});
</script>
@endpush
@endsection
