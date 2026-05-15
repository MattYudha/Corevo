@extends('layouts.dashboard')

@section('content')

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><i class="bi bi-cash-stack"></i> Payrolls</h3>
                <p class="text-subtitle text-muted">Manage employee payroll records.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                        <li class="breadcrumb-item active">Payrolls</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        {{-- filter bar --}}
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-body">
                <div class="row g-3 align-items-end">

                    {{-- filter month --}}
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label fw-bold mb-1">
                            <i class="bi bi-calendar-event"></i> Month
                        </label>
                        <select id="filter-month" class="form-select form-select-sm">
                            <option value="">-- Select Month --</option>
                            @php 
                                $months = [
                                    'January','February','March','April','May','June',
                                    'July','August','September','October','November','December'
                                ]; 
                            @endphp

                            @foreach($months as $i => $m)
                                <option value="{{ $i+1 }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- filter year --}}
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label fw-bold mb-1">
                            <i class="bi bi-calendar"></i> Year
                        </label>
                        <select id="filter-year" class="form-select form-select-sm">
                            @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- filter status --}}
                    <div class="col-12 col-sm-6 col-lg-2">
                        <label class="form-label fw-bold mb-1">
                            <i class="bi bi-check-circle"></i> Status
                        </label>
                        <select id="filter-status" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                            <option value="draft">Draft</option>
                            <option value="approved">Approved</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>

                    {{-- fund source --}}
                    <div class="col-12 col-lg-3">
                        @php
                            $currentAccount = $assetAccounts->where('id', $defaultAccountId)->first();

                            $accountLabel = $currentAccount
                                ? $currentAccount->code . ' - ' . $currentAccount->name
                                : 'Not Set';

                            $labelClass = $currentAccount
                                ? 'text-success'
                                : 'text-danger';
                        @endphp

                        <label class="form-label fw-bold mb-1 d-block">
                            <i class="bi bi-wallet2"></i> Fund Source
                        </label>

                        <button 
                            type="button"
                            class="btn btn-outline-primary btn-sm w-100 text-start"
                            data-bs-toggle="modal"
                            data-bs-target="#modalSettingAkun"
                        >
                            <i class="bi bi-gear-fill"></i>
                            <span 
                                id="labelAkunTerpilih"
                                class="fw-bold {{ $labelClass }} ms-1"
                                data-current-id="{{ $defaultAccountId }}"
                            >
                                {{ $accountLabel }}
                            </span>
                        </button>
                    </div>

                    {{-- action buttons --}}
                    <div class="col-12 col-lg-3">
                        <div class="d-grid gap-2">
                            <button 
                                type="button"
                                class="btn btn-success btn-sm shadow-sm"
                                id="btnExportCsv"
                                disabled
                            >
                                <i class="bi bi-file-earmark-spreadsheet"></i>
                                Export CSV (<span id="countSelected">0</span>)
                            </button>

                            @if (\App\Constants\Roles::hasFullFinanceAccess(session('role')))
                                <a href="{{ route('payrolls.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle"></i> Create New Payroll
                                </a>
                            @endif
                        </div>
                    </div>

                </div>

                {{-- hidden export form --}}
                <form id="formExportCsv" action="{{ route('payrolls.export-csv') }}" method="POST" class="d-none">
                    @csrf
                    <div id="hiddenCsvInputs"></div>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @php
            $now = \Carbon\Carbon::now('Asia/Jakarta');
            
            $curMonthName = $months[$now->month - 1];
            $curMonthVal = $now->month;
            $curYear = $now->year;

            $prevDate = $now->copy()->subMonth();
            $prevMonthName = $months[$prevDate->month - 1];
            $prevMonthVal = $prevDate->month;
            $prevYear = $prevDate->year;
        @endphp

        {{-- initial message --}}
        <div id="initial-message" class="card shadow-sm border-0 mb-4" style="background: linear-gradient(145deg, #ffffff, #f8f9fa); border-radius: 15px;">
            <div class="card-body text-center" style="padding: 5rem 2rem;">
                <div class="d-inline-block bg-primary bg-opacity-10 rounded-circle mb-4" style="padding: 1.5rem; border: 1px dashed #cfe2ff;">
                    <i class="bi bi-receipt-cutoff text-primary" style="font-size: 4rem; line-height: 1;"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">Waiting for Period Selection</h4>
                <p class="text-muted fs-6 mb-4" style="max-width: 400px; margin: 0 auto;">
                    Select a month in the filter above or use the quick shortcuts below to view data.
                </p>

                <div class="d-flex flex-column flex-md-row justify-content-center gap-2 gap-md-3 mt-3">
                    <button type="button" class="btn btn-primary px-4 py-2 rounded-pill btn-shortcut w-100 w-md-auto" 
                            data-month="{{ $curMonthVal }}" data-year="{{ $curYear }}">
                        <i class="bi bi-calendar-check-fill me-1"></i> This Month: {{ $curMonthName }}
                    </button>
                    <button type="button" class="btn btn-outline-primary px-4 py-2 rounded-pill btn-shortcut w-100 w-md-auto" 
                            data-month="{{ $prevMonthVal }}" data-year="{{ $prevYear }}">
                        <i class="bi bi-calendar-minus me-1"></i> Last Month: {{ $prevMonthName }}
                    </button>
                </div>
            </div>
        </div>

        {{-- table container --}}
        <div class="card shadow-sm border-0" id="table-card" style="display: none; border-radius: 15px;">
            <div class="card-body p-0">
                <div class="table-responsive p-4">
                    <table class="table align-middle custom-table" id="payroll-table" style="width: 100%;">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th class="text-center" width="50"><input type="checkbox" id="checkAll" class="form-check-input"></th>
                                <th class="text-secondary fw-bold" style="font-size: 0.75rem; border-bottom: 2px solid #e9ecef; padding: 1rem;">Employee</th>
                                <th class="text-secondary fw-bold" style="font-size: 0.75rem; border-bottom: 2px solid #e9ecef; padding: 1rem;">Period</th>
                                <th class="text-secondary fw-bold text-end" style="font-size: 0.75rem; border-bottom: 2px solid #e9ecef; padding: 1rem;">Earnings</th>
                                <th class="text-secondary fw-bold text-end" style="font-size: 0.75rem; border-bottom: 2px solid #e9ecef; padding: 1rem;">Deductions</th>
                                <th class="text-secondary fw-bold text-end" style="font-size: 0.75rem; border-bottom: 2px solid #e9ecef; padding: 1rem;">Net Salary</th>
                                <th class="text-secondary fw-bold text-center" style="font-size: 0.75rem; border-bottom: 2px solid #e9ecef; padding: 1rem;">Status</th>
                                <th class="text-secondary fw-bold text-center" style="font-size: 0.75rem; border-bottom: 2px solid #e9ecef; padding: 1rem;">Manage Status</th>
                                <th class="text-secondary fw-bold text-center" style="font-size: 0.75rem; border-bottom: 2px solid #e9ecef; padding: 1rem; width: 120px;">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    
    {{-- modal account setting --}}
    <div class="modal fade" id="modalSettingAkun" tabindex="-1" aria-labelledby="modalSettingAkunLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="modalSettingAkunLabel">Set Fund Source</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">The cash/bank account selected here will be used automatically to disburse payroll funds.</p>
                    <div class="mb-3">
                        <label for="master_account_id" class="form-label fw-bold">Select Cash/Bank Account (Asset) <span class="text-danger">*</span></label>
                        <select class="form-select" id="master_account_id" name="master_account_id">
                            <option value="" disabled {{ !$defaultAccountId ? 'selected' : '' }}>-- Select Cash/Bank Account --</option>
                            @foreach($assetAccounts as $akun)
                                <option value="{{ $akun->id }}" {{ $defaultAccountId == $akun->id ? 'selected' : '' }}>
                                    {{ $akun->code }} - {{ $akun->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnSimpanSetting">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    let table = null;
    let selectedIds = []; 

    const navEntries = performance.getEntriesByType("navigation");
    if (navEntries.length > 0 && navEntries[0].type === "reload") {
        sessionStorage.removeItem('payroll_pref_month');
        sessionStorage.removeItem('payroll_pref_year');
        sessionStorage.removeItem('payroll_pref_status');
    }

    function getSelectedMonthName() {
        return $('#filter-month option:selected').text();
    }

    function checkAndLoadData() {
        let month = $('#filter-month').val();
        let year = $('#filter-year').val();
        let status = $('#filter-status').val();

        if (month) {
            sessionStorage.setItem('payroll_pref_month', month);
            sessionStorage.setItem('payroll_pref_year', year);
            sessionStorage.setItem('payroll_pref_status', status);

            selectedIds = [];
            updateExportButton();
            $('#checkAll').prop('checked', false);

            $('#initial-message').hide();
            $('#table-card').show(); 

            if (!table) {
                table = $('#payroll-table').DataTable({
                    scrollX: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('payrolls.index') }}",
                        data: function(d) {
                            d.filter_month = $('#filter-month').val();
                            d.filter_year = $('#filter-year').val();
                            d.filter_status = $('#filter-status').val();
                        }
                    },
                    // ==========================================
                    // fix 1: shift index from 1 to 2 (sort period)
                    // ==========================================
                    order: [[2, 'desc']], 
                    columns: [
                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            className: 'text-center align-middle',
                            render: function(data, type, row) {
                                if (row.status === 'approved') {
                                    let checked = selectedIds.includes(data.toString()) ? 'checked' : '';
                                    return `<input type="checkbox" class="check-item form-check-input" value="${data}" ${checked}>`;
                                }
                                return `<input type="checkbox" class="form-check-input" disabled title="Only Approved data can be exported.">`;
                            }
                        },
                        // ==========================================
                        // fix 2: disable orderable on employee_name to prevent sql crash
                        // ==========================================
                        { data: 'employee_name', name: 'employee.fullname', orderable: false }, 
                        
                        { data: 'period', name: 'period_year', orderable: true, searchable: false },
                        { data: 'total_earnings', name: 'total_earnings', className: 'text-end' },
                        { data: 'total_deductions', name: 'total_deductions', className: 'text-end' },
                        { data: 'net_salary', name: 'net_salary', className: 'text-end fw-bold' },
                        { data: 'status_badge', name: 'status', className: 'text-center', orderable: true, searchable: false },
                        { data: 'status_actions', name: 'status_actions', orderable: false, searchable: false }, 
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                    ],
                    language: {
                        processing: '<div class="spinner-border text-primary spinner-border-sm" role="status"></div>',
                        emptyTable: 'No payroll data found for ' + getSelectedMonthName() + '.',
                        info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                        infoEmpty: 'No data available',
                        search: '<i class="bi bi-search"></i>',
                        searchPlaceholder: 'Search employee...',
                        paginate: { previous: '<i class="bi bi-chevron-left"></i>', next: '<i class="bi bi-chevron-right"></i>' }
                    }
                });

                setTimeout(function() { table.columns.adjust(); }, 10);
                
            } else {
                table.context[0].oLanguage.sEmptyTable = 'No payroll data found for ' + getSelectedMonthName() + '.';
                table.draw();
            }
        } else {
            $('#table-card').hide();
            $('#initial-message').fadeIn();
        }
    }

    // export csv logic 
    function updateExportButton() {
        let count = selectedIds.length;
        $('#countSelected').text(count);
        $('#btnExportCsv').prop('disabled', count === 0); 
    }

    $('#checkAll').on('change', function() {
        let isChecked = $(this).prop('checked');
        $('.check-item:not(:disabled)').prop('checked', isChecked);
        
        $('.check-item:not(:disabled)').each(function() {
            let val = $(this).val().toString();
            if (isChecked) {
                if (!selectedIds.includes(val)) selectedIds.push(val);
            } else {
                selectedIds = selectedIds.filter(id => id !== val);
            }
        });
        updateExportButton();
    });

    $(document).on('change', '.check-item', function() {
        let val = $(this).val().toString();
        
        if ($(this).prop('checked')) {
            if (!selectedIds.includes(val)) selectedIds.push(val);
        } else {
            selectedIds = selectedIds.filter(id => id !== val);
            $('#checkAll').prop('checked', false);
        }
        updateExportButton();
    });

    $('#btnExportCsv').click(function() {
        if (selectedIds.length === 0) return;
        
        let inputs = '';
        selectedIds.forEach(id => {
            inputs += `<input type="hidden" name="ids[]" value="${id}">`;
        });
        
        $('#hiddenCsvInputs').html(inputs);
        $('#formExportCsv').submit();
    });

    // save account setting logic
    $('#btnSimpanSetting').click(function() {
        let accountId = $('#master_account_id').val();

        if (!accountId) {
            Swal.fire('Oops!', 'Please select an account first!', 'warning');
            return;
        }

        let btn = $(this);
        let originalText = btn.text();
        btn.html('<span class="spinner-border spinner-border-sm"></span> Saving...').prop('disabled', true);

        $.ajax({
            url: `{{ route('payrolls.update-setting') }}`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                account_id: accountId
            },
            success: function(res) {
                btn.html(originalText).prop('disabled', false);
                if (res.success) {
                    $('#labelAkunTerpilih').removeClass('text-danger').addClass('text-success').text(res.account_name);
                    $('#labelAkunTerpilih').attr('data-current-id', accountId);
                    $('#modalSettingAkun').modal('hide');
                    Swal.fire('Saved!', 'Central fund source updated successfully.', 'success');
                }
            },
            error: function() {
                btn.html(originalText).prop('disabled', false);
                Swal.fire('Failed!', 'System error occurred.', 'error');
            }
        });
    });

    // update status logic
    $(document).on('click', '.btn-update-status', function() {
        let id = $(this).data('id');
        let status = $(this).data('status');

        if (status === 'paid') {
            let currentId = $('#labelAkunTerpilih').attr('data-current-id');
            let currentName = $('#labelAkunTerpilih').text();

            if (!currentId) {
                Swal.fire('Stop!', 'Central fund source not set. Please click the "Fund Source" button above the table first.', 'error');
                return;
            }

            Swal.fire({
                title: 'Payment Confirmation',
                html: `Salary will be disbursed and recorded to account:<br><strong class="text-success">${currentName}</strong>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Pay!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    prosesUpdateStatus(id, 'paid', currentId);
                }
            });
        } else {
            Swal.fire({
                title: 'Change Status?',
                text: "Payroll status will be changed to " + status + ".",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Change!'
            }).then((result) => {
                if (result.isConfirmed) {
                    prosesUpdateStatus(id, status, null); 
                }
            });
        }
    });

    function prosesUpdateStatus(id, status, account_id) {
        Swal.fire({
            title: 'Processing...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: `{{ url('payrolls') }}/${id}/update-status`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: status,
                account_id: account_id 
            },
            success: function(res) {
                if (res.success) {
                    $('#payroll-table').DataTable().ajax.reload(null, false); 
                    Swal.fire('Success!', res.message, 'success').then(() => {
                        
                        if (status === 'paid') {
                            Swal.fire({
                                title: 'Preparing PDF...',
                                text: 'Please wait, rendering layout...',
                                allowOutsideClick: false,
                                didOpen: () => { Swal.showLoading(); }
                            });

                            let iframe = document.createElement('iframe');
                            iframe.style.position = 'fixed';
                            iframe.style.top = '0'; iframe.style.left = '0';
                            iframe.style.width = '850px'; iframe.style.height = '100vh';
                            iframe.style.zIndex = '-9999'; iframe.style.opacity = '0.01';
                            document.body.appendChild(iframe);

                            window.addEventListener('message', function(event) {
                                if (event.data === 'pdf_selesai') {
                                    document.body.removeChild(iframe);
                                    Swal.close(); 
                                }
                            }, { once: true });

                            iframe.src = `{{ url('payrolls') }}/${id}/slip?auto_pdf=true`;
                        }
                    });
                }
            },
            error: function(err) {
                Swal.fire(
                    'Failed!',
                    err.responseJSON?.message || 'Failed to update payroll status.',
                    'error'
                );
            }
        });
    }

    // delete logic
    $(document).on('click', '.btn-delete-payroll', function() {
        const id = $(this).data('id');
        const status = $(this).data('status');
        
        let config = {
            title: 'Are you sure?',
            text: "This payroll data will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete!'
        };

        if (status === 'paid') {
            config.title = 'Critical Warning!';
            config.text = "This payroll is already marked as Paid. Deleting this data will automatically remove the related cash book transaction, which may affect your cashflow report and account balance.";
            config.icon = 'error';
            config.confirmButtonText = 'I Understand, Delete Anyway';
        }

        Swal.fire(config).then((result) => {
            if (result.isConfirmed) {
                $(`#form-delete-${id}`).submit();
            }
        });
    });

    // page load inits
    const savedMonth = sessionStorage.getItem('payroll_pref_month');
    const savedYear = sessionStorage.getItem('payroll_pref_year');
    const savedStatus = sessionStorage.getItem('payroll_pref_status');

    if (savedMonth) {
        $('#filter-month').val(savedMonth);
        $('#filter-year').val(savedYear);
        $('#filter-status').val(savedStatus);
        checkAndLoadData();
    }

    $('#filter-month, #filter-year, #filter-status').on('change', function() {
        checkAndLoadData();
    });

    $(document).on('click', '.btn-shortcut', function() {
        $('#filter-month').val($(this).data('month'));
        $('#filter-year').val($(this).data('year'));
        checkAndLoadData();
    });
});
</script>
@endpush
@endsection