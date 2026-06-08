@extends ('layouts.dashboard')

@push ('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet" />
    <style>
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
                <a href="{{ route('letters.index') }}" class="btn btn-secondary me-3" title="Back">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>

                <div>
                    <h3 class="mb-0">Edit Letter</h3>
                    <p class="text-subtitle text-muted mb-0 mt-1">Update the letter draft.</p>
                </div>
            </div>

            <nav aria-label="breadcrumb" class="breadcrumb-header order-1 order-md-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('letters.index') }}">Letters</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Letter</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="page-content">
        <div class="container-fluid">
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

                    <div class="alert alert-light-warning mb-4">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Important:</strong> Changing the template will reset all document content below!
                    </div>

                    <form action="{{ route('letters.update', $letter->id) }}" method="POST" id="form-letter">
                        @csrf
                        @method ('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="letter_template_id" class="form-label fw-bold">Use Template</label>
                                <select class="form-select" id="letter_template_id" name="letter_template_id">
                                    <option value="">-- Type Manually (No Template) --</option>
                                    @foreach ($templates as $template)
                                        <option
                                            value="{{ $template->id }}"
                                            {{
                                                old('letter_template_id', $letter->letter_template_id) == $template->id
                                                    ? 'selected'
                                                    : ''
                                            }}
                                            >{{ $template->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="letter_type" class="form-label fw-bold"
                                    >Letter Type <span class="text-danger">*</span></label
                                >
                                <select class="form-select" id="letter_type" name="letter_type" required>
                                    <option
                                        value="official"
                                        {{
                                            old('letter_type', $letter->letter_type) == 'official'
                                                ? 'selected'
                                                : ''
                                        }}
                                        >Official Letter
                                    </option>
                                    <option
                                        value="memo"
                                        {{
                                            old('letter_type', $letter->letter_type) == 'memo'
                                                ? 'selected'
                                                : ''
                                        }}
                                        >Memorandum
                                    </option>
                                    <option
                                        value="notice"
                                        {{
                                            old('letter_type', $letter->letter_type) == 'notice'
                                                ? 'selected'
                                                : ''
                                        }}
                                        >Notice
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label fw-bold"
                                >Letter Subject <span class="text-danger">*</span></label
                            >
                            <input
                                type="text"
                                class="form-control"
                                id="subject"
                                name="subject"
                                value="{{ old('subject', $letter->subject) }}"
                                placeholder="Enter the letter subject/topic..."
                                required
                            />
                        </div>

                        <div id="dynamic-tags-container" class="row mt-4 mb-2"></div>

                        <div class="mb-3 mt-4">
                            <label for="content" class="form-label fw-bold"
                                >Document Content <span class="text-danger">*</span></label
                            >
                            <textarea
                                class="form-control"
                                id="content"
                                name="content"
                                required
                                >{{ old('content', $letter->content) }}</textarea
                            >
                        </div>

                        <div class="form-group mt-4 pt-3 border-top d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Update Draft
                            </button>
                            <a href="{{ route('letters.index') }}" class="btn btn-secondary"
                                ><i class="bi bi-x-circle me-1"></i> Cancel</a
                            >
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push ('scripts')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        // === capture active user data from php to javascript ===
        const currentUserData = @json (\App\Constants\LetterTagConfig::getAutoFillValues(auth()->user()));

        $(document).ready(function () {
            $('#content').summernote({
                placeholder: 'Type the letter content here...',
                tabsize: 2,
                height: 400,
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

            $('#form-letter').on('submit', function (e) {
                let konfirmasi = confirm('Are you sure the letter changes are correct and ready to be saved?');
                if (!konfirmasi) {
                    e.preventDefault();
                }
            });

            $('#letter_template_id').on('change', function () {
                let templateId = $(this).val();
                let container = $('#dynamic-tags-container');
                container.empty();

                if (templateId) {
                    // special alert if employee changes template while in edit mode
                    if (!confirm('Changing the template will reset the existing document text. Continue?')) {
                        $(this).val('{{ $letter->letter_template_id }}'); // return to the original choice
                        return;
                    }

                    $.ajax({
                        url: '{{ url('letter-templates') }}/' + templateId,
                        type: 'GET',
                        dataType: 'json',
                        success: function (data) {
                            $('#content').summernote('code', data.content);
                            if (data.template) {
                                $('#subject').val(data.template.name);
                                $('#letter_type').val(data.template.type);
                            }

                            if (data.tags && data.tags.length > 0) {
                                container.append(
                                    '<div class="col-12"><div class="alert alert-light-primary"><i class="bi bi-info-circle me-2"></i><strong>Complete Letter Variable Data:</strong> The fields below will automatically replace the tags in the document.</div></div>',
                                );

                                data.tags.forEach(function (tag) {
                                    let labelName = tag.tag_name.replace(/_/g, ' ').toUpperCase();
                                    let defaultValue = tag.default_value ? tag.default_value : '';

                                    // === new logic: check auto fill ===
                                    let isReadOnly = '';
                                    let helperText = '';

                                    if (tag.dropdown_type === 'auto_fill') {
                                        isReadOnly = 'readonly style="background-color: var(--bs-secondary-bg);"';
                                        helperText =
                                            '<small class="text-primary mt-1 d-block"><i class="bi bi-magic me-1"></i>Automatically filled by the system</small>';
                                        defaultValue = currentUserData[tag.dropdown_model] || '';
                                    }

                                    let colClass = tag.input_type === 'long_text' ? 'col-12' : 'col-md-6';
                                    let inputElement = '';

                                    if (tag.input_type === 'long_text') {
                                        inputElement = `<textarea name="dynamic_tags[${tag.tag_name}]" class="form-control" rows="4" required ${isReadOnly}>${defaultValue}</textarea>`;
                                    } else if (tag.input_type === 'date') {
                                        inputElement = `<input type="date" name="dynamic_tags[${tag.tag_name}]" class="form-control" value="${defaultValue}" required ${isReadOnly}>`;
                                    } else if (tag.input_type === 'time') {
                                        inputElement = `<input type="time" name="dynamic_tags[${tag.tag_name}]" class="form-control" value="${defaultValue}" required ${isReadOnly}>`;
                                    } else if (tag.input_type === 'number') {
                                        inputElement = `<input type="number" name="dynamic_tags[${tag.tag_name}]" class="form-control" value="${defaultValue}" required ${isReadOnly}>`;
                                    } else if (tag.input_type === 'dropdown') {
                                        let optionsHtml = '<option value="">-- Select Data --</option>';
                                        if (tag.dropdown_data) {
                                            for (let [val, label] of Object.entries(tag.dropdown_data)) {
                                                let isSelected = defaultValue == val ? 'selected' : '';
                                                optionsHtml += `<option value="${val}" ${isSelected}>${label}</option>`;
                                            }
                                        }
                                        inputElement = `<select name="dynamic_tags[${tag.tag_name}]" class="form-select" required>${optionsHtml}</select>`;
                                    } else {
                                        inputElement = `<input type="text" name="dynamic_tags[${tag.tag_name}]" class="form-control" value="${defaultValue}" required ${isReadOnly}>`;
                                    }

                                    let html = `
                                        <div class="${colClass} mb-3">
                                            <label class="form-label fw-bold text-secondary">${labelName}</label>
                                            ${inputElement}
                                            ${helperText}
                                        </div>
                                    `;
                                    container.append(html);
                                });
                            }
                        },
                        error: function () {
                            alert('Failed to load template.');
                        },
                    });
                }
            });
        });
    </script>
@endpush
