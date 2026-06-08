@extends ('layouts.dashboard')

@section ('content')
    <div class="page-heading mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="d-flex align-items-center order-2 order-md-1 mt-3 mt-md-0">
                <a href="{{ route('letter-templates.index') }}" class="btn btn-secondary me-3" title="Kembali">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>

                <div>
                    <h3 class="mb-0">Template Preview</h3>
                    <p class="text-subtitle text-muted mb-0 mt-1">Detailed content of the letter template.</p>
                </div>
            </div>

            <nav aria-label="breadcrumb" class="breadcrumb-header order-1 order-md-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('letter-templates.index') }}">Letter Templates</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Template Preview</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="page-content">
        <section class="section">
            {{-- success alert --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-1">{{ $letterTemplate->name }}</h4>
                        <span class="badge bg-primary fs-6">{{ ucfirst($letterTemplate->type) }}</span>
                    </div>
                </div>

                <div class="card-body pt-4">
                    @if ($letterTemplate->description)
                        <div class="mb-4">
                            <h6 class="text-muted fw-bold"><i class="bi bi-info-circle me-1"></i> Description:</h6>
                            <p class="mb-0">{{ $letterTemplate->description }}</p>
                        </div>
                    @endif

                    <h6 class="text-muted fw-bold mb-3"><i class="bi bi-file-text me-1"></i> Content Preview:</h6>

                    <div
                        class="border p-4 rounded shadow-sm"
                        style="min-height: 400px; overflow-x: auto; background-color: var(--bs-body-bg)"
                    >
                        {!! $letterTemplate->content !!}
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex gap-2 flex-wrap">
                        <a href="{{ route('letter-templates.edit', $letterTemplate) }}" class="btn btn-warning">
                            <i class="bi bi-pencil-square me-1"></i> Edit Template
                        </a>

                        <form
                            action="{{ route('letter-templates.destroy', $letterTemplate) }}"
                            method="POST"
                            class="d-inline delete-form"
                        >
                            @csrf
                            @method ('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @push ('scripts')
        <script>
            $(document).ready(function () {
                $('.delete-form').on('submit', function (e) {
                    e.preventDefault();
                    window.confirmDelete(this, 'Permanently delete this letter template?');
                });
            });
        </script>
    @endpush
@endsection
