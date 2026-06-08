@extends ('layouts.dashboard')

@section ('content')
    <div class="page-heading mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="d-flex align-items-center order-2 order-md-1 mt-3 mt-md-0">
                <a href="{{ route('letters.index') }}" class="btn btn-secondary me-3" title="Back">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>

                <div>
                    <h3 class="mb-0">My Signatures Requests</h3>
                    <p class="text-subtitle text-muted mb-0 mt-1">List of documents requiring my signature.</p>
                </div>
            </div>

            <nav aria-label="breadcrumb" class="breadcrumb-header order-1 order-md-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('letters.index') }}">Letters</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Signatures Requests</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="page-content">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle w-100" id="signatures-table">
                        <thead>
                            <tr>
                                <th>Letter No.</th>
                                <th>Subject</th>
                                <th>Your Signature Status</th>
                                <th class="text-center" style="width: 150px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mySignatures as $sig)
                                @php
                                    // check if the user has signed
                                    $isSigned = !empty($sig->signature_image) && $sig->signature_image !== 'PENDING';
                                @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $sig->signable->letter_number ?? 'No number yet' }}</td>
                                    <td>{{ $sig->signable->subject ?? '-' }}</td>
                                    <td>
                                        @if ($isSigned)
                                            <span
                                                class="badge bg-success-subtle text-success-emphasis border border-success-subtle"
                                            >
                                                <i class="bi bi-check-circle-fill me-1"></i> Completed
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle"
                                            >
                                                <i class="bi bi-clock-fill me-1"></i> Waiting for Your Signature
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($isSigned)
                                            <a
                                                href="{{ route('letters.show', $sig->signable_id) }}"
                                                class="btn btn-sm btn-outline-info shadow-sm rounded-3"
                                            >
                                                <i class="bi bi-eye me-1"></i> View Letter
                                            </a>
                                        @else
                                            <a
                                                href="{{ route('letters.show', $sig->signable_id) }}"
                                                class="btn btn-sm btn-primary shadow-sm rounded-3"
                                            >
                                                <i class="bi bi-pen me-1"></i> Review & Sign
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <i
                                            class="bi bi-check2-circle text-success d-block mb-3"
                                            style="font-size: 3rem; opacity: 0.5"
                                        ></i>
                                        <h6 class="fw-bold text-body">All Clear!</h6>
                                        <p class="text-body-secondary mb-0">No document history involving your signature yet.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @push ('scripts')
        <script>
            $(document).ready(function () {
                if ($('#signatures-table tbody tr td').length > 1) {
                    $('#signatures-table').DataTable({
                        language: {
                            search: "<i class='bi bi-search'></i>",
                            searchPlaceholder: 'Search letter...',
                            paginate: {
                                previous: "<i class='bi bi-chevron-left'></i>",
                                next: "<i class='bi bi-chevron-right'></i>",
                            },
                        },
                        order: [],
                        columnDefs: [{ orderable: false, targets: 3 }],
                    });
                }
            });
        </script>
    @endpush
@endsection
