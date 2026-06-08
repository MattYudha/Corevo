@extends ('layouts.dashboard')

@section ('content')
    @php
        use App\Constants\Roles;

        $userRole = Auth::user()->employee->role->title ?? '';
        $isAllowedAdmin = Roles::isAdmin($userRole);

        $isCreator = false;
        if (isset($signatures) && $signatures->count() > 0) {
            $isCreator = $signatures->first()->signable->user_id === Auth::id();
        }

        $canManage = $isAllowedAdmin || $isCreator;
    @endphp
    <div class="page-heading mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="d-flex align-items-center order-2 order-md-1 mt-3 mt-md-0">
                <a href="{{ route('letters.show', $id) }}" class="btn btn-secondary me-3" title="Back">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>

                <div>
                    <h3 class="mb-0">Manage Signatures</h3>
                    <p class="text-subtitle text-muted mb-0 mt-1">Preview document content and manage approval actions.</p>
                </div>
            </div>

            <nav aria-label="breadcrumb" class="breadcrumb-header order-1 order-md-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('letters.index') }}">Letters</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('letters.show', $id) }}">Letter Preview</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manage Signatures</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="page-content">
        <section class="section">
            @if (session('generated_link'))
                <div class="card border-primary mb-4 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex gap-3 mb-3">
                            <div
                                class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 42px; height: 42px; margin-top: 2px"
                            >
                                <i
                                    class="bi bi-patch-check-fill text-success"
                                    style="font-size: 1.2rem; line-height: 0.8; transform: translateY(-1px)"
                                ></i>
                            </div>
                            <div>
                                <div class="fw-bold" style="line-height: 1.2; margin-bottom: 2px">
                                    Public Signature Link Created Successfully
                                </div>
                                <small class="text-body-secondary"> Ready to be shared with external parties </small>
                            </div>
                        </div>

                        <p class="mb-3 text-body-secondary">Please copy the information below and send it to external parties via WhatsApp or Email.</p>

                        <div class="input-group">
                            <textarea
                                class="form-control bg-body-tertiary text-body"
                                id="new-generated-link"
                                readonly
                                rows="2"
                                style="resize: none"
                            >
                                Access Link: {{ session('generated_link') }}
                                OTP Code: {{ session('generated_otp') }}
                            </textarea
                            >

                            <button
                                type="button"
                                class="btn btn-primary fw-semibold"
                                onclick="copyLink('new-generated-link', this)"
                            >
                                <i class="bi bi-clipboard me-1"></i> Copy Data
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible shadow-sm border-0 rounded-3 mb-4 show fade">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible shadow-sm border-0 rounded-3 mb-4 show fade">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0 rounded-3">
                <div
                    class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-2 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3"
                >
                    <div>
                        <h5 class="fw-bold mb-1">Signer List</h5>
                        <p class="small text-body-secondary mb-0">Status and history of digital signatures on the document.</p>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        @if ($canManage)
                            <button
                                type="button"
                                class="btn btn-success fw-semibold rounded-3 shadow-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#internalSignModal"
                            >
                                <i class="bi bi-person-plus me-1"></i> Internal Signature
                            </button>
                            <button
                                type="button"
                                class="btn btn-primary fw-semibold rounded-3 shadow-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#externalSignModal"
                            >
                                <i class="bi bi-link-45deg me-1"></i> Add External Signature
                            </button>
                        @endif
                    </div>
                </div>

                <div class="card-body p-4 pt-2">
                    @if (isset($signatures) && $signatures->count() > 0)
                        <div class="row g-4">
                            @foreach ($signatures as $sig)
                                @php
                                    $itemIsCreator = $sig->signable->user_id === Auth::id();
                                    $itemCanManage = $isAllowedAdmin || $itemIsCreator;
                                    $isSigned = !empty($sig->signature_image) && $sig->signature_image !== 'PENDING';
                                @endphp
                                <div class="col-md-6 col-xl-4">
                                    <div
                                        class="card border border-secondary-subtle bg-body-tertiary shadow-sm h-100 rounded-3 position-relative overflow-hidden mb-0"
                                    >
                                        <div
                                            class="position-absolute top-0 start-0 w-100"
                                            style="height: 4px; background-color: {{ $isSigned ? '#198754' : '#ffc107' }};"
                                        ></div>

                                        <div class="card-body p-4 d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                @if ($isSigned)
                                                    <span
                                                        class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-3 fw-bold small"
                                                    >
                                                        <i class="bi bi-check-circle-fill me-1"></i> Signed
                                                    </span>
                                                @else
                                                    <span
                                                        class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-3 fw-bold small"
                                                    >
                                                        <i class="bi bi-clock-fill me-1"></i> Waiting
                                                    </span>
                                                @endif

                                                @if ($itemCanManage)
                                                    <form
                                                        action="{{ route('signatures.destroy', $sig->id) }}"
                                                        method="POST"
                                                        class="m-0"
                                                    >
                                                        @csrf
                                                        @method ('DELETE')
                                                        <button
                                                            type="submit"
                                                            class="btn btn-sm btn-outline-danger border-0 rounded-circle"
                                                            data-bs-toggle="tooltip"
                                                            title="Delete Signature Data"
                                                            onclick="
                                                                return confirm(
                                                                    'Are you sure you want to delete this signer?',
                                                                );
                                                            "
                                                        >
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>

                                            <h6 class="fw-bold text-body mb-1">
                                                {{
                                                    $sig->user_id
                                                        ? $sig->signer->name
                                                        : $sig->external_name ?? 'External Party'
                                                }}
                                            </h6>
                                            <p class="small text-body-secondary mb-3">
                                                <i class="bi bi-briefcase me-1"></i>
                                                {{
                                                    $sig->user_id
                                                        ? $sig->signer->employee->position->position_name ?? 'Employee'
                                                        : $sig->external_title ?? 'Vendor / External'
                                                }}
                                                <br />
                                                <i class="bi bi-building me-1"></i>
                                                {{
                                                    $sig->user_id
                                                        ? 'Company Internal'
                                                        : $sig->external_company ?? '-'
                                                }}
                                            </p>

                                            <div class="mt-auto">
                                                @if ($isSigned)
                                                    <div
                                                        class="bg-body rounded-3 p-3 text-center border border-secondary-subtle"
                                                    >
                                                        <div class="signature-display mb-2">
                                                            <img
                                                                src="{{ $sig->signature_image }}"
                                                                alt="Signature"
                                                                class="img-fluid"
                                                                style="max-height: 60px; object-fit: contain"
                                                            />
                                                        </div>
                                                        <div class="border-top border-secondary-subtle pt-2 mt-2">
                                                            <span
                                                                class="d-block text-body-secondary"
                                                                style="font-size: 0.75rem"
                                                                >Authorized on:</span
                                                            >
                                                            <strong class="small text-body">
                                                                {{
                                                                    \Carbon\Carbon::parse($sig->signed_date ?? $sig->updated_at)->format(
                                                                        'd M Y, H:i',
                                                                    )
                                                                }}
                                                            </strong>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div
                                                        class="bg-body rounded-3 p-3 text-center border border-secondary-subtle mb-3"
                                                        style="border-style: dashed !important"
                                                    >
                                                        <i
                                                            class="bi bi-hourglass-split text-warning d-block mb-2"
                                                            style="font-size: 2rem"
                                                        ></i>
                                                        <span class="small text-body-secondary fw-medium"
                                                            >Waiting for signature...</span
                                                        >
                                                    </div>
                                                    @if ($sig->user_id)
                                                        @if (Auth::id() === $sig->user_id)
                                                            <a
                                                                href="{{ url('signatures/' . request('signable') . '/' . request('id') . '/pad') }}"
                                                                class="btn btn-success w-100 fw-bold shadow-sm rounded-3"
                                                            >
                                                                <i class="bi bi-vector-pen me-1"></i> Sign
                                                            </a>
                                                        @else
                                                            <div
                                                                class="alert alert-light-warning py-2 mb-0 text-center small border"
                                                            >
                                                                <i class="bi bi-clock me-1"></i> Waiting for the
                                                                respective employee
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="input-group input-group-sm">
                                                            <textarea id="link-{{ $sig->id }}" class="d-none">
                                                                Access Link: {{ route('signatures.public.pad', $sig->token) }}
                                                                OTP Code: {{ $sig->otp_code }}
                                                            </textarea
                                                            >

                                                            <input
                                                                type="text"
                                                                class="form-control bg-body border-end-0 text-body-secondary"
                                                                value="Link & OTP Available"
                                                                readonly
                                                            />
                                                            <button
                                                                class="btn btn-outline-primary fw-semibold"
                                                                type="button"
                                                                onclick="copyLink('link-{{ $sig->id }}', this)"
                                                            >
                                                                <i class="bi bi-clipboard"></i> Copy
                                                            </button>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i
                                class="bi bi-folder2-open text-body-secondary d-block mb-3"
                                style="font-size: 4rem; opacity: 0.5"
                            ></i>
                            <h5 class="fw-bold text-body">No Signatures Yet</h5>
                            <p class="text-body-secondary mb-4">This document does not have any internal or external signer data yet.</p>

                            @if ($canManage)
                                <div class="d-flex justify-content-center gap-2 mt-4 flex-wrap">
                                    <button
                                        type="button"
                                        class="btn btn-outline-primary fw-semibold rounded-3 shadow-sm px-4"
                                        data-bs-toggle="modal"
                                        data-bs-target="#internalSignModal"
                                    >
                                        <i class="bi bi-person-plus me-2"></i>Internal Employee Signature
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-primary fw-semibold rounded-3 shadow-sm px-4"
                                        data-bs-toggle="modal"
                                        data-bs-target="#externalSignModal"
                                    >
                                        <i class="bi bi-link-45deg me-2"></i>Create External Link
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
    @if ($canManage)
        <div class="modal fade" id="internalSignModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-3 shadow bg-body">
                    <form
                        action="{{ route('signatures.store_internal', ['signable' => request('signable'), 'id' => request('id')]) }}"
                        method="POST"
                    >
                        @csrf
                        <div class="modal-header border-bottom-0 pt-4 px-4">
                            <h5 class="modal-title fw-bold text-body">
                                <i class="bi bi-person-plus text-primary me-1"></i> Add Internal Signature
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-4 pb-2">
                            <p class="text-body-secondary small mb-4">Select an authorized employee from the system to sign this document.</p>
                            <div class="form-group mb-3">
                                <label class="form-label fw-semibold text-body"
                                    >Select Employee <span class="text-danger">*</span></label
                                >
                                <select name="user_id" class="form-select bg-body-tertiary text-body" required>
                                    <option value="">-- Search & Select --</option>
                                    @if (isset($users) && $users->count() > 0)
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">
                                                {{ $user->name }} - {{ $user->employee->position->position_name ?? 'Employee' }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 pb-4 px-4">
                            <button
                                type="button"
                                class="btn btn-secondary px-4 fw-semibold rounded-3"
                                data-bs-dismiss="modal"
                            >
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm rounded-3">
                                Save Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="externalSignModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-3 shadow bg-body">
                    <form
                        action="{{ route('signatures.generate_public_link', ['signable' => request('signable'), 'id' => request('id')]) }}"
                        method="POST"
                    >
                        @csrf
                        <div class="modal-header border-bottom-0 pt-4 px-4">
                            <h5 class="modal-title fw-bold text-body">
                                <i class="bi bi-link-45deg text-primary me-1"></i> Create External Signature Link
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body px-4 pb-2">
                            <p class="text-body-secondary small mb-4">Create a specific link so vendors/clients can sign without having to log in to the system.</p>

                            <div class="form-group mb-3">
                                <label class="form-label fw-semibold text-body"
                                    >Full Name <span class="text-danger">*</span></label
                                >
                                <input
                                    type="text"
                                    name="external_name"
                                    class="form-control bg-body-tertiary text-body"
                                    required
                                    placeholder="Example: Budi Santoso"
                                />
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label fw-semibold text-body"
                                    >Email Address <span class="text-danger">*</span></label
                                >
                                <input
                                    type="email"
                                    name="external_email"
                                    class="form-control bg-body-tertiary text-body"
                                    required
                                    placeholder="Example: budi@vendor.com"
                                />
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label fw-semibold text-body">Position (Optional)</label>
                                <input
                                    type="text"
                                    name="external_title"
                                    class="form-control bg-body-tertiary text-body"
                                    placeholder="Example: Managing Director"
                                />
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label fw-semibold text-body"
                                    >Company / Institution <span class="text-danger">*</span></label
                                >
                                <input
                                    type="text"
                                    name="external_company"
                                    class="form-control bg-body-tertiary text-body"
                                    required
                                    placeholder="Example: PT Mitra Sejahtera"
                                />
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 pb-4 px-4">
                            <button
                                type="button"
                                class="btn btn-secondary px-4 fw-semibold rounded-3"
                                data-bs-dismiss="modal"
                            >
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm rounded-3">
                                Generate Link
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <style>
        /* ensure signature image stays bright white in mazer dark mode */
        [data-bs-theme='dark'] .signature-display img {
            filter: invert(1) brightness(2);
        }
    </style>
@endsection

@push ('scripts')
    <script>
        function copyLink(elementId, btnElement) {
            var copyText = document.getElementById(elementId);
            copyText.select();
            copyText.setSelectionRange(0, 99999);

            navigator.clipboard
                .writeText(copyText.value)
                .then(function () {
                    var originalHtml = btnElement.innerHTML;
                    var originalClass = btnElement.className;

                    btnElement.innerHTML = '<i class="bi bi-check2-all me-1"></i> Copied!';
                    btnElement.classList.remove('btn-primary', 'btn-outline-primary');
                    btnElement.classList.add('btn-success', 'text-white');

                    setTimeout(function () {
                        btnElement.innerHTML = originalHtml;
                        btnElement.className = originalClass;
                    }, 2000);
                })
                .catch(function (err) {
                    console.error('Failed to copy link: ', err);
                });
        }

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
@endpush
