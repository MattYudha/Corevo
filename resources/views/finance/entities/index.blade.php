@extends('layouts.dashboard')

@section('title', 'Master Entitas Keuangan')

@push('styles')
<style>
/* ── Shared Finance Module ──────────────────────────── */
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

/* Stat pills in hero */
.stat-pill {
    background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.15);
    border-radius:10px; padding:.6rem 1.1rem; text-align:center; min-width:90px;
}
.stat-pill .sp-val { font-size:1.2rem; font-weight:800; color:#fff; line-height:1; }
.stat-pill .sp-lbl { font-size:.62rem; color:rgba(255,255,255,.55); text-transform:uppercase; letter-spacing:.05em; margin-top:.2rem; }

/* Filter toolbar */
.filter-bar {
    background:#f8f9fc; border-radius:12px; border:1.5px solid #edf0f7;
    padding:.75rem 1rem; margin-bottom:1rem;
    display:flex; flex-wrap:wrap; gap:.6rem; align-items:center;
}
.filter-bar .form-control, .filter-bar .form-select {
    border-radius:8px; border:1.5px solid #e4e8f0; font-size:.82rem;
    background:#fff; transition:border-color .15s;
}
.filter-bar .form-control:focus, .filter-bar .form-select:focus {
    border-color:#5e72e4; box-shadow:0 0 0 3px rgba(94,114,228,.1);
}

/* Premium Table */
.fin-tbl { border-collapse:separate; border-spacing:0; width:100%; }
.fin-tbl thead tr { background:#f4f6fb; }
.fin-tbl thead th {
    font-size:.65rem; font-weight:800; letter-spacing:.09em; text-transform:uppercase;
    color:#8392ab; padding:.85rem 1.1rem; border-bottom:2px solid #edf0f7;
    white-space:nowrap;
}
.fin-tbl tbody tr { transition:background .12s; }
.fin-tbl tbody tr:hover { background:#f7f9ff; }
.fin-tbl tbody td { padding:.85rem 1.1rem; border-bottom:1px solid #f1f3f7; vertical-align:middle; }
.fin-tbl tbody tr:last-child td { border-bottom:none; }

/* Type badges */
.ent-badge {
    display:inline-flex; align-items:center; gap:.3rem;
    border-radius:7px; padding:.25rem .7rem;
    font-size:.7rem; font-weight:700; letter-spacing:.04em; white-space:nowrap;
}
.ent-bank     { background:#dff0fb; color:#1171ef; }
.ent-vendor   { background:#fff4de; color:#d48a00; }
.ent-internal { background:#e2faf0; color:#1aae6f; }
.ent-client   { background:#f0e6ff; color:#8965e0; }
.ent-employee { background:#ffe8f0; color:#f3187d; }
.ent-tax_office { background:#f0f0f0; color:#525f7f; }
.ent-other    { background:#f4f5f7; color:#8898aa;  }

/* Action btns */
.act-btn {
    width:30px; height:30px; border-radius:8px; border:none;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:.78rem; transition:all .15s; cursor:pointer; text-decoration:none;
}
.act-edit   { background:#eef0ff; color:#5e72e4; }
.act-edit:hover   { background:#5e72e4; color:#fff; }
.act-delete { background:#fce8e8; color:#f5365c; }
.act-delete:hover { background:#f5365c; color:#fff; }
</style>
@endpush
@include('finance._finance_mobile')

@section('content')

{{-- ── Hero ─────────────────────────────────────── --}}
@php
    use App\Models\FinancialEntity;
    use Illuminate\Support\Str;
    $countByType   = FinancialEntity::groupBy('type')->selectRaw('type, count(*) as cnt')->pluck('cnt','type');
    $totalEntities = FinancialEntity::count();
@endphp

<div class="fin-page-hero shadow">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
            <p class="ph-title">🏢 Master Entitas Keuangan</p>
            <p class="ph-sub">Kelola daftar vendor, bank, dan entitas keuangan organisasi</p>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <a href="{{ route('finance.entities.create') }}" class="btn btn-sm text-dark fw-bold mb-0"
                   style="background:#fff;border-radius:8px;font-size:.78rem;padding:.4rem 1rem">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Entitas
                </a>
                <a href="{{ route('finance.transactions.index') }}" class="btn btn-sm mb-0"
                   style="background:rgba(255,255,255,.15);color:#fff;border-radius:8px;font-size:.78rem;padding:.4rem 1rem;border:1px solid rgba(255,255,255,.25)">
                    ↗ Lihat Transaksi
                </a>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <div class="stat-pill">
                <div class="sp-val">{{ $totalEntities }}</div>
                <div class="sp-lbl">Total</div>
            </div>
            <div class="stat-pill">
                <div class="sp-val" style="color:#a8d8f0">{{ $countByType['bank'] ?? 0 }}</div>
                <div class="sp-lbl">Bank</div>
            </div>
            <div class="stat-pill">
                <div class="sp-val" style="color:#ffd580">{{ $countByType['vendor'] ?? 0 }}</div>
                <div class="sp-lbl">Vendor</div>
            </div>
            <div class="stat-pill">
                <div class="sp-val" style="color:#a0efcc">{{ $countByType['internal'] ?? 0 }}</div>
                <div class="sp-lbl">Internal</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Alerts ───────────────────────────────────── --}}
@if(session('success'))
    <div class="alert mb-3 d-flex align-items-center gap-2 text-white py-2" style="background:#1aae6f;border-radius:10px;font-size:.84rem">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert mb-3 d-flex align-items-center gap-2 text-white py-2" style="background:#f5365c;border-radius:10px;font-size:.84rem">
        <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
    </div>
@endif

{{-- ── Filter Bar ───────────────────────────────── --}}
<form method="GET" action="{{ route('finance.entities.index') }}">
<div class="filter-bar">
    <div class="input-group">
        <span class="input-group-text" style="background:#fff;border:1.5px solid #e4e8f0;border-radius:8px 0 0 8px;border-right:0">
            <i class="bi bi-search" style="color:#8392ab;font-size:.8rem"></i>
        </span>
        <input type="text" name="search" class="form-control" style="border-left:0;border-radius:0 8px 8px 0"
               placeholder="Cari nama atau tipe..." value="{{ request('search') }}">
    </div>
    <select name="type" class="form-select" onchange="this.form.submit()">
        <option value="">🏷 Semua Tipe</option>
        @foreach(['bank','vendor','internal','client','other'] as $t)
            <option value="{{ $t }}" {{ request('type') == $t ? 'selected':'' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
        @endforeach
    </select>
    <div class="filter-actions">
        <button type="submit" class="btn btn-dark btn-sm mb-0" style="border-radius:8px">Cari</button>
        @if(request('search') || request('type'))
            <a href="{{ route('finance.entities.index') }}" class="btn btn-outline-secondary btn-sm mb-0" style="border-radius:8px">Reset</a>
        @endif
    </div>
    <span class="ms-auto text-xs text-muted">{{ $entities->total() }} entitas ditemukan</span>
</div>
</form>

{{-- ── Table ────────────────────────────────────── --}}
<div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden">
    <div class="table-responsive">
        <table class="fin-tbl">
            <thead>
                <tr>
                    <th style="width:130px">Tipe</th>
                    <th>Nama Entitas</th>
                    <th class="d-none d-md-table-cell">Deskripsi</th>
                    <th style="width:90px;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entities as $entity)
                @php
                    $typeIcons = ['bank'=>'🏦','vendor'=>'🏪','internal'=>'🏢','client'=>'🤝','employee'=>'👤','tax_office'=>'🏛️','other'=>'📌'];
                @endphp
                <tr>
                    <td>
                        <span class="ent-badge ent-{{ $entity->type }}">
                            {{ $typeIcons[$entity->type] ?? '📌' }}
                            {{ str_replace('_',' ', ucfirst($entity->type)) }}
                        </span>
                    </td>
                    <td>
                        <p class="fw-bold mb-0 text-sm" style="color:#344767">{{ $entity->name }}</p>
                        <p class="text-xs text-muted mb-0">#{{ $entity->id }} · Dibuat {{ $entity->created_at->diffForHumans() }}</p>
                    </td>
                    <td class="d-none d-md-table-cell">
                        <p class="text-sm text-muted mb-0" style="line-height:1.5">
                            {{ $entity->description ? \Illuminate\Support\Str::limit($entity->description, 70) : '—' }}
                        </p>
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="{{ route('finance.entities.edit', $entity->id) }}" class="act-btn act-edit" title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            @php
                                $user = Auth::user();
                                $sessionRole = session('role');
                                $isMasterAdmin = ($user->isMasterAdmin() || $sessionRole === \App\Constants\Roles::MASTER_ADMIN);
                                $isFinance = ($user->isFinance() || $sessionRole === \App\Constants\Roles::FINANCE);
                            @endphp
                            @if($isMasterAdmin || $isFinance)
                            <form action="{{ route('finance.entities.destroy', $entity->id) }}" method="POST"
                                  onsubmit="return confirm('Hapus entitas \'{{ addslashes($entity->name) }}\'?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="act-btn act-delete" title="Hapus">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">
                        <div class="text-center py-5">
                            <div style="font-size:3rem;opacity:.15;line-height:1">🏢</div>
                            <p class="fw-bold mt-3 mb-1" style="color:#344767">Belum ada entitas</p>
                            <p class="text-muted text-xs mb-3">Tambahkan bank, vendor, atau entitas internal untuk mencatat transaksi.</p>
                            <a href="{{ route('finance.entities.create') }}" class="btn btn-primary btn-sm" style="border-radius:8px">
                                <i class="bi bi-plus-lg me-1"></i>Tambah Entitas Pertama
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($entities->hasPages())
    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top" style="background:#fafbff">
        <p class="text-xs text-muted mb-0">
            Menampilkan <strong>{{ $entities->firstItem() }}–{{ $entities->lastItem() }}</strong> dari <strong>{{ $entities->total() }}</strong> entitas
        </p>
        {{ $entities->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>
@endsection
