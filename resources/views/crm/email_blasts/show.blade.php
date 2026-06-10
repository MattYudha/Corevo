@extends ('layouts.dashboard')

@section ('content')
    <div class="page-heading mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="d-flex align-items-center order-2 order-md-1 mt-3 mt-md-0">
                <a href="{{ route('crm.email-blasts.index') }}" class="btn btn-secondary me-3" title="Back">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div>
                    <h3 class="mb-0">Live Email Blast Monitor</h3>
                    <p class="text-subtitle text-muted mb-0 mt-1">Delivery status will be updated automatically by the system.</p>
                </div>
            </div>

            <nav aria-label="breadcrumb" class="breadcrumb-header order-1 order-md-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('crm.email-blasts.index') }}">Email Blasts</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Live Email Blast Monitor</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h6 class="text-muted font-semibold text-uppercase mb-2" style="font-size: 0.8rem">
                                Total Target
                            </h6>
                            <h3 class="font-extrabold mb-0 text-body">{{ $blast->target_count }}</h3>
                        </div>
                        <div class="badge bg-primary-subtle text-primary p-3 rounded-3">
                            <i class="bi bi-envelope-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h6 class="text-muted font-semibold text-uppercase mb-2" style="font-size: 0.8rem">
                                Successful
                            </h6>
                            <h3 class="font-extrabold mb-0 text-success" id="count-sent">
                                {{ $blast->recipients->where('status', 'sent')->count() }}
                            </h3>
                        </div>
                        <div class="badge bg-success-subtle text-success p-3 rounded-3">
                            <i class="bi bi-check-circle-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h6 class="text-muted font-semibold text-uppercase mb-2" style="font-size: 0.8rem">
                                Failed
                            </h6>
                            <h3 class="font-extrabold mb-0 text-danger" id="count-failed">
                                {{ $blast->recipients->where('status', 'failed')->count() }}
                            </h3>
                        </div>
                        <div class="badge bg-danger-subtle text-danger p-3 rounded-3">
                            <i class="bi bi-x-circle-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h6 class="text-muted font-semibold text-uppercase mb-2" style="font-size: 0.8rem">
                                System Status
                            </h6>
                            <div id="main-status" class="mt-1">
                                @if ($blast->status == 'completed')
                                    <span class="font-bold text-success fs-5">Completed</span>
                                @elseif ($blast->status == 'processing')
                                    <span class="font-bold text-warning fs-5">Processing</span>
                                @else
                                    <span class="font-bold text-secondary fs-5">Queued</span>
                                @endif
                            </div>
                        </div>
                        <div class="badge bg-secondary-subtle text-secondary p-3 rounded-3">
                            <i class="bi bi-activity fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card shadow-sm border-0">
            <div
                class="card-header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center"
            >
                <div>
                    <h5 class="mb-1 text-body fw-bold">Subject: <span>{{ $blast->subject }}</span></h5>
                    <p class="mb-0 text-sm text-muted">
                        <i class="bi bi-clock me-1"></i> Created on: {{ $blast->created_at->format('d M Y, H:i') }}
                        <span class="mx-2">|</span>
                        <i class="bi bi-person me-1"></i> Creator: {{ $blast->creator->name ?? 'System' }}
                    </p>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive mt-3">
                    <table
                        class="table table-striped table-hover align-middle mb-0"
                        id="recipient-table"
                        style="width: 100%"
                    >
                        <thead>
                            <tr>
                                <th style="width: 25%">Company / Contact</th>
                                <th style="width: 25%">Target Email</th>
                                <th style="width: 15%">Delivery Status</th>
                                <th style="width: 35%">Detail Log</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($blast->recipients as $recipient)
                                <tr id="row-{{ $recipient->id }}">
                                    <td class="fw-bold text-body">{{ $recipient->contact->company_name ?? 'N/A' }}</td>
                                    <td>{{ $recipient->email }}</td>
                                    <td class="status-cell">
                                        @if ($recipient->status == 'sent')
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i> Sent
                                            </span>
                                        @elseif ($recipient->status == 'failed')
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i> Failed
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <span
                                                    class="spinner-border spinner-border-sm me-1"
                                                    role="status"
                                                ></span>
                                                Processing...
                                            </span>
                                        @endif
                                    </td>
                                    <td class="error-cell text-danger text-sm">
                                        {{ $recipient->error_message ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@push ('scripts')
    <script>
        $(document).ready(function () {
            let table = $('#recipient-table').DataTable({
                responsive: true,
                pageLength: 25,
                language: {
                    search: 'Search:',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ recipients',
                },
            });

            let blastId = {{ $blast->id }};
            let currentStatus = '{{ $blast->status }}';

            function fetchLiveUpdates() {
                if (currentStatus === 'completed') return;

                $.ajax({
                    url: `/crm/email-blasts/${blastId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        $('#count-sent').text(response.sent_count);
                        $('#count-failed').text(response.failed_count);

                        currentStatus = response.status;
                        if (currentStatus === 'completed') {
                            $('#main-status').html('<span class="font-bold text-success fs-5">Completed</span>');
                        } else if (currentStatus === 'processing') {
                            $('#main-status').html('<span class="font-bold text-warning fs-5">Processing</span>');
                        }

                        let dataChanged = false;

                        response.recipients.forEach(function (recipient) {
                            let rowNode = $(`#row-${recipient.id}`);
                            if (rowNode.length > 0) {
                                let statusCell = rowNode.find('.status-cell');
                                let errorCell = rowNode.find('.error-cell');
                                let rowUpdated = false;

                                if (recipient.status === 'sent' && !statusCell.html().includes('Sent')) {
                                    statusCell.html(
                                        '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Sent</span>',
                                    );
                                    rowUpdated = true;
                                } else if (recipient.status === 'failed' && !statusCell.html().includes('Failed')) {
                                    statusCell.html(
                                        '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Failed</span>',
                                    );
                                    errorCell.text(recipient.error_message || 'A system error occurred.');
                                    rowUpdated = true;
                                }

                                if (rowUpdated) {
                                    table.row(rowNode).invalidate();
                                    dataChanged = true;
                                }
                            }
                        });

                        if (dataChanged) {
                            table.draw(false);
                        }
                    },
                    error: function () {
                        console.error('Connection temporarily lost. trying to sync data again...');
                    },
                });
            }

            if (currentStatus !== 'completed') {
                setInterval(fetchLiveUpdates, 2500);
            }
        });
    </script>
@endpush
