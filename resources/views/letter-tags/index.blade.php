@extends ('layouts.dashboard')

@section ('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Letter Tags</h3>
                    <p class="text-subtitle text-muted">Manage dynamic variables for letter templates.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Letter Tags</li>
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

            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom mb-3 text-start bg-transparent">
                    <a href="{{ route('letter-tags.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add New Tag
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="table1" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tag Name</th>
                                    <th>Input Type</th>
                                    <th>Description</th>
                                    <th>Default Value</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tags as $index => $tag)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><span class="text-danger fw-bold">[{{ $tag->tag_name }}]</span></td>
                                        <td>
                                            @php
                                                $badgeColor = match ($tag->input_type) {
                                                    'short_text' => 'bg-primary-subtle text-primary-emphasis',
                                                    'long_text' => 'bg-secondary-subtle text-secondary-emphasis',
                                                    'date' => 'bg-info-subtle text-info-emphasis',
                                                    'number' => 'bg-warning-subtle text-warning-emphasis',
                                                    'time' => 'bg-danger-subtle text-danger-emphasis',
                                                    'dropdown' => 'bg-success-subtle text-success-emphasis',
                                                    default => 'bg-dark-subtle text-dark-emphasis',
                                                };

                                                $configBadge = '';
                                                if ($tag->dropdown_type === 'auto_fill') {
                                                    $configBadge =
                                                        '<br><small class="text-primary fw-bold" style="font-size: 0.7rem;"><i class="bi bi-magic"></i> Auto-Fill</small>';
                                                } elseif ($tag->dropdown_type === 'model') {
                                                    $configBadge =
                                                        '<br><small class="text-success fw-bold" style="font-size: 0.7rem;"><i class="bi bi-database"></i> DB Relation</small>';
                                                } elseif ($tag->dropdown_type === 'manual') {
                                                    $configBadge =
                                                        '<br><small class="text-muted fw-bold" style="font-size: 0.7rem;"><i class="bi bi-card-list"></i> Manual</small>';
                                                }
                                            @endphp

                                            <span class="badge {{ $badgeColor }}">
                                                {{ ucfirst(str_replace('_', ' ', $tag->input_type)) }}
                                            </span>
                                            {!! $configBadge !!}
                                        </td>
                                        <td class="text-body-secondary">
                                            {{
                                                \Illuminate\Support\Str::limit(
                                                    $tag->description ?? '-',
                                                    30,
                                                )
                                            }}
                                        </td>
                                        <td class="text-body-secondary">
                                            {{
                                                \Illuminate\Support\Str::limit(
                                                    $tag->default_value ?? '-',
                                                    30,
                                                )
                                            }}
                                        </td>

                                        <td class="text-center">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-info btn-show"
                                                data-name="{{ $tag->tag_name }}"
                                                data-type="{{ ucfirst(str_replace('_', ' ', $tag->input_type)) }}"
                                                data-desc="{{ $tag->description ?? '-' }}"
                                                data-default="{{ $tag->default_value ?? '-' }}"
                                                data-droptype="{{ $tag->dropdown_type }}"
                                                data-dropmodel="{{ $tag->dropdown_model }}"
                                                data-dropoptions="{{ $tag->dropdown_options }}"
                                                title="Tag Details"
                                            >
                                                <i class="bi bi-eye"></i>
                                            </button>

                                            <a
                                                href="{{ route('letter-tags.edit', $tag->id) }}"
                                                class="btn btn-sm btn-outline-warning"
                                                title="Edit Tag"
                                            >
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <form
                                                action="{{ route('letter-tags.destroy', $tag->id) }}"
                                                method="POST"
                                                class="d-inline delete-form"
                                            >
                                                @csrf
                                                @method ('DELETE')
                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete Tag"
                                                >
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="bi bi-tags fs-1 d-block mb-2"></i>
                                            No tag data available.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="modal fade" id="showTagModal" tabindex="-1" aria-labelledby="showTagModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-body text-body">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title" id="showTagModalLabel">
                        <i class="bi bi-info-circle text-primary me-2"></i>Letter Tag Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless table-sm mb-3 text-body">
                        <tr>
                            <th style="width: 35%">Tag Name</th>
                            <td>: <span class="badge bg-primary fs-6" id="detail-name"></span></td>
                        </tr>
                        <tr>
                            <th>Input Type</th>
                            <td>: <span id="detail-type" class="fw-bold"></span></td>
                        </tr>

                        <tr id="row-config" style="display: none">
                            <th>System Config</th>
                            <td>: <span id="detail-config-badge"></span></td>
                        </tr>
                        <tr id="row-options" style="display: none">
                            <th>Data Config</th>
                            <td>: <span id="detail-options" class="text-muted fw-bold"></span></td>
                        </tr>
                    </table>

                    <div class="mb-3">
                        <label class="fw-bold mb-1 text-body-secondary">Description:</label>
                        <textarea
                            id="detail-desc"
                            class="form-control bg-body-tertiary text-body"
                            rows="3"
                            readonly
                            style="resize: none"
                        ></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="fw-bold mb-1 text-body-secondary">Default Value:</label>
                        <textarea
                            id="detail-default"
                            class="form-control bg-body-tertiary text-body"
                            rows="2"
                            readonly
                            style="resize: none"
                        ></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push ('scripts')
    <script>
        $(document).ready(function () {
            if ($.fn.DataTable) {
                $('#table1').DataTable({
                    scrollX: false,
                    autoWidth: false,
                });
            }

            $('.btn-show').on('click', function () {
                let name = $(this).data('name');
                let type = $(this).data('type');
                let desc = $(this).data('desc');
                let def = $(this).data('default');
                let dropType = $(this).data('droptype');
                let dropModel = $(this).data('dropmodel');
                let dropOptions = $(this).data('dropoptions');

                $('#detail-name').text('[' + name + ']');
                $('#detail-type').text(type);
                $('#detail-desc').val(desc);
                $('#detail-default').val(def);

                $('#row-config').hide();
                $('#row-options').hide();

                if (dropType) {
                    $('#row-config').show();
                    $('#row-options').show();

                    if (dropType === 'auto_fill') {
                        $('#detail-config-badge').html(
                            '<span class="badge bg-primary-subtle text-primary-emphasis"><i class="bi bi-magic me-1"></i>Auto-Fill</span>',
                        );
                        $('#detail-options').html('<span class="text-primary">Key: ' + dropModel + '</span>');
                    } else if (dropType === 'model') {
                        $('#detail-config-badge').html(
                            '<span class="badge bg-success-subtle text-success-emphasis"><i class="bi bi-database me-1"></i>DB Relation</span>',
                        );
                        $('#detail-options').html('<span class="text-success">Table: ' + dropModel + '</span>');
                    } else if (dropType === 'manual') {
                        $('#detail-config-badge').html(
                            '<span class="badge bg-secondary-subtle text-secondary-emphasis"><i class="bi bi-card-list me-1"></i>Manual</span>',
                        );
                        $('#detail-options').html('<span class="text-muted">' + dropOptions + '</span>');
                    }
                }

                $('#showTagModal').modal('show');
            });

            $('.delete-form').on('submit', function (e) {
                e.preventDefault();
                window.confirmDelete(
                    this,
                    'Permanently delete this tag? Warning: Letter templates using this tag might encounter errors or lose data!',
                );
            });
        });
    </script>
@endpush
