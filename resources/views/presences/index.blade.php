@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Presences</h3>
                <p class="text-subtitle text-muted">Monitor presences data.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">Dashboard</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('presences.index') }}">Presences</a>
        </li>
    </ol>
</nav>

            </div>
        </div>
    </div>
    
    <section class="section">
        <div class="card">
            
            <div class="card-body">

<style>
    @media (max-width: 768px) {
        .presence-actions {
            display: grid !important;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
        }
        .presence-actions .btn {
            font-size: 0.8rem;
            padding: 0.45rem 0.5rem;
            text-align: center;
            width: 100%;
        }
    }
</style>

                <div class="d-flex flex-wrap gap-2 mb-3 presence-actions">
                    <a href="{{ route('presences.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle"></i> New Presence</a>
                    <a href="{{ route('presences.calendar') }}" class="btn btn-info"><i class="bi bi-calendar3"></i> Calendar View</a>
                    <a href="{{ route('presences.statistics') }}" class="btn btn-secondary"><i class="bi bi-bar-chart"></i> Statistics</a>
                    @if(\App\Constants\Roles::isAdmin(session('role')))
                        <a href="{{ route('presences.export') }}" class="btn btn-success"><i class="bi bi-download"></i> Export CSV</a>
                    @endif
                    @if(in_array(session('role'), ['HR Administrator', \App\Constants\Roles::MASTER_ADMIN]))
                        <a href="{{ route('master-presences.index') }}" class="btn btn-warning">
                            <i class="bi bi-gear-fill"></i> Master Presence
                        </a>
                    @endif
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#holidayModal">
                        <i class="bi bi-calendar-event"></i> Holidays List
                    </button>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show">
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped align-middle nowrap" id="presence-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Date</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Work Type</th>
                                <th>Office Site</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="holidayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white"><i class="bi bi-calendar-heart"></i> Hari Libur Nasional & Cuti Bersama</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <select id="holiday-month" class="form-select">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <select id="holiday-year" class="form-select">
                            @foreach(range(date('Y') - 1, date('Y') + 2) as $y)
                                <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div id="holiday-loader" class="text-center d-none my-3">
                    <div class="spinner-border text-danger" role="status"></div>
                    <p class="mt-2 text-muted">Mengambil data libur...</p>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="holiday-table">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="holiday-tbody">
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        $('#presence-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('presences.index') }}",
            order: [[1, 'desc']],
            columns: [
                { data: 'employee.fullname', name: 'employee.fullname', defaultContent: '<em>Unknown</em>' },
                { data: 'date', name: 'date' },
                { data: 'check_in', name: 'check_in' },
                { data: 'check_out', name: 'check_out' },
                { data: 'work_type_badge', name: 'work_type', orderable: false, searchable: false },
                { data: 'office_location_name', name: 'office_location_name', orderable: false, searchable: false, defaultContent: '-' },
                { data: 'status_badge', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ]
        });

        // delete confirmation standard
        $(document).on('submit', '.delete-form', function (e) {
            e.preventDefault();
            window.confirmDelete(this, 'Delete this presence record?');
        });

        function loadHolidays() {
            let year = $('#holiday-year').val();
            let month = $('#holiday-month').val();
            let tbody = $('#holiday-tbody');
            let loader = $('#holiday-loader');
            let table = $('#holiday-table');

            tbody.empty();
            table.hide();
            loader.removeClass('d-none');

            fetch(`https://libur.deno.dev/api?year=${year}&month=${month}`)
                .then(response => response.json())
                .then(data => {
                    loader.addClass('d-none');
                    table.show();

                    if (data.length === 0) {
                        tbody.append('<tr><td colspan="3" class="text-center text-muted">Tidak ada hari libur di bulan ini.</td></tr>');
                        return;
                    }

                    data.forEach(item => {
                        // Format tanggal jadi lebih cantik (misal: 01 May 2026)
                        let dateObj = new Date(item.date);
                        let formattedDate = dateObj.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                        
                        let badge = item.is_national_holiday 
                            ? '<span class="badge bg-danger">Libur Nasional</span>' 
                            : '<span class="badge bg-warning text-dark">Cuti Bersama</span>';

                        tbody.append(`
                            <tr>
                                <td class="text-nowrap fw-bold">${formattedDate}</td>
                                <td>${item.name}</td>
                                <td>${badge}</td>
                            </tr>
                        `);
                    });
                })
                .catch(error => {
                    loader.addClass('d-none');
                    tbody.append('<tr><td colspan="3" class="text-center text-danger">Gagal memuat data. Silakan coba lagi.</td></tr>');
                });
        }

        // Load pertama kali pas modal dibuka
        $('#holidayModal').on('show.bs.modal', function () {
            loadHolidays();
        });

        // Reload otomatis kalau bulan atau tahun diubah
        $('#holiday-month, #holiday-year').on('change', function() {
            loadHolidays();
        });
    });
</script>
@endpush
@endsection