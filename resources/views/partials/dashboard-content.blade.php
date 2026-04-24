{{-- ================= STAT CARDS ================= --}}

@if($isGlobal ?? false)
{{-- HR Administrator / Master Admin / Master Admin: Show global stats --}}
<div class="row g-3 mb-4">
    @foreach ([
        ['Departments', $departmentCount ?? 0, 'bi-diagram-3-fill', '#8b5cf6'],
        ['Employees', $employeeCount ?? 0, 'bi-people-fill', '#0ea5e9'],
        ['Presences', $presenceCount ?? 0, 'bi-calendar-check-fill', '#10b981'],
        ['Payrolls', $payrollCount ?? 0, 'bi-cash-stack', '#ef4444']
    ] as [$title, $count, $icon, $color])
    <div class="col-6 col-lg-3">
        <div class="card shadow-sm h-100 border-0" style="border-radius: 20px; overflow: hidden;">
            <div class="card-body p-4 d-flex flex-column text-center" style="align-items: center; justify-content: center;">
                <div style="width: 100%; display: flex; justify-content: center; margin-bottom: 12px;">
                    <div class="fc-icon-box" style="--icon-color: {{ $color }};">
                        <i class="bi {{ $icon }}"></i>
                    </div>
                </div>
                <h6 class="text-muted mb-1 fw-semibold text-uppercase tracking-wider" style="font-size: 0.65rem; letter-spacing: 0.05em;">{{ $title }}</h6>
                <h3 class="fw-bold mb-0 fs-3 color-navy">{{ $count }}</h3>
            </div>
        </div>
    </div>
    @endforeach
</div>

@else
{{-- Manager / Unit Head / Employee / Employee: Show personal stats --}}
<div class="row g-3 mb-4">
    {{-- Welcome Card --}}
    <div class="col-12">
        <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #1b2a4a 0%, #3d4e6c 100%); border-radius: 20px;">
            <div class="card-body px-4 py-4 text-white">
                <div class="d-flex align-items-center gap-4">
                    <div class="position-relative">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=ffffff&color=1b2a4a&size=80"
                             class="rounded-circle shadow-lg" width="72" height="72" style="border: 3px solid rgba(255,255,255,0.2);">
                        <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle" style="width: 18px; height: 18px;"></span>
                    </div>
                    <div>
                        <h4 class="mb-1 fw-bold tracking-tight text-white">Selamat datang, {{ auth()->user()->name ?? 'User' }}!</h4>
                        <p class="mb-0 opacity-75 d-flex align-items-center gap-2" style="font-size: 14px;">
                            <span class="badge bg-white bg-opacity-10 text-white fw-medium px-2 py-1"><i class="bi bi-shield-check me-1"></i> {{ session('role', 'Employee') }}</span>
                            <span class="opacity-50">&bull;</span>
                            <span class="fw-medium"><i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach ([
        ['Kehadiran Saya', $presenceCount ?? 0, 'bi-calendar2-check-fill', '#10b981'],
        ['Tugas Saya', $myTaskCount ?? 0, 'bi-cpu-fill', '#0ea5e9'],
        ['Tugas Pending', $pendingTaskCount ?? 0, 'bi-clock-history', '#f97316'],
        ['Surat Saya', $myLetterCount ?? 0, 'bi-envelope-paper-fill', '#8b5cf6']
    ] as [$title, $count, $icon, $color])
    <div class="col-6 col-lg-3">
        <div class="card shadow-sm h-100 border-0" style="border-radius: 20px;">
            <div class="card-body p-4 d-flex flex-column text-center" style="align-items: center; justify-content: center;">
                <div style="width: 100%; display: flex; justify-content: center; margin-bottom: 12px;">
                    <div class="fc-icon-box" style="--icon-color: {{ $color }};">
                        <i class="bi {{ $icon }}"></i>
                    </div>
                </div>
                <h6 class="text-muted mb-1 fw-semibold text-uppercase tracking-wider" style="font-size: 0.65rem; letter-spacing: 0.05em;">{{ $title }}</h6>
                <h3 class="fw-bold mb-0 fs-3 color-navy">{{ $count }}</h3>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ================= CHARTS ================= --}}
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100 shadow-sm border-0" style="border-radius: 20px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <h6 class="fw-bold mb-0 color-navy opacity-75 text-uppercase" style="font-size: 0.75rem;">{{ ($isGlobal ?? false) ? 'Presence Chart' : 'Kehadiran Saya' }}</h6>
            </div>
            <div class="card-body p-4">
                <div style="height: 300px; min-height: 300px; position: relative; width: 100%; overflow: hidden;">
                    <canvas id="presenceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100 shadow-sm border-0" style="border-radius: 20px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <h6 class="fw-bold mb-0 color-navy opacity-75 text-uppercase" style="font-size: 0.75rem;">{{ ($isGlobal ?? false) ? 'Payroll Chart' : 'Payroll Saya' }}</h6>
            </div>
            <div class="card-body p-4">
                <div style="height: 300px; min-height: 300px; position: relative; width: 100%; overflow: hidden;">
                    <canvas id="payrollChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================= EMPLOYEE STATUS CHART (GLOBAL ONLY) ================= --}}
