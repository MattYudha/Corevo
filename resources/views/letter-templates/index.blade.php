@extends ('layouts.dashboard')

@section ('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Letter Templates</h3>
                    <p class="text-subtitle text-muted">Manage company letter formats and templates.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Letter Templates</li>
                        </ol>
                    </nav>
                </div>
            </div>
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
                <div class="card-header border-bottom mb-3 text-start">
                    <a href="{{ route('letter-templates.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Create Template
                    </a>
                </div>

                <div class="card-body">
                    <div class="row">
                        @forelse ($templates as $template)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 border shadow-sm">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">{{ $template->name }}</h5>
                                        <p class="card-text text-muted mb-3" style="font-size: 0.9rem">
                                            {{ $template->description ?? 'No description available.' }}
                                        </p>

                                        <span class="badge bg-primary mb-4 align-self-start">
                                            {{ ucfirst($template->type) }}
                                        </span>

                                        {{-- action buttons --}}
                                        <div class="d-flex gap-2 flex-wrap mt-auto pt-3 border-top">
                                            <a
                                                href="{{ route('letter-templates.show', $template) }}"
                                                class="btn btn-sm btn-outline-info"
                                                title="View Content"
                                            >
                                                <i class="bi bi-eye"></i> Preview
                                            </a>

                                            <a
                                                href="{{ route('letter-templates.edit', $template) }}"
                                                class="btn btn-sm btn-outline-warning"
                                                title="Edit Template"
                                            >
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route('letter-templates.destroy', $template) }}"
                                                class="d-inline ms-auto"
                                            >
                                                @csrf
                                                @method ('DELETE')
                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-danger delete-confirm"
                                                    title="Delete Template"
                                                >
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info text-center py-4">
                                    <i class="bi bi-info-circle fs-4 d-block mb-2"></i>
                                    No letter templates have been created yet.<br />
                                    <a
                                        href="{{ route('letter-templates.create') }}"
                                        class="alert-link text-decoration-underline"
                                        >Create your first template</a
                                    >.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </div>
    @push ('scripts')
        <script>
            $(function () {
                $('.delete-confirm').on('click', function (e) {
                    e.preventDefault();
                    const form = $(this).closest('form');
                    window.confirmDelete(form[0], 'Permanently delete this letter template?');
                });
            });
        </script>
    @endpush
@endsection
