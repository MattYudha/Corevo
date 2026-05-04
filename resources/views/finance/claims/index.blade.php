@extends('layouts.dashboard')

@section('title', 'Daftar Klaim Biaya')

@push('styles')
<style>
.fin-page-hero {
    background: linear-gradient(135deg,#1a1f3c 0%,#2d3561 60%,#1e3a5f 100%);
    border-radius: 16px; padding: 1.4rem 1.75rem;
    margin-bottom: 1.25rem; position: relative; overflow: hidden;
}
.fin-page-hero::after {
    content:''; position:absolute; top:-40%; right:-5%; width:280px; height:280px;
    border-radius:50%; background:rgba(255,255,255,.04); pointer-events:none;
}
.fin-page-hero .ph-title { font-size:1.1rem; font-weight:800; color:#fff; margin:0; }
.fin-page-hero .ph-sub   { font-size:.75rem; color:rgba(255,255,255,.5); margin:.2rem 0 0; }

.stat-pill {
    background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.15);
    border-radius:10px; padding:.6rem 1.1rem; text-align:center; min-width:105px;
}
.stat-pill .sp-val { font-size:1.05rem; font-weight:800; color:#fff; line-height:1; }
.stat-pill .sp-lbl { font-size:.62rem; color:rgba(255,255,255,.55); text-transform:uppercase; letter-spacing:.05em; margin-top:.2rem; }

.claim-badge {
    border-radius:6px; padding:.22rem .65rem; font-size:.68rem; font-weight:800; letter-spacing:.04em; text-transform:uppercase;
}
.badge-pending  { background:#fff4de; color:#d48a00; }
.badge-approved { background:#e2faf0; color:#1aae6f; }
.badge-rejected { background:#fce8e8; color:#f5365c; }

.fin-tbl { border-collapse:separate; border-spacing:0; width:100%; }
.fin-tbl thead tr { background:#f4f6fb; }
.fin-tbl thead th {
    font-size:.65rem; font-weight:800; letter-spacing:.09em; text-transform:uppercase;
    color:#8392ab; padding:.85rem 1rem; border-bottom:2px solid #edf0f7; white-space:nowrap;
}
.fin-tbl tbody td { padding:.85rem 1rem; border-bottom:1px solid #f1f3f7; vertical-align:middle; }
</style>
@endpush
@include('finance._finance_mobile')

@section('content')

<div class="fin-page-hero shadow">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
            <p class="ph-title">🧾 Klaim Biaya & Reimbursement</p>
            <p class="ph-sub">Pengajuan klaim operasional dan pengembalian dana biaya kerja</p>
            <div class="mt-3">
                <a href="{{ route('finance.claims.create') }}" class="btn btn-sm fw-bold mb-0"
                   style="background:#fff;color:#1a1f3c;border-radius:8px;font-size:.78rem;padding:.4rem 1rem">
                    <i class="bi bi-plus-lg me-1"></i>Ajukan Klaim Baru
                </a>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <div class="stat-pill">
                <div class="sp-val" style="color:#ffd580">{{ $stats['pending'] }}</div>
                <div class="sp-lbl">Menunggu</div>
            </div>
            <div class="stat-pill">
                <div class="sp-val" style="color:#a0efcc">Rp {{ number_format($stats['total_approved_amount']/1e6,1) }}jt</div>
                <div class="sp-lbl">Disetujui</div>
            </div>
            <div class="stat-pill">
                <div class="sp-val" style="color:#f5bfc8">{{ $stats['rejected'] }}</div>
                <div class="sp-lbl">Ditolak</div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert mb-3 d-flex align-items-center gap-2 text-white py-2" style="background:#1aae6f;border-radius:10px;font-size:.84rem">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
@endif

<div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden">
    <div class="table-responsive">
        <table class="fin-tbl">
            <thead>
                <tr>
                    <th class="d-none d-md-table-cell" style="width:110px">ID #</th>
                    @if($isAdmin) <th>Karyawan</th> @endif
                    <th>Judul Klaim</th>
                    <th class="d-none d-md-table-cell">Kategori</th>
                    <th class="text-end">Nominal</th>
                    <th class="text-center">Status</th>
                    <th class="d-none d-md-table-cell">Tanggal</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($query as $claim)
                <tr>
                    <td class="d-none d-md-table-cell"><span class="fw-bold text-xs" style="color:#8392ab">#{{ $claim->id }}</span></td>
                    @if($isAdmin)
                    <td>
                        <p class="fw-bold mb-0 text-sm" style="color:#344767">{{ $claim->employee?->fullname ?? 'Admin/Unknown' }}</p>
                        <p class="text-xs text-muted mb-0">{{ $claim->employee?->nik ?? '-' }}</p>
                    </td>
                    @endif
                    <td>
                        <p class="fw-semibold mb-0 text-sm" style="color:#344767">{{ $claim->title }}</p>
                        <p class="text-xs text-muted mb-0">{{ Str::limit($claim->description, 40) }}</p>
                    </td>
                    <td class="d-none d-md-table-cell"><span class="text-xs fw-bold" style="color:#5e72e4">{{ $claim->categoryLabel() }}</span></td>
                    <td class="text-end fw-bold text-sm" style="color:#344767">Rp {{ number_format($claim->amount,0,',','.') }}</td>
                    <td class="text-center">
                        <span class="claim-badge badge-{{ $claim->status }}">{{ $claim->statusLabel() }}</span>
                    </td>
                    <td class="d-none d-md-table-cell">
                        <p class="text-xs mb-0" style="color:#8392ab">{{ $claim->created_at->format('d/m/Y') }}</p>
                        <p class="text-xs text-muted mb-0">{{ $claim->created_at->diffForHumans() }}</p>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('finance.claims.show', $claim->id) }}" class="btn btn-xs btn-outline-primary mb-0" style="font-size:.65rem">Detail</a>
                        @if($claim->status === 'pending' || $claim->status === 'draft')
                            <a href="{{ route('finance.claims.edit', $claim->id) }}" class="btn btn-xs btn-outline-warning mb-0" style="font-size:.65rem" title="Edit"><i class="bi bi-pencil"></i></a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $isAdmin ? 8 : 7 }}" class="text-center py-5">
                        <div style="font-size:2.5rem;opacity:.15">🧾</div>
                        <p class="text-muted text-xs mt-3">Belum ada pengajuan klaim.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($query->hasPages())
    <div class="px-3 py-3 border-top">
        {{ $query->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>
@endsection
