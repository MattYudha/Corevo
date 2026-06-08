@extends ('layouts.dashboard')

@section ('content')
    @php
        $userRole = Auth::user()->employee?->role?->title ?? '';
        $isHROrPowerUser = \App\Constants\Roles::isAdmin($userRole);

        // for hr/admin: count the number of incoming letters that need approval
        $pendingCount = $isHROrPowerUser ? \App\Models\Letter::where('status', 'pending')->count() : 0;

        // for all employees: count the number of letters waiting for their signature
        $myPendingSignaturesCount = \App\Models\Signature::where('user_id', Auth::id())
            ->where(function ($query) {
                $query->whereNull('signature_image')->orWhere('signature_image', 'PENDING');
            })
            ->count();
    @endphp
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>
                        Letters
                        @if ($isHROrPowerUser && $pendingCount > 0)
                            <span class="badge bg-warning fs-6 ms-2">{{ $pendingCount }} Pending</span>
                        @endif
                    </h3>
                    <p class="text-subtitle text-muted">Management of letter submissions, drafts, and archives.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Letters</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="page-content">
        <section class="section">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header border-bottom mb-3 text-start d-flex flex-wrap gap-2">
                    <a href="{{ route('letters.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Create New Letter
                    </a>

                    <a href="{{ route('signatures.my') }}" class="btn btn-success position-relative">
                        <i class="bi bi-pen me-1"></i> My Signature Requests

                        @if ($myPendingSignaturesCount > 0)
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-3 bg-danger border border-light"
                            >
                                {{ $myPendingSignaturesCount }}
                                <span class="visually-hidden">unsigned documents</span>
                            </span>
                        @endif
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="letter-table" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>Letter No.</th>
                                    <th>Subject</th>
                                    <th>Sender</th>
                                    <th>Status</th>
                                    <th>Date Created</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @push ('scripts')
        <script>
            $(function () {
                $('#letter-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('letters.index') }}',
                    order: [[4, 'desc']],
                    columns: [
                        {
                            data: 'letter_number',
                            name: 'letter_number',
                            defaultContent: '<span class="text-muted fst-italic">Draft</span>',
                        },
                        { data: 'subject', name: 'subject' },
                        { data: 'user.name', name: 'user.name' },
                        { data: 'status_badge', name: 'status', orderable: false, searchable: false },
                        { data: 'created_date', name: 'created_date' },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
                    ],
                });

                $(document).on('submit', '.delete-letter-form', function (e) {
                    e.preventDefault();
                    let konfirmasi = confirm(
                        'Warning!\nAre you sure you want to permanently delete this letter? Once deleted, the data cannot be recovered.',
                    );
                    if (konfirmasi) {
                        this.submit();
                    }
                });
            });
        </script>
    @endpush
@endsection
