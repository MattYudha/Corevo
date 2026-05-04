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
                        <a href="{{ route('master-presences.settings') }}" class="btn btn-warning d-flex align-items-center">
                            <i class="bi bi-gear-fill me-2 mb-2"></i> Master Presence
                        </a>
                    @endif
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
    });
</script>
@endpush
@endsection