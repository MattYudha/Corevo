@extends('layouts.dashboard')

@section('content')
{{-- Memanggil CSS DataTables khusus untuk Bootstrap 5 --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Daftar Lembur</h3>
                <p class="text-subtitle text-muted">Kelola dan pantau jam lembur karyawan secara terpusat.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dasbor</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Lembur</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4 class="card-title mb-0">Tabel Lembur</h4>
            <div class="d-flex flex-wrap gap-2">
                {{-- Tombol Tindakan Admin / HR --}}
                @if(Auth::user()->isMasterAdmin() || session('role') == 'HR Administrator')
                    <button id="btn-approve-batch" class="btn btn-success" style="display:none;" onclick="processBatch('approve')">
                        <i class="bi bi-check-all"></i> Setujui Sekaligus
                    </button>
                    <button id="btn-reject-batch" class="btn btn-danger" style="display:none;" onclick="processBatch('reject')">
                        <i class="bi bi-x-circle"></i> Tolak Sekaligus
                    </button>
                    
                    <button class="btn btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalSettings">
                        <i class="bi bi-cash"></i> Tarif Lembur
                    </button>
                @endif
                
                <a href="{{ route('overtimes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Ajukan Lembur
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle" id="table-overtime" style="width:100%">
                    <thead>
                        <tr>
                            @if(Auth::user()->isMasterAdmin() || session('role') == 'HR Administrator') 
                                <th width="30px" class="text-center"><input type="checkbox" id="select-all" class="form-check-input"></th> 
                            @endif
                            <th>Tanggal</th>
                            <th>Karyawan</th>
                            <th>Durasi</th>
                            <th>Status</th>
                            <th class="text-center" width="200px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submissions as $item)
                        <tr>
                            @if(Auth::user()->isMasterAdmin() || session('role') == 'HR Administrator')
                                <td class="text-center">
                                    @if($item->status == 'pending') 
                                        <input type="checkbox" class="form-check-input ot-checkbox" value="{{ $item->id }}"> 
                                    @endif
                                </td>
                            @endif
                            <td>{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</td>
                            <td>{{ $item->employee ? $item->employee->fullname : 'Tidak Diketahui' }}</td>
                            <td>{{ round($item->duration_minutes / 60, 2) }} Jam</td>
                            <td>
                                @if($item->status == 'pending') <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($item->status == 'approved') <span class="badge bg-success">Approved</span>
                                @else <span class="badge bg-danger">Rejected</span> @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    @if($item->status == 'pending' && (Auth::user()->isMasterAdmin() || session('role') == 'HR Administrator'))
                                        <button class="btn btn-outline-success" title="Setujui" onclick="processSingle({{ $item->id }}, 'approve')"><i class="bi bi-check-lg"></i></button>
                                        <button class="btn btn-outline-danger" title="Tolak" onclick="processSingle({{ $item->id }}, 'reject')"><i class="bi bi-x-lg"></i></button>
                                    @endif

                                    <a href="{{ route('overtimes.show', $item->id) }}" class="btn btn-outline-info" title="Detail"><i class="bi bi-eye"></i></a>
                                    
                                    @if($item->status == 'pending')
                                        <a href="{{ route('overtimes.edit', $item->id) }}" class="btn btn-outline-primary" title="Ubah"><i class="bi bi-pencil"></i></a>
                                    @endif

                                    @if(in_array($item->status, ['pending', 'rejected']) || (Auth::user()->isMasterAdmin() && $item->status == 'approved'))
                                        <form action="{{ route('overtimes.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data lembur ini?')" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

{{-- MODAL PENGATURAN TARIF LEMBUR --}}
@if(Auth::user()->isMasterAdmin() || session('role') == 'HR Administrator')
<div class="modal fade" id="modalSettings" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title text-white"><i class="bi bi-cash-stack me-2"></i> Pengaturan Uang Lembur</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('overtimes.settings') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-0">
                        <label class="form-label fw-bold">Tarif Lembur per Jam</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="overtime_rate_per_hour" class="form-control form-control-lg" value="{{ \App\Models\Setting::getValue('overtime_rate_per_hour', 0) }}" required min="0">
                        </div>
                        <small class="text-muted mt-2 d-block">Sistem payroll akan otomatis mengalikan nominal ini dengan total jam lembur yang <b>disetujui</b>.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-info text-white">Simpan Tarif</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Memanggil Plugin jQuery & DataTables --}}
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    let overtimeTable;

    $(document).ready(function() {
        // Inisialisasi DataTables yang aman dari error
        overtimeTable = $('#table-overtime').DataTable({
            "columnDefs": [
                // Menggunakan target -1 agar selalu menunjuk ke kolom paling terakhir (Aksi), 
                // tidak peduli ada berapa jumlah kolomnya.
                { "orderable": false, "targets": [0, -1] } 
            ]
        });

        // Fitur Select All - Bisa mendeteksi semua halaman Datatable
        $('#select-all').on('click', function(){
            var rows = overtimeTable.rows({ 'search': 'applied' }).nodes();
            $('input[type="checkbox"]', rows).prop('checked', this.checked);
            toggleActionBtns();
        });

        // Event listener saat checkbox satuan dicentang
        $('#table-overtime tbody').on('change', 'input[type="checkbox"]', function(){
            if(!this.checked){
                var el = $('#select-all').get(0);
                if(el && el.checked && ('indeterminate' in el)){
                    el.indeterminate = true;
                }
            }
            toggleActionBtns();
        });
    });

    // Fungsi untuk memunculkan / menyembunyikan tombol Setuju/Tolak Masal
    function toggleActionBtns() {
        var checkedCount = overtimeTable.$('.ot-checkbox:checked').length;
        
        if($('#btn-approve-batch').length && $('#btn-reject-batch').length) {
            if(checkedCount > 0) {
                $('#btn-approve-batch').fadeIn(150).css('display', 'inline-block');
                $('#btn-reject-batch').fadeIn(150).css('display', 'inline-block');
            } else {
                $('#btn-approve-batch').fadeOut(150);
                $('#btn-reject-batch').fadeOut(150);
            }
        }
    }

    // Fungsi untuk proses massal
    function processBatch(action) {
        const actionText = action === 'approve' ? 'menyetujui' : 'menolak';
        if(!confirm(`Yakin ingin ${actionText} semua pengajuan lembur yang dipilih?`)) return;
        
        const ids = [];
        overtimeTable.$('.ot-checkbox:checked').each(function() {
            ids.push($(this).val());
        });

        sendProcessRequest(action, ids);
    }

    // Fungsi untuk proses 1 baris tombol satuan
    function processSingle(id, action) {
        const actionText = action === 'approve' ? 'menyetujui' : 'menolak';
        if(!confirm(`Yakin ingin ${actionText} pengajuan lembur ini?`)) return;
        
        sendProcessRequest(action, [id]);
    }

    // Proses kirim ke server
    function sendProcessRequest(action, ids) {
        if(ids.length === 0) return alert("Belum ada data yang dipilih!");

        const route = action === 'approve' ? '{{ route('overtimes.approve-batch') }}' : '{{ route('overtimes.reject-batch') }}';
        
        fetch(route, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                window.location.reload();
            } else {
                alert('Gagal memproses data.');
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan server.');
        });
    }
</script>
@endsection