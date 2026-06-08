@extends ('layouts.dashboard')

@section ('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Positions</h3>
                    <p class="text-subtitle text-muted">Manage position names, levels, and employee salary grades.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Position</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible shadow-sm border-0 rounded-3 show fade">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible shadow-sm border-0 rounded-3 show fade">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header border-bottom mb-3 text-start d-flex flex-wrap gap-2">
            <a href="{{ route('positions.create') }}" class="btn btn-primary px-3 fw-semibold shadow-sm rounded-3">
                <i class="bi bi-plus-lg me-1"></i> Add Position
            </a>
        </div>
        <div class="card-body px-4 pb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="table-positions" style="width: 100%">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Position Name</th>
                            <th>Title</th>
                            <th>Level</th>
                            <th>Salary Grade</th>
                            <th>Description</th>
                            <th width="12%" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($positions as $i => $position)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="fw-bold text-primary">{{ $position->position_name }}</td>
                                <td>{{ $position->title ?? '-' }}</td>
                                <td>
                                    @if ($position->level)
                                        <span
                                            class="badge bg-info bg-opacity-10 text-info rounded-3 px-2 py-1"
                                            >{{ $position->level }}</span
                                        >
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($position->salary_grade)
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success rounded-3 px-2 py-1"
                                            >{{ $position->salary_grade }}</span
                                        >
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-muted text-truncate" style="max-width: 200px">
                                    {{ $position->description ?? '-' }}
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a
                                            href="{{ route('positions.edit', $position->position_id) }}"
                                            class="btn btn-outline-warning btn-sm rounded-3"
                                            title="Edit"
                                        >
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form
                                            action="{{ route('positions.destroy', $position->position_id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this position?');"
                                        >
                                            @csrf
                                            @method ('DELETE')
                                            <button
                                                type="submit"
                                                class="btn btn-outline-danger btn-sm rounded-3"
                                                title="Delete"
                                            >
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push ('scripts')
    <script>
        $(document).ready(function () {
            $('#table-positions').DataTable({
                responsive: true,
                dom:
                    "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            });
        });
    </script>
@endpush
