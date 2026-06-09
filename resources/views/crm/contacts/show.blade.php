@extends ('layouts.dashboard')

@section ('content')
    <div class="page-heading mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="d-flex align-items-center order-2 order-md-1 mt-3 mt-md-0">
                <a href="{{ route('crm.contacts.index') }}" class="btn btn-secondary me-3" title="Back">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>

                <div>
                    <h3 class="mb-0">Contact Details</h3>
                    <p class="text-subtitle text-muted mb-0 mt-1">View comprehensive profile details of the selected company contact.</p>
                </div>
            </div>

            <nav aria-label="breadcrumb" class="breadcrumb-header order-1 order-md-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('crm.contacts.index') }}">Contacts</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Contact Details</li>
                </ol>
            </nav>
        </div>
    </div>
    <section class="section">
        <div class="card w-100">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="card-title mb-1">{{ $contact->company_name }}</h4>
                    <p class="card-subtitle text-muted small">Contact ID: #{{ $contact->id }} &bull; Added on {{ $contact->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
            <div class="card-body mt-3">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <tbody>
                            <tr>
                                <th style="width: 25%" class="fw-bold">Company Name</th>
                                <td class="fw-bold fs-5">{{ $contact->company_name }}</td>
                            </tr>
                            <tr>
                                <th class="fw-bold">Complete Address</th>
                                <td>
                                    {{
                                        $contact->address ??
                                            'No official address data available'
                                    }}
                                </td>
                            </tr>
                            <tr>
                                <th class="fw-bold">Phone / Telephone</th>
                                <td>
                                    @if ($contact->phone)
                                        <span class="text-primary"
                                            ><i class="bi bi-telephone-fill me-1"></i> {{ $contact->phone }}</span
                                        >
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="fw-bold">Email Address</th>
                                <td>
                                    @if ($contact->email && $contact->email !== '-')
                                        <span><i class="bi bi-envelope-fill me-1"></i> {{ $contact->email }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="fw-bold">Website Availability</th>
                                <td>
                                    @if ($contact->has_website)
                                        <span class="badge bg-light-success text-success fw-bold"
                                            ><i class="bi bi-check-circle-fill me-1"></i> Has Website</span
                                        >
                                    @else
                                        <span class="badge bg-light-danger text-danger fw-bold"
                                            ><i class="bi bi-x-circle-fill me-1"></i> No Website</span
                                        >
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="fw-bold">Website URL</th>
                                <td>
                                    @if ($contact->has_website && $contact->website_url !== '-')
                                        <a
                                            href="{{ Str::startsWith($contact->website_url, 'http') ? $contact->website_url : 'https://' . $contact->website_url }}"
                                            target="_blank"
                                            class="text-decoration-underline"
                                        >
                                            <i class="bi bi-box-arrow-up-right me-1"></i> {{ $contact->website_url }}
                                        </a>
                                    @else
                                        <span class="text-muted">{{ $contact->website_url }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="fw-bold">Lead Source</th>
                                <td>
                                    <span
                                        class="badge bg-secondary px-2 py-1 fs-7"
                                        >{{ $contact->source ?? 'Unknown' }}</span
                                    >
                                </td>
                            </tr>
                            <tr>
                                <th class="fw-bold">Last Updated</th>
                                <td>{{ $contact->updated_at->format('d M Y, H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('crm.contacts.edit', $contact->id) }}" class="btn btn-warning text-dark">
                        <i class="bi bi-pencil-square me-1"></i> Edit Contact Info
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
