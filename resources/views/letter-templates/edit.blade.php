@extends ('layouts.dashboard')

@push ('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet" />
    <style>
        /* 1. fix summernote button size bloated by mazer css */
        .note-editor .note-toolbar .note-btn {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.875rem !important;
            height: auto !important;
            min-height: 30px !important;
            margin: 2px !important;
        }
        .note-editor .note-toolbar {
            padding: 0.5rem !important;
            margin: 0 !important;
        }

        /* 2. dark mode override */
        html[data-bs-theme='dark'] .note-editor.note-frame {
            border: 1px solid #434c5e;
            background-color: #1e1e2d;
        }
        html[data-bs-theme='dark'] .note-editor .note-toolbar {
            background-color: #151521;
            border-bottom: 1px solid #434c5e;
        }
        html[data-bs-theme='dark'] .note-editor .note-statusbar {
            background-color: #151521;
            border-top: 1px solid #434c5e;
        }
        html[data-bs-theme='dark'] .note-editing-area .note-editable {
            color: #ced4da;
            background-color: #1e1e2d;
        }
        html[data-bs-theme='dark'] .note-btn {
            color: #ced4da;
            background: transparent;
            border-color: transparent;
        }
        html[data-bs-theme='dark'] .note-btn:hover,
        html[data-bs-theme='dark'] .note-btn.active {
            background-color: #2b2b40;
            color: #ffffff;
        }
        html[data-bs-theme='dark'] .note-dropdown-menu {
            background-color: #1e1e2d;
            border: 1px solid #434c5e;
        }
        html[data-bs-theme='dark'] .note-dropdown-item {
            color: #ced4da;
        }
        html[data-bs-theme='dark'] .note-dropdown-item:hover {
            background-color: #2b2b40;
        }
        html[data-bs-theme='dark'] .note-modal-content {
            background-color: #1e1e2d;
            border: 1px solid #434c5e;
            color: #ced4da;
        }
        html[data-bs-theme='dark'] .note-modal-header {
            border-bottom: 1px solid #434c5e;
        }
        html[data-bs-theme='dark'] .note-modal-title {
            color: #ced4da;
        }
        html[data-bs-theme='dark'] .note-input {
            background-color: #151521;
            border: 1px solid #434c5e;
            color: #ced4da;
        }
    </style>
@endpush

@section ('content')
    <div class="page-heading mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="d-flex align-items-center order-2 order-md-1 mt-3 mt-md-0">
                <a href="{{ route('letter-templates.index') }}" class="btn btn-secondary me-3" title="Kembali">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>

                <div>
                    <h3 class="mb-0">Edit Letter Template</h3>
                    <p class="text-subtitle text-muted mb-0 mt-1">Modify the content or configuration of the letter template.</p>
                </div>
            </div>

            <nav aria-label="breadcrumb" class="breadcrumb-header order-1 order-md-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('letter-templates.index') }}">Letter Templates</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Letter Template</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="page-content">
        <div class="container-fluid">
            <div class="row align-items-start">
                <div class="col-12 col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body pt-4">
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form action="{{ route('letter-templates.update', $letterTemplate) }}" method="POST">
                                @csrf
                                @method ('PUT')

                                <div class="mb-3">
                                    <label for="name" class="form-label fw-bold"
                                        >Template Name <span class="text-danger">*</span></label
                                    >
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="name"
                                        name="name"
                                        value="{{ old('name', $letterTemplate->name) }}"
                                        required
                                    />
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label fw-bold">Description</label>
                                    <textarea
                                        class="form-control"
                                        id="description"
                                        name="description"
                                        rows="2"
                                        >{{ old('description', $letterTemplate->description) }}</textarea
                                    >
                                </div>

                                <div class="mb-3">
                                    <label for="type" class="form-label fw-bold"
                                        >Type <span class="text-danger">*</span></label
                                    >
                                    <select class="form-select" id="type" name="type" required>
                                        <option
                                            value="official"
                                            {{
                                                old('type', $letterTemplate->type) == 'official'
                                                    ? 'selected'
                                                    : ''
                                            }}
                                            >Official Letter
                                        </option>
                                        <option
                                            value="memo"
                                            {{
                                                old('type', $letterTemplate->type) == 'memo'
                                                    ? 'selected'
                                                    : ''
                                            }}
                                            >Memorandum
                                        </option>
                                        <option
                                            value="notice"
                                            {{
                                                old('type', $letterTemplate->type) == 'notice'
                                                    ? 'selected'
                                                    : ''
                                            }}
                                            >Notice
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="content" class="form-label fw-bold"
                                        >Template Content <span class="text-danger">*</span></label
                                    >
                                    <textarea
                                        class="form-control"
                                        id="content"
                                        name="content"
                                        required
                                        >{{ old('content', $letterTemplate->content) }}</textarea
                                    >
                                </div>

                                <div class="form-group mt-4 pt-3 border-top d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-pencil-square me-1"></i> Update Template
                                    </button>
                                    <a href="{{ route('letter-templates.index') }}" class="btn btn-secondary"
                                        ><i class="bi bi-x-circle me-1"></i> Cancel</a
                                    >
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4 mt-4 mt-lg-0 sticky-lg-top" style="top: 2rem; z-index: 10">
                    <div class="card shadow-sm">
                        <div class="card-header border-bottom">
                            <h5 class="card-title mb-0 fs-6">
                                <i class="bi bi-tags-fill me-2 text-primary"></i> List of Tag Variables
                            </h5>
                        </div>
                        <div class="card-body pt-3">
                            <p class="text-muted small">Click a tag below to automatically insert the variable at the cursor position in the editor.</p>

                            <div class="d-flex flex-wrap gap-2">
                                @if (isset($tags) && $tags->count() > 0)
                                    @foreach ($tags as $tag)
                                        <span
                                            class="badge bg-primary px-3 py-2"
                                            style="cursor: pointer; user-select: none; transition: 0.2s"
                                            onmouseover="this.classList.replace('bg-primary', 'bg-dark')"
                                            onmouseout="this.classList.replace('bg-dark', 'bg-primary')"
                                            onclick="window.insertTagToEditor('{{ $tag->tag_name }}')"
                                        >
                                            [{{ $tag->tag_name }}]
                                        </span>
                                    @endforeach
                                @else
                                    <div class="alert alert-light-warning w-100 small">
                                        No tags found in Master Data. <br />
                                        <a href="{{ route('letter-tags.index') }}">Create New Tag</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push ('scripts')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#content').summernote({
                placeholder: 'Type the template content here...',
                tabsize: 2,
                height: 500,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'hr']],
                    ['view', ['fullscreen', 'codeview', 'help']],
                ],
            });
        });

        window.insertTagToEditor = function (tagName) {
            let tagText = `[${tagName}]`;
            $('#content').summernote('editor.insertText', tagText);
        };
    </script>
@endpush
