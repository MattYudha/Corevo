@extends ('layouts.dashboard')

@section ('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Holidays Management</h3>
                    <p class="text-subtitle text-muted">Manage national holidays and company collective leave.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Holidays Management</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm rounded-3 border-0 mb-4 position-sticky" style="top: 2rem">
                    <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-plus-circle-dotted text-primary me-2"></i> Add Holiday
                        </h5>
                    </div>
                    <div class="card-body p-4 pt-3">
                        @if ($errors->any())
                            <div class="alert alert-danger py-2 small">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('holidays.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Holiday Date <span class="text-danger">*</span></label
                                >
                                <input
                                    type="date"
                                    name="date"
                                    class="form-control bg-body text-body"
                                    value="{{ old('date') }}"
                                    required
                                />
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Holiday/Event Name <span class="text-danger">*</span></label
                                >
                                <input
                                    type="text"
                                    name="name"
                                    class="form-control bg-body text-body"
                                    value="{{ old('name') }}"
                                    placeholder="E.g., Independence Day"
                                    required
                                />
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold"
                                    >Holiday Type <span class="text-danger">*</span></label
                                >
                                <select name="type" class="form-select bg-body text-body" required>
                                    <option value="national" {{ old('type') == 'national' ? 'selected' : '' }}
                                        >National Holiday
                                    </option>
                                    <option value="collective" {{ old('type') == 'collective' ? 'selected' : '' }}
                                        >Collective Leave
                                    </option>
                                </select>
                            </div>

                            <div class="alert alert-light-danger py-2 small border-danger border-start border-3">
                                <i class="bi bi-shield-lock-fill text-danger me-1"></i>
                                Employees will be blocked from the attendance system on this date. Admins can still add
                                manual attendance if necessary.
                            </div>

                            <button type="submit" class="btn btn-primary w-100 fw-bold rounded-3 shadow-sm mt-2">
                                <i class="bi bi-save me-1"></i> Save Holiday Data
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm rounded-3 border-0">
                    <div class="card-header bg-transparent border-bottom-0 pt-4 pb-2">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-card-checklist text-primary me-2"></i> Holiday List
                        </h5>
                    </div>
                    <div class="card-body p-4 pt-3">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible show fade py-2 small shadow-sm">
                                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="alert"
                                    aria-label="Close"
                                    style="padding: 0.8rem"
                                ></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle w-100" id="holidays-table">
                                <thead>
                                    <tr class="border-bottom border-2">
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($holidays as $holiday)
                                        <tr>
                                            <td class="fw-semibold text-nowrap">
                                                {{
                                                    \Carbon\Carbon::parse($holiday->date)->translatedFormat(
                                                        'd M Y',
                                                    )
                                                }}
                                            </td>
                                            <td>{{ $holiday->name }}</td>
                                            <td>
                                                @if ($holiday->is_national_holiday)
                                                    <span
                                                        class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-2"
                                                    >
                                                        National Holiday
                                                    </span>
                                                @else
                                                    <span
                                                        class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-2"
                                                    >
                                                        Collective Leave
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <form
                                                    action="{{ route('holidays.destroy', $holiday->id) }}"
                                                    method="POST"
                                                    class="d-inline delete-form"
                                                >
                                                    @csrf
                                                    @method ('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-danger border-0 rounded-circle"
                                                        data-bs-toggle="tooltip"
                                                        title="Delete Data"
                                                    >
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <style>
        /* styling datatables to blend with dark mode */
        [data-bs-theme='dark'] table.dataTable td,
        [data-bs-theme='dark'] table.dataTable th {
            border-color: #323246 !important;
        }
    </style>
@endsection

@push ('scripts')
    <script>
        $(document).ready(function () {
            $('#holidays-table').DataTable({
                responsive: true,
                order: [[0, 'desc']], // sort from the latest date
            });

            $(document).on('submit', '.delete-form', function (e) {
                e.preventDefault();
                window.confirmDelete(this, 'Are you sure you want to delete this holiday?');
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush
