@extends ('layouts.dashboard')

@push ('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet" />
    <style>
        /* summernote ui adjustments for size and spacing */
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

        /* summernote adjustments for mazer dark mode via bs-theme attribute */
        html[data-bs-theme='dark'] .note-editor.note-frame {
            border: 1px solid #434c5e;
            background-color: #1e1e2d;
        }
        html[data-bs-theme='dark'] .note-editor .note-toolbar,
        html[data-bs-theme='dark'] .note-editor .note-statusbar {
            background-color: #151521;
            border-color: #434c5e;
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
        html[data-bs-theme='dark'] .note-dropdown-menu,
        html[data-bs-theme='dark'] .note-modal-content {
            background-color: #1e1e2d;
            border: 1px solid #434c5e;
            color: #ced4da;
        }
        html[data-bs-theme='dark'] .note-dropdown-item {
            color: #ced4da;
        }
        html[data-bs-theme='dark'] .note-dropdown-item:hover {
            background-color: #2b2b40;
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
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center order-2 order-md-1">
                <a href="{{ route('crm.email-blasts.index') }}" class="btn btn-secondary me-3" title="Back">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h3 class="mb-0">Create New Email Blast</h3>
                    <p class="text-subtitle text-muted mb-0 mt-1">Send mass emails with a queuing system to CRM contacts.</p>
                </div>
            </div>

            <nav aria-label="breadcrumb" class="breadcrumb-header order-1 order-md-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('crm.email-blasts.index') }}">Email Blasts</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create New Email Blast</li>
                </ol>
            </nav>
        </div>
    </div>
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-octagon me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <section class="section">
        <div class="card shadow-sm w-100">
            <div class="card-body mt-2">
                <form action="{{ route('crm.email-blasts.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-12 mb-4">
                            <label for="target_type" class="form-label fw-bold"
                                >Target Recipients <span class="text-danger">*</span></label
                            >
                            <select
                                name="target_type"
                                id="target_type"
                                class="form-select @error('target_type') is-invalid @enderror"
                            >
                                <option value="all">All Contacts with Email ({{ $contactCount }} contacts)</option>
                                <option value="selected" {{ request('contact_id') ? 'selected' : '' }}
                                    >Select Specific Contacts / Manual
                                </option>
                            </select>
                            @error ('target_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div
                            class="col-12 mb-4"
                            id="manual_contact_selection"
                            style="{{ request('contact_id') ? '' : 'display:none;' }}"
                        >
                            <label class="form-label fw-bold">Select Target Contacts</label>
                            <div class="border border-secondary-subtle rounded">
                                <div class="p-2 border-bottom border-secondary-subtle bg-body-tertiary rounded-top">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-body text-body border-secondary-subtle">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input
                                            type="text"
                                            id="search-contact"
                                            class="form-control bg-body text-body border-secondary-subtle"
                                            placeholder="Search company name or email..."
                                        />
                                    </div>
                                </div>

                                <div
                                    class="p-3 bg-body rounded-bottom"
                                    style="max-height: 250px; overflow-y: auto"
                                    id="contact-list"
                                >
                                    <div class="form-check mb-3 pb-2 border-bottom border-secondary-subtle">
                                        <input class="form-check-input" type="checkbox" id="select-all-contacts" />
                                        <label class="form-check-label cursor-pointer w-100" for="select-all-contacts">
                                            Select All Search Results
                                        </label>
                                    </div>

                                    @foreach ($allContacts as $contact)
                                        <div class="form-check contact-item mb-2">
                                            <input
                                                class="form-check-input contact-checkbox"
                                                type="checkbox"
                                                name="contact_ids[]"
                                                value="{{ $contact->id }}"
                                                id="contact-{{ $contact->id }}"
                                                {{ request('contact_id') == $contact->id ? 'checked' : '' }}
                                            />
                                            <label
                                                class="form-check-label w-100 cursor-pointer d-flex flex-column"
                                                for="contact-{{ $contact->id }}"
                                            >
                                                <span
                                                    class="fw-bold text-body"
                                                    >{{ $contact->company_name ?? 'No Name' }}</span
                                                >
                                                <span class="text-muted small"
                                                    ><i class="bi bi-envelope me-1"></i>{{ $contact->email }}</span
                                                >
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-4">
                            <label for="template_selector" class="form-label fw-bold">
                                Use Email Template (Optional)
                            </label>
                            <select id="template_selector" class="form-select">
                                <option value="">-- Type manual message or select an email template --</option>
                                @foreach ($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-warning mt-2 d-block">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Selecting a template will overwrite
                                all text in the message editor below.
                            </small>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="subject" class="form-label fw-bold"
                                >Email Subject <span class="text-danger">*</span></label
                            >
                            <input
                                type="text"
                                id="subject"
                                class="form-control @error('subject') is-invalid @enderror"
                                name="subject"
                                value="{{ old('subject') }}"
                                placeholder="Example: Special Offer from Company"
                                required
                            />
                            @error ('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mt-2">
                            <div class="card border shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rounded-3 p-2 bg-body-tertiary me-2">
                                            <i class="bi bi-magic fs-5"></i>
                                        </div>

                                        <div>
                                            <h6 class="mb-0 fw-semibold">Email Personalization Variables</h6>
                                            <small class="text-body-secondary">
                                                Automatic tags for recipient data
                                            </small>
                                        </div>
                                    </div>

                                    <p class="mb-3 text-body-secondary">The system automatically changes the following codes according to the recipient's data. Please <em>copy-paste</em> the following tags into the email body:</p>

                                    <div class="d-flex flex-wrap gap-2">
                                        <div
                                            class="border rounded-3 px-3 py-2 bg-body-tertiary d-flex align-items-center"
                                        >
                                            <code class="fw-semibold me-2">[perusahaan]</code>
                                            <small class="border-start ps-2 text-body-secondary"> Company Name </small>
                                        </div>

                                        <div
                                            class="border rounded-3 px-3 py-2 bg-body-tertiary d-flex align-items-center"
                                        >
                                            <code class="fw-semibold me-2">[email]</code>
                                            <small class="border-start ps-2 text-body-secondary"> Email Address </small>
                                        </div>

                                        <div
                                            class="border rounded-3 px-3 py-2 bg-body-tertiary d-flex align-items-center"
                                        >
                                            <code class="fw-semibold me-2">[telepon]</code>
                                            <small class="border-start ps-2 text-body-secondary"> Phone Number </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="body" class="form-label fw-bold"
                                >Message Body <span class="text-danger">*</span></label
                            >
                            <textarea
                                id="body"
                                class="form-control @error('body') is-invalid @enderror"
                                name="body"
                                required
                                >{{ old('body') }}</textarea
                            >
                            @error ('body')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div
                        class="d-flex flex-column flex-md-row justify-content-end gap-2 mt-4 pt-3 border-top border-secondary-subtle"
                    >
                        <a href="{{ route('crm.email-blasts.index') }}" class="btn btn-light-secondary">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                        <button
                            type="submit"
                            class="btn btn-primary"
                            onclick="
                                return confirm(
                                    'Are you sure the data is correct and you want to send this email queue?',
                                );
                            "
                        >
                            <i class="bi bi-send-fill me-1"></i> Process Delivery
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push ('scripts')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function () {
            // 1. show / hide contact checkbox area based on selection
            $('#target_type').on('change', function () {
                if ($(this).val() === 'selected') {
                    $('#manual_contact_selection').slideDown();
                } else {
                    $('#manual_contact_selection').slideUp();
                }
            });

            // 2. real-time contact checkbox search feature
            $('#search-contact').on('keyup', function () {
                let value = $(this).val().toLowerCase();
                $('.contact-item').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // 3. select all search results feature
            $('#select-all-contacts').on('change', function () {
                let isChecked = $(this).is(':checked');
                // only check/uncheck elements that are currently visible (search filter results)
                $('.contact-item:visible .contact-checkbox').prop('checked', isChecked);
            });

            // 4. initialize summernote editor with full toolbar settings
            $('#body').summernote({
                placeholder: 'Type your email message here...',
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

            // 5. inject template data from server to js without needing additional ajax calls
            const templatesData = @json ($templates->keyBy('id'));

            // 6. event listener when template dropdown is changed to fill editor content
            $('#template_selector').on('change', function () {
                let id = $(this).val();

                if (id && templatesData[id]) {
                    // insert template content into summernote workspace
                    $('#body').summernote('code', templatesData[id].content || '');

                    // automatically fill subject column if template has a name and subject is not manually filled
                    if (!$('#subject').val()) {
                        $('#subject').val(templatesData[id].name);
                    }
                } else {
                    // clear editor if user goes back to selecting manual input
                    $('#body').summernote('code', '');
                }
            });
        });
    </script>
@endpush
