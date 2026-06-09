@extends ('layouts.dashboard')

@section ('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Contacts</h3>
                    <p class="text-subtitle text-muted">Manage company contacts and lead information.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Contacts</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('crm.contacts.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add Contact
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle" id="contact-table" style="width: 100%">
                        <thead>
                            <tr>
                                <th style="width: 35%">Company Name</th>
                                <th style="width: 20%">Phone</th>
                                <th style="width: 25%">Email</th>
                                <th style="width: 20%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($contacts as $contact)
                                <tr>
                                    <td
                                        class="fw-bold text-truncate-custom"
                                        data-fulltext="{{ $contact->company_name }}"
                                    >
                                        {{ $contact->company_name }}
                                    </td>
                                    <td>{{ $contact->phone ?? '-' }}</td>
                                    <td>{{ $contact->email ?? '-' }}</td>
                                    <td class="text-center">
                                        <a
                                            href="{{ route('crm.contacts.show', $contact->id) }}"
                                            class="btn btn-sm btn-outline-info"
                                            title="View"
                                            ><i class="bi bi-eye"></i
                                        ></a>
                                        <a
                                            href="{{ route('crm.contacts.edit', $contact->id) }}"
                                            class="btn btn-sm btn-outline-warning"
                                            title="Edit"
                                            ><i class="bi bi-pencil"></i
                                        ></a>
                                        <form
                                            action="{{ route('crm.contacts.destroy', $contact->id) }}"
                                            method="POST"
                                            class="d-inline form-delete"
                                        >
                                            @csrf
                                            @method ('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    @push ('scripts')
        <script>
            $(document).ready(function () {
                $('#contact-table').DataTable({
                    responsive: true,
                    pageLength: 25,
                    columnDefs: [
                        { orderable: false, targets: -1 },
                        {
                            targets: [0, 2],
                            render: function (data, type, row, meta) {
                                let maxLength = 25;

                                if (!data) return '-';

                                if (type === 'display' && data.length > maxLength) {
                                    let shortText = data.substr(0, maxLength) + '...';
                                    let fullText = data.replace(/"/g, '&quot;');

                                    return (
                                        '<span class="short-text">' +
                                        shortText +
                                        '</span>' +
                                        '<span class="full-text d-none">' +
                                        fullText +
                                        '</span>' +
                                        ' <a href="javascript:void(0);" class="read-more-btn text-primary small ms-1">Read more</a>'
                                    );
                                }
                                return data;
                            },
                        },
                    ],
                });

                $('#contact-table').on('click', '.read-more-btn', function () {
                    let btn = $(this);
                    let parent = btn.parent();

                    if (btn.text() === 'Read more') {
                        parent.find('.short-text').addClass('d-none');
                        parent.find('.full-text').removeClass('d-none');
                        btn.text('Show less').removeClass('text-primary').addClass('text-secondary');
                    } else {
                        parent.find('.full-text').addClass('d-none');
                        parent.find('.short-text').removeClass('d-none');
                        btn.text('Read more').removeClass('text-secondary').addClass('text-primary');
                    }
                });

                $(document).on('submit', '.form-delete', function (e) {
                    e.preventDefault();
                    if (typeof window.confirmDelete === 'function') {
                        window.confirmDelete(this, 'Delete this contact?');
                    } else {
                        if (confirm('Are you sure you want to delete this data?')) {
                            this.submit();
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
