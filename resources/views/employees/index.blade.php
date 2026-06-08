@extends ('layouts.dashboard')

@section ('content')
    <div class="page-heading">
        <div class="page-title mb-4">
            <div class="row">
                <div class="col-md-6">
                    <h3>Employees</h3>
                    <p class="text-subtitle text-muted">Manage employees data</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <nav aria-label="breadcrumb" class="breadcrumb-header">
                        <ol class="breadcrumb justify-content-md-end">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Employees</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Employee List</h5>

                                @can ('create', App\Models\Employee::class)
                                    <a href="{{ route('employees.create') }}" class="btn btn-primary">
                                        <i class="bi bi-plus-lg"></i> New Employee
                                    </a>
                                @endcan
                            </div>

                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show">
                                    {{ session('success') }}
                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="alert"
                                        aria-label="Close"
                                    ></button>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-striped align-middle w-100" id="employee-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>NIK</th>
                                            <th>Fullname</th>
                                            <th>Status Karyawan</th>
                                            <th>NPWP</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Department</th>
                                            <th>Office Location</th>
                                            <th>Status</th>
                                            <th>Salary</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @push ('scripts')
        <script>
            $(function () {
                $('#employee-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('employees.index') }}',
                    order: [[1, 'asc']],
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'nik', name: 'nik' },
                        { data: 'fullname', name: 'fullname' },
                        { data: 'employee_status_badge', name: 'employee_status' },
                        { data: 'npwp', name: 'npwp' },
                        { data: 'email', name: 'email' },
                        { data: 'role.title', name: 'role.title', defaultContent: '-' },
                        { data: 'department.name', name: 'department.name', defaultContent: '-' },
                        { data: 'office_location_name', name: 'officeLocation.name', defaultContent: '-' },
                        { data: 'status_badge', name: 'status', orderable: false, searchable: false },
                        { data: 'salary', name: 'salary', orderable: false, searchable: false },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
                    ],
                });

                // delete confirmation standard
                $(document).on('submit', '.delete-form', function (e) {
                    e.preventDefault();
                    window.confirmDelete(this, 'Hapus data karyawan ini?');
                });
            });
        </script>
    @endpush

@endsection