@if($isGlobal ?? false)
<div class="row g-4 mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0" style="border-radius: 20px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <h6 class="fw-bold mb-0 color-navy opacity-75 text-uppercase" style="font-size: 0.75rem;">Distribusi Status Karyawan</h6>
            </div>
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div style="height: 300px; position: relative;">
                            <canvas id="employeeStatusChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless">
                                <thead>
                                    <tr>
                                        <th style="font-size: 0.7rem; color: #7486a4;">STATUS</th>
                                        <th class="text-end" style="font-size: 0.7rem; color: #7486a4;">JUMLAH</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statusLabels as $index => $label)
                                    <tr>
                                        <td class="py-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="rounded-circle" style="width: 8px; height: 8px; background-color: {{ ['#38bdf8', '#10b981', '#fb923c', '#8b5cf6', '#a78bfa'][$index % 5] }}"></span>
                                                <span class="fw-medium text-dark" style="font-size: 0.85rem;">{{ $label }}</span>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold py-2" style="font-size: 0.85rem;">{{ $statusData[$index] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ================= STYLE ================= --}}
<style>
.color-navy { color: #1b2a4a; }

.fc-icon-box {
    width: 64px;
    height: 64px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    color: var(--icon-color);
    background: rgba(var(--bs-body-bg-rgb), 1);
    position: relative;
    margin-left: auto;
    margin-right: auto;
    box-shadow: 
        0 10px 20px -10px var(--icon-color),
        0 0 0 1px rgba(0,0,0,0.05),
        inset 0 -4px 8px rgba(0,0,0,0.02);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.fc-icon-box::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 18px;
    background: var(--icon-color);
    opacity: 0.08;
    transition: opacity 0.3s;
}

.card:hover .fc-icon-box {
    transform: translateY(-5px);
    box-shadow: 
        0 15px 30px -12px var(--icon-color),
        0 0 0 1px rgba(0,0,0,0.05);
}

.card:hover .fc-icon-box::before {
    opacity: 0.15;
}

[data-bs-theme='dark'] .fc-icon-box {
    background: #1e1e2d;
    box-shadow: 
        0 10px 25px -10px var(--icon-color),
        0 0 0 1px rgba(255,255,255,0.05);
}

.tracking-wider { letter-spacing: 0.05em; }
.tracking-tight { letter-spacing: -0.025em; }

.fc-icon-box i {
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    width: 100%;
    height: 100%;
}
</style>

{{-- ================= SCRIPT ================= --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
    if (typeof Chart !== 'undefined') {
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            resizeDelay: 200,
            layout: { padding: { top: 10, bottom: 10 } }
        };

        const presenceCtx = document.getElementById('presenceChart');
        if (presenceCtx) {
            new Chart(presenceCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($presenceLabels ?? []) !!},
                    datasets: [{
                        label: '{{ ($isGlobal ?? false) ? "Presences" : "Kehadiran" }}',
                        data: {!! json_encode($presenceData ?? []) !!},
                        borderWidth: 2,
                        tension: .4,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true
                    }]
                },
                options: commonOptions
            });
        }

        const payrollCtx = document.getElementById('payrollChart');
        if (payrollCtx) {
            new Chart(payrollCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($payrollLabels ?? []) !!},
                    datasets: [{
                        label: 'Payroll',
                        data: {!! json_encode($payrollData ?? []) !!},
                        borderWidth: 1,
                        backgroundColor: '#38bdf8'
                    }]
                },
                options: commonOptions
            });
        }

        const statusCtx = document.getElementById('employeeStatusChart');
        if (statusCtx) {
            new Chart(statusCtx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($statusLabels ?? []) !!},
                    datasets: [{
                        data: {!! json_encode($statusData ?? []) !!},
                        backgroundColor: ['#38bdf8', '#10b981', '#fb923c', '#8b5cf6', '#a78bfa']
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }
    }
});
</script>
