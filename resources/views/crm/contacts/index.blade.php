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
                                <th>No</th>
                                <th>Company Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Source</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($contacts as $contact)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold">{{ $contact->company_name }}</td>
                                    <td>{{ $contact->phone ?? '-' }}</td>
                                    <td>{{ $contact->email ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-light-primary">{{ $contact->source ?? '-' }}</span>
                                    </td>
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
                    columnDefs: [{ orderable: false, targets: -1 }],
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
