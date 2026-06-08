@extends ('layouts.dashboard')

@section ('content')
    <div class="page-heading mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="d-flex align-items-center order-2 order-md-1 mt-3 mt-md-0">
                <a href="{{ route('positions.index') }}" class="btn btn-secondary me-3" title="Back">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>

                <div>
                    <h3 class="mb-0">Edit Position</h3>
                    <p class="text-subtitle text-muted mb-0 mt-1">Update position details, level, and job description.</p>
                </div>
            </div>

            <nav aria-label="breadcrumb" class="breadcrumb-header order-1 order-md-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('positions.index') }}">Positions</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Position</li>
                </ol>
            </nav>
        </div>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-0 rounded-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('positions.update', $position->position_id) }}" method="POST">
        @csrf
        @method ('PUT')
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="row row-gap-3">
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label for="position_name" class="form-label fw-semibold text-secondary"
                                >Position Name <span class="text-danger">*</span></label
                            >
                            <input
                                type="text"
                                name="position_name"
                                id="position_name"
                                class="form-control"
                                placeholder="Example: IT Manager"
                                value="{{ old('position_name', $position->position_name) }}"
                                required
                            />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label for="title" class="form-label fw-semibold text-secondary">Job Title</label>
                            <input
                                type="text"
                                name="title"
                                id="title"
                                class="form-control"
                                placeholder="Example: Head of Technology"
                                value="{{ old('title', $position->title) }}"
                            />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label for="level" class="form-label fw-semibold text-secondary">Level</label>
                            <input
                                type="text"
                                name="level"
                                id="level"
                                class="form-control"
                                placeholder="Example: Managerial"
                                value="{{ old('level', $position->level) }}"
                            />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label for="salary_grade" class="form-label fw-semibold text-secondary">Salary Grade</label>
                            <input
                                type="text"
                                name="salary_grade"
                                id="salary_grade"
                                class="form-control"
                                placeholder="Example: Grade IV-A"
                                value="{{ old('salary_grade', $position->salary_grade) }}"
                            />
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group mb-0">
                            <label for="description" class="form-label fw-semibold text-secondary"
                                >Job Description</label
                            >
                            <textarea
                                name="description"
                                id="description"
                                class="form-control"
                                rows="4"
                                placeholder="Describe the roles and responsibilities for this position..."
                                >{{ old('description', $position->description) }}</textarea
                            >
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 px-4 pb-4 text-end">
                <a href="{{ route('positions.index') }}" class="btn btn-light-secondary px-4 fw-semibold me-2"
                    >Cancel</a
                >
                <button type="submit" class="btn btn-primary px-5 fw-semibold shadow-sm">Update Data</button>
            </div>
        </div>
    </form>
@endsection
