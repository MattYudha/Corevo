@extends ('layouts.dashboard')

@section ('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Presences</h3>
                    <p class="text-subtitle text-muted">Monitor attendance records.</p>
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
    </div>
    <section class="section">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show shadow-sm">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <div class="card shadow-sm rounded-3 border-0">
            <div class="card-body p-4">
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
                    /* additional tweak to blend datatables border with dark mode */
                    [data-bs-theme='dark'] table.dataTable td,
                    [data-bs-theme='dark'] table.dataTable th {
                        border-color: #323246 !important;
                    }
                </style>

                @if (isset($isHolidayToday) && $isHolidayToday)
                    <div class="alert alert-danger shadow-sm mb-4">
                        <i class="bi bi-calendar-x-fill me-2"></i>
                        <strong>Attendance System Closed:</strong> Today coincides with the
                        <strong>{{ $holidayName }}</strong> holiday.
                    </div>
                @endif

                <div class="d-flex flex-wrap gap-2 mb-4 presence-actions">
                    @if (isset($isHolidayToday) && $isHolidayToday)
                        <button
                            class="btn btn-secondary fw-semibold disabled"
                            style="cursor: not-allowed"
                            title="Locked due to public holiday"
                        >
                            <i class="bi bi-lock-fill me-1"></i> New Presence
                        </button>
                    @else
                        <a href="{{ route('presences.create') }}" class="btn btn-primary fw-semibold">
                            <i class="bi bi-plus-circle me-1"></i> New Presence
                        </a>
                    @endif

                    <a href="{{ route('presences.calendar') }}" class="btn btn-info fw-semibold"
                        ><i class="bi bi-calendar3 me-1"></i> Calendar & Summary</a
                    >

                    @if (\App\Constants\Roles::isAdmin(session('role')))
                        <a href="{{ route('presences.export') }}" class="btn btn-success fw-semibold"
                            ><i class="bi bi-download me-1"></i> Export CSV</a
                        >
                        <a href="{{ route('master-presences.index') }}" class="btn btn-warning fw-semibold text-dark">
                            <i class="bi bi-gear-fill me-1"></i> Master Presence
                        </a>
                        <a href="{{ route('holidays.index') }}" class="btn btn-danger fw-semibold">
                            <i class="bi bi-calendar-event me-1"></i> Manage Holidays
                        </a>
                    @endif
                </div>

                <div class="table-responsive">
                    <table
                        class="table table-striped table-hover align-middle nowrap"
                        id="presence-table"
                        style="width: 100%"
                    >
                        <thead>
                            <tr class="border-bottom border-2">
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
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    @push ('scripts')
        <script>
            $(function () {
                $('#presence-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('presences.index') }}',
                    order: [[1, 'desc']],
                    columns: [
                        { data: 'employee.fullname', name: 'employee.fullname', defaultContent: '<em>Unknown</em>' },
                        { data: 'date', name: 'date' },
                        { data: 'check_in', name: 'check_in' },
                        { data: 'check_out', name: 'check_out' },
                        { data: 'work_type_badge', name: 'work_type', orderable: false, searchable: false },
                        {
                            data: 'office_location_name',
                            name: 'office_location_name',
                            orderable: false,
                            searchable: false,
                            defaultContent: '-',
                        },
                        { data: 'status_badge', name: 'status', orderable: false, searchable: false },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
                    ],
                });

                // standard delete confirmation
                $(document).on('submit', '.delete-form', function (e) {
                    e.preventDefault();
                    window.confirmDelete(this, 'Delete this presence record?');
                });
            });
        </script>
    @endpush
@endsection
