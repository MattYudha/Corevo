@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>My Activity</h3>
                <p class="text-subtitle text-muted">Daftar rekaman aktivitas harian</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">My Activity</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h4 class="card-title mb-0">Daftar Aktivitas</h4>
            
            <div class="d-flex flex-wrap align-items-center gap-2">
                {{-- Filter Karyawan Khusus Master Admin --}}
                @if(Auth::user()->isMasterAdmin())
                <div class="form-group mb-0">
                    <select id="employee_filter" class="form-select">
                        <option value="">-- All Employees --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->fullname }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <a href="{{ route('work-logs.create') }}" class="btn btn-primary d-flex align-items-center">
                    <i class="bi bi-plus-circle me-2 mb-2"></i> Tambah Log
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped w-100" id="worklog-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Karyawan</th>
                            <th>Aktivitas</th>
                            <th>Tugas (Task)</th>
                            <th width="100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#worklog-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ route('work-logs.index') }}",
                data: function (d) {
                    d.employee_filter = $('#employee_filter').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'log_date', name: 'log_date' },
                { data: 'employee_name', name: 'employee.fullname' },
                { data: 'description', name: 'description' },
                { data: 'task_info', name: 'task_info' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"
            }
        });

        // Memuat ulang tabel saat filter karyawan diubah
        $('#employee_filter').on('change', function() {
            table.draw();
        });
    });
</script>
@endpush