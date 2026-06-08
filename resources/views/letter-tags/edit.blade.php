@extends ('layouts.dashboard')

@section ('content')
    <div class="page-heading mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="d-flex align-items-center order-2 order-md-1 mt-3 mt-md-0">
                <a href="{{ route('letter-tags.index') }}" class="btn btn-secondary me-3" title="Back">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>

                <div>
                    <h3 class="mb-0">Edit Letter Tag</h3>
                    <p class="text-subtitle text-muted mb-0 mt-1">Modify dynamic tag configuration.</p>
                </div>
            </div>

            <nav aria-label="breadcrumb" class="breadcrumb-header order-1 order-md-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('letter-tags.index') }}">Letter Tags</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Letter Tag</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="page-content">
        <div class="card shadow-sm">
            <div class="card-body pt-4">
                <form action="{{ route('letter-tags.update', $letterTag->id) }}" method="POST">
                    @csrf
                    @method ('PUT')

                    <div class="alert alert-light-warning mb-4">
                        <i class="bi bi-exclamation-triangle me-2"></i> <strong>Warning:</strong> Changing the "Tag
                        Name" will automatically synchronize (replace) the variable in all Letter Templates currently
                        using this tag.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tag Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">[</span>
                            <input
                                type="text"
                                class="form-control"
                                name="tag_name"
                                value="{{ $letterTag->tag_name }}"
                                required
                            />
                            <span class="input-group-text">]</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea
                            class="form-control"
                            name="description"
                            rows="2"
                            >{{ $letterTag->description }}</textarea
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Input Type <span class="text-danger">*</span></label>
                        <select class="form-select" name="input_type" id="input_type" required>
                            @foreach (\App\Constants\LetterTagConfig::inputTypes() as $val => $label)
                                <option
                                    value="{{ $val }}"
                                    {{
                                        old('input_type', $letterTag->input_type ?? '') == $val
                                            ? 'selected'
                                            : ''
                                    }}
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-4 p-4 border rounded" style="background-color: var(--bs-tertiary-bg)">
                        <h6 class="mb-3">
                            <i class="bi bi-magic me-2"></i>Automation & Relation Configuration (Optional)
                        </h6>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Data Source</label>
                            <select class="form-select" name="dropdown_type" id="dropdown_type">
                                <option value="">-- Free Input (No Automation) --</option>
                                <optgroup label="Specifically for Text & Date" id="optgroup_autofill">
                                    <option
                                        value="auto_fill"
                                        {{
                                            $letterTag->dropdown_type == 'auto_fill'
                                                ? 'selected'
                                                : ''
                                        }}
                                        >System Auto-Fill (Automatically Filled)
                                    </option>
                                </optgroup>
                                <optgroup label="Specifically for Dropdown" id="optgroup_dropdown">
                                    <option
                                        value="manual"
                                        {{
                                            $letterTag->dropdown_type == 'manual'
                                                ? 'selected'
                                                : ''
                                        }}
                                        >Manual Input (Custom Typing)
                                    </option>
                                    <option value="model" {{ $letterTag->dropdown_type == 'model' ? 'selected' : '' }}
                                        >Fetch from Database (Relation)
                                    </option>
                                </optgroup>
                            </select>
                        </div>

                        <div id="autofill_options_wrapper" class="d-none mb-3">
                            <label class="form-label fw-bold">Select System Data</label>
                            <select class="form-select config-model-input" id="select_autofill">
                                @foreach (\App\Constants\LetterTagConfig::autoFillOptions() as $val => $label)
                                    <option
                                        value="{{ $val }}"
                                        {{
                                            old('dropdown_model', $letterTag->dropdown_model ?? '') == $val
                                                ? 'selected'
                                                : ''
                                        }}
                                    >
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted"
                                >This data will be automatically filled when the letter creation form is opened.</small
                            >
                        </div>

                        <div id="manual_options_wrapper" class="d-none mb-3">
                            <label class="form-label fw-bold">List of Choices (Separate with commas)</label>
                            <textarea
                                class="form-control"
                                name="dropdown_options"
                                rows="2"
                                >{{ $letterTag->dropdown_options }}</textarea
                            >
                        </div>

                        <div id="model_options_wrapper" class="d-none mb-3">
                            <label class="form-label fw-bold text-success">Select Database Table/Model</label>
                            <select class="form-select config-model-input" id="select_model">
                                @foreach (\App\Constants\LetterTagConfig::dropdownModels() as $val => $label)
                                    <option
                                        value="{{ $val }}"
                                        {{
                                            old('dropdown_model', $letterTag->dropdown_model ?? '') == $val
                                                ? 'selected'
                                                : ''
                                        }}
                                    >
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted"
                                >The system will automatically fetch the latest data from the database.</small
                            >
                        </div>

                        <input
                            type="hidden"
                            name="dropdown_model"
                            id="final_dropdown_model"
                            value="{{ $letterTag->dropdown_model }}"
                        />
                    </div>

                    <div class="mb-3 mt-4">
                        <label class="form-label fw-bold">Default Value (Optional)</label>
                        <input
                            type="text"
                            class="form-control"
                            name="default_value"
                            value="{{ $letterTag->default_value }}"
                        />
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i> Update Tag
                        </button>
                        <a href="{{ route('letter-tags.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push ('scripts')
    <script>
        $(document).ready(function () {
            function toggleConfigOptions() {
                let inputType = $('#input_type').val();
                let currentConfig = $('#dropdown_type').val();

                if (inputType === 'dropdown') {
                    $('#optgroup_dropdown').prop('disabled', false).show();
                    $('#optgroup_autofill').prop('disabled', true).hide();
                    if (currentConfig === 'auto_fill') $('#dropdown_type').val('');
                } else {
                    $('#optgroup_dropdown').prop('disabled', true).hide();
                    $('#optgroup_autofill').prop('disabled', false).show();
                    if (currentConfig === 'manual' || currentConfig === 'model') $('#dropdown_type').val('');
                }
                toggleWrappers();
            }

            function toggleWrappers() {
                let sourceType = $('#dropdown_type').val();
                $('#manual_options_wrapper, #model_options_wrapper, #autofill_options_wrapper').addClass('d-none');

                if (sourceType === 'manual') {
                    $('#manual_options_wrapper').removeClass('d-none');
                } else if (sourceType === 'model') {
                    $('#model_options_wrapper').removeClass('d-none');
                } else if (sourceType === 'auto_fill') {
                    $('#autofill_options_wrapper').removeClass('d-none');
                }
                updateHiddenModel();
            }

            function updateHiddenModel() {
                let sourceType = $('#dropdown_type').val();
                if (sourceType === 'model') {
                    $('#final_dropdown_model').val($('#select_model').val());
                } else if (sourceType === 'auto_fill') {
                    $('#final_dropdown_model').val($('#select_autofill').val());
                } else {
                    $('#final_dropdown_model').val('');
                }
            }

            $('#input_type').on('change', toggleConfigOptions);
            $('#dropdown_type').on('change', toggleWrappers);
            $('.config-model-input').on('change', updateHiddenModel);

            toggleConfigOptions();
        });
    </script>
@endpush
