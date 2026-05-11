@extends('layouts.dashboard')

@section('title', 'Buku Kas — Ledger Transaksi')

@push('styles')
<style>
/* ══════════════════════════════════════════════════════════
   BUKU KAS — Clean Professional Ledger
   ══════════════════════════════════════════════════════════ */
:root {
    --t-navy:   #1b2a4a;
    --t-slate:  #3d4e6c;
    --t-muted:  #7486a4;
    --t-border: #e2e7f0;
    --t-soft:   #f7f9fc;
    --t-white:  #ffffff;
    --t-brand:  #1e3a5f;
}

/* Page header */
.t-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 1.25rem;
    border-bottom: 1px solid var(--t-border);
    margin-bottom: 1.25rem;
    gap: 1.25rem;
}
.t-page-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--t-navy);
    margin: 0;
    letter-spacing: -.02em;
}
.t-page-sub {
    font-size: .75rem;
    color: var(--t-muted);
    margin: .15rem 0 0;
}

/* Stat cards row */
.t-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: .75rem;
    margin-bottom: 1.25rem;
}
.t-stat-card {
    background: var(--t-white);
    border: 1px solid var(--t-border);
    border-radius: 10px;
    padding: .9rem 1.1rem;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
}
.t-stat-card .sc-label {
    font-size: .65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: var(--t-muted);
    margin-bottom: .3rem;
}
.t-stat-card .sc-val {
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--t-navy);
    letter-spacing: -.03em;
    line-height: 1.1;
}
.t-stat-card .sc-val.val-debit  { color: #059669; }
.t-stat-card .sc-val.val-kredit { color: #dc2626; }
.t-stat-card .sc-val.val-neg    { color: #dc2626; }
.t-stat-card .sc-sub {
    font-size: .68rem;
    color: var(--t-muted);
    margin-top: .2rem;
}

/* Action buttons */
.t-btn {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .45rem 1rem;
    font-size: .78rem;
    font-weight: 600;
    border-radius: 7px;
    border: 1px solid transparent;
    text-decoration: none;
    transition: all .15s;
    white-space: nowrap;
    cursor: pointer;
    font-family: inherit;
    line-height: 1;
}
.t-btn-primary { background: var(--t-brand); border-color: var(--t-brand); color: #fff; }
.t-btn-primary:hover { background: #142840; color: #fff; }
.t-btn-ghost { background: var(--t-white); border-color: var(--t-border); color: var(--t-slate); }
.t-btn-ghost:hover { background: var(--t-soft); color: var(--t-navy); }

/* Alert */
.t-alert {
    display: flex; align-items: center; gap: .5rem;
    padding: .65rem 1rem; border-radius: 8px;
    font-size: .8rem; margin-bottom: 1rem;
}
.t-alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
.t-alert-error   { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

/* Filter bar */
.t-filter {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    align-items: end;
    gap: .75rem;
    background: var(--t-white);
    border: 1px solid var(--t-border);
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1.25rem;
    box-shadow: 0 1px 4px rgba(0,0,0,.03);
}
.t-filter-group {
    display: flex;
    flex-direction: column;
    gap: .35rem;
}
.t-filter-label {
    font-size: .65rem;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--t-muted);
    letter-spacing: .05em;
}
.t-filter .fc-input, .t-filter .fc-select {
    height: 34px;
    padding: 0 .75rem;
    font-size: .8rem;
    border: 1px solid var(--t-border);
    border-radius: 7px;
    background: var(--t-soft);
    color: var(--t-navy);
    outline: none;
    font-family: inherit;
    transition: border-color .15s, box-shadow .15s;
    appearance: none; -webkit-appearance: none;
}
.t-filter .fc-input:focus, .t-filter .fc-select:focus {
    border-color: var(--t-brand);
    box-shadow: 0 0 0 3px rgba(30,58,95,.1);
    background: var(--t-white);
}
.t-filter .fc-select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' fill='%237486a4' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right .6rem center;
    padding-right: 2rem;
    cursor: pointer;
}
.t-filter-search {
    display: flex;
    align-items: center;
    flex: 1;
    min-width: 160px;
    border: 1px solid var(--t-border);
    border-radius: 7px;
    background: var(--t-soft);
    overflow: hidden;
    transition: border-color .15s, box-shadow .15s;
}
.t-filter-search:focus-within {
    border-color: var(--t-brand);
    box-shadow: 0 0 0 3px rgba(30,58,95,.1);
    background: var(--t-white);
}
.t-filter-search i { padding: 0 .55rem; color: var(--t-muted); font-size: .8rem; }
.t-filter-search input {
    flex: 1; border: none; background: transparent;
    padding: 0 .5rem 0 0; font-size: .8rem; color: var(--t-navy);
    outline: none; height: 34px; font-family: inherit;
}
.t-filter-count {
    margin-left: auto;
    font-size: .72rem;
    color: var(--t-muted);
    white-space: nowrap;
}

/* Table card */
.t-card {
    background: var(--t-white);
    border: 1px solid var(--t-border);
    border-radius: 12px;
    box-shadow: 0 1px 6px rgba(0,0,0,.04);
    overflow: hidden;
}

/* Table */
.t-table {
    width: 100%;
    border-collapse: collapse;
}
.t-table thead th {
    font-size: .64rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--t-muted);
    padding: .75rem 1rem;
    border-bottom: 1px solid var(--t-border);
    background: #fafbfd;
    white-space: nowrap;
}
.t-table tbody td {
    padding: .8rem 1rem;
    border-bottom: 1px solid #f1f4f9;
    vertical-align: middle;
    font-size: .84rem;
    color: var(--t-slate);
}
.t-table tbody tr:last-child td { border-bottom: none; }
.t-table tbody tr:hover { background: #f9fafc; }

/* Period marker rows */
.row-eom td { background: #f8faff !important; }
.row-eom td:first-child { border-left: 2px solid #93acd1; }
.row-eoy td { background: #f5f6f8 !important; }
.row-eoy td:first-child { border-left: 2px solid var(--t-navy); }

/* Type badge — monochrome */
.type-badge {
    display: inline-block;
    padding: .18rem .55rem;
    border-radius: 5px;
    font-size: .62rem;
    font-weight: 700;
    letter-spacing: .05em;
    line-height: 1;
}
.type-debit  { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
.type-kredit { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
.period-tag {
    display: inline-block;
    padding: .14rem .45rem;
    border-radius: 4px;
    font-size: .58rem;
    font-weight: 700;
    letter-spacing: .05em;
    background: #eef2f8;
    color: var(--t-muted);
    border: 1px solid var(--t-border);
}

/* Amount cells */
.amt-debit  { font-weight: 700; color: #166534; font-variant-numeric: tabular-nums; }
.amt-kredit { font-weight: 700; color: #991b1b; font-variant-numeric: tabular-nums; }
.amt-running {
    font-weight: 700;
    color: var(--t-navy);
    font-variant-numeric: tabular-nums;
}
.amt-running.neg { color: #991b1b; }
.amt-blank { color: #d1d9e6; }

/* CoA code */
.coa-code {
    display: inline-block;
    background: var(--t-soft);
    border: 1px solid var(--t-border);
    border-radius: 5px;
    padding: .12rem .45rem;
    font-size: .7rem;
    font-weight: 600;
    font-family: 'Courier New', monospace;
    color: var(--t-slate);
}

/* Tax tag */
.tax-tag {
    display: inline-block;
    background: #fefce8;
    border: 1px solid #fde68a;
    border-radius: 4px;
    padding: .1rem .4rem;
    font-size: .58rem;
    font-weight: 700;
    color: #92400e;
    letter-spacing: .04em;
}

/* PPh auto-generated tag */
.pph-auto-tag {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 4px;
    padding: .1rem .45rem;
    font-size: .58rem;
    font-weight: 700;
    color: #1d4ed8;
    letter-spacing: .03em;
    margin-top: .2rem;
}

/* Auto-PPh row styling */
.row-auto-pph td {
    background: #f8faff !important;
    opacity: .92;
}
.row-auto-pph td:first-child {
    border-left: 2px solid #93c5fd;
}

/* Entity text */
.entity-from { font-size: .75rem; color: var(--t-muted); }
.entity-from strong { color: var(--t-slate); font-weight: 600; }

/* Action buttons */
.act-btn {
    width: 26px; height: 26px;
    border-radius: 6px; border: 1px solid var(--t-border);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .7rem; transition: all .15s; cursor: pointer;
    text-decoration: none; background: var(--t-white); color: var(--t-muted);
}
.act-btn:hover { background: var(--t-soft); color: var(--t-navy); border-color: #c5cfe0; }
.act-btn.act-delete:hover { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
.act-btn.act-edit:hover { background: #f0f4ff; color: #3451b2; border-color: #c7d2fe; }

/* Empty state */
.t-empty {
    text-align: center;
    padding: 4rem 1rem;
}
.t-empty-icon { font-size: 2.5rem; opacity: .15; line-height: 1; margin-bottom: .75rem; }
.t-empty-title { font-size: .95rem; font-weight: 700; color: var(--t-navy); margin-bottom: .35rem; }
.t-empty-sub { font-size: .8rem; color: var(--t-muted); margin-bottom: 1.25rem; }

/* Pagination bar */
.t-pager {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .75rem 1.25rem;
    border-top: 1px solid var(--t-border);
    background: #fafbfd;
    font-size: .75rem;
    color: var(--t-muted);
}

@media (max-width: 1199px) {
    .t-stats { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 767px) {
    .t-page-header { flex-direction: column; align-items: stretch; text-align: center; }
    .t-header-actions { justify-content: center; }
    .t-filter { grid-template-columns: 1fr; }
    .t-filter-search { min-width: 100%; order: -1; }
    .t-filter-count { text-align: center; width: 100%; margin: 0; }
}
@media (max-width: 480px) {
    .t-stats { grid-template-columns: 1fr; }
    .t-header-actions { flex-direction: column; }
    .t-header-actions .t-btn { width: 100%; justify-content: center; }
}
</style>
@endpush

@section('content')

@php
    $saldo      = $totalDebit - $totalKredit;
    $countTotal = \App\Models\FinancialTransaction::count();
@endphp

{{-- ── Page Header ────────────────────────────────────── --}}
<div class="t-page-header">
    <div>
        <h1 class="t-page-title">Buku Kas &amp; Keuangan</h1>
        <p class="t-page-sub">Ledger transaksi masuk &amp; keluar secara kronologis</p>
    </div>
    <div class="t-header-actions d-flex align-items-center gap-2">
        <a href="{{ route('finance.reports.index') }}" class="t-btn t-btn-ghost">
            <i class="bi bi-file-earmark-text"></i> Laporan
        </a>
        <a href="{{ route('finance.charts.index') }}" class="t-btn t-btn-ghost">
            <i class="bi bi-bar-chart-line"></i> Grafik
        </a>
        <a href="{{ route('finance.transactions.create') }}" class="t-btn t-btn-primary">
            <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">Tambah Transaksi</span><span class="d-sm-none">Tambah</span>
        </a>
    </div>
</div>

{{-- ── Stat Cards ──────────────────────────────────────── --}}
<div class="t-stats">
    <div class="t-stat-card">
        <div class="sc-label">Total Masuk</div>
        <div class="sc-val val-debit">Rp {{ number_format($totalDebit, 0, ',', '.') }}</div>
        <div class="sc-sub">Akumulasi debit</div>
    </div>
    <div class="t-stat-card">
        <div class="sc-label">Total Keluar</div>
        <div class="sc-val val-kredit">Rp {{ number_format($totalKredit, 0, ',', '.') }}</div>
        <div class="sc-sub">Akumulasi kredit</div>
    </div>
    <div class="t-stat-card">
        <div class="sc-label">Saldo Bersih</div>
        <div class="sc-val {{ $saldo < 0 ? 'val-neg' : '' }}">
            {{ $saldo < 0 ? '−' : '' }}Rp {{ number_format(abs($saldo), 0, ',', '.') }}
        </div>
        <div class="sc-sub">Debit − Kredit</div>
    </div>
    <div class="t-stat-card">
        <div class="sc-label">Jumlah Transaksi</div>
        <div class="sc-val">{{ number_format($countTotal) }}</div>
        <div class="sc-sub">Seluruh periode</div>
    </div>
</div>

{{-- ── Alerts ──────────────────────────────────────────── --}}
@if(session('success'))
    <div class="t-alert t-alert-success">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="t-alert t-alert-error">
        <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
    </div>
@endif

{{-- ── Filter Bar ──────────────────────────────────────── --}}
<form method="GET" action="{{ route('finance.transactions.index') }}">
<div class="t-filter">
    <div class="t-filter-group">
        <span class="t-filter-label">Periode Awal</span>
        <input type="date" name="start_date" class="fc-input w-100" value="{{ request('start_date') }}">
    </div>

    <div class="t-filter-group">
        <span class="t-filter-label">Periode Akhir</span>
        <input type="date" name="end_date" class="fc-input w-100" value="{{ request('end_date') }}">
    </div>

    <div class="t-filter-group">
        <span class="t-filter-label">Tipe</span>
        <select name="type" class="fc-select w-100">
            <option value="">Semua Tipe</option>
            <option value="debit"  {{ request('type')=='debit'  ? 'selected':'' }}>Debit</option>
            <option value="kredit" {{ request('type')=='kredit' ? 'selected':'' }}>Kredit</option>
        </select>
    </div>

    <div class="t-filter-group" style="min-width:160px">
        <span class="t-filter-label">Kategori Akun</span>
        <select name="account_id" class="fc-select w-100">
            <option value="">Semua Akun</option>
            @foreach($accounts as $acc)
                <option value="{{ $acc->id }}" {{ request('account_id')==$acc->id ? 'selected':'' }}>
                    [{{ $acc->code }}] {{ $acc->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="t-filter-group" style="flex: 2; min-width: 180px;">
        <span class="t-filter-label">Pencarian</span>
        <div class="t-filter-search w-100">
            <i class="bi bi-search"></i>
            <input type="text" name="search" placeholder="Cari keterangan…" value="{{ request('search') }}">
        </div>
    </div>

    <div class="t-filter-group d-flex flex-row gap-2">
        <button type="submit" class="t-btn t-btn-primary flex-fill" style="height:34px">
            <i class="bi bi-funnel"></i> Filter
        </button>
        @if(request()->hasAny(['start_date','end_date','type','account_id','search']))
            <a href="{{ route('finance.transactions.index') }}" class="t-btn t-btn-ghost" style="height:34px">
                <i class="bi bi-x-lg"></i>
            </a>
        @endif
    </div>

    <div class="t-filter-count text-end">
        <strong>{{ $transactions->total() }}</strong> transaksi ditemukan
    </div>
</div>
</form>

{{-- ── Table ───────────────────────────────────────────── --}}
<div class="t-card">
    <div class="table-responsive">
        <table class="t-table">
            <thead>
                <tr>
                    <th style="width:110px">Tanggal</th>
                    <th>Keterangan</th>
                    <th class="d-none d-md-table-cell" style="width:150px">Akun</th>
                    <th class="d-none d-lg-table-cell">Entitas</th>
                    <th class="text-end" style="width:130px">Debit</th>
                    <th class="text-end" style="width:130px">Kredit</th>
                    <th class="d-none d-md-table-cell text-end" style="width:140px">Saldo Running</th>
                    <th style="width:80px"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                @php $isAutoPph = $trx->isAutoPph(); @endphp
                <tr class="{{ $isAutoPph ? 'row-auto-pph' : ($trx->is_end_of_year ? 'row-eoy' : ($trx->is_end_of_month ? 'row-eom' : '')) }}">

                    {{-- Tanggal --}}
                    <td>
                        <div class="fw-semibold" style="font-size:.83rem;color:var(--t-navy)">
                            {{ $trx->transaction_date->format('d M Y') }}
                        </div>
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            <span class="type-badge type-{{ $trx->transaction_type }}">
                                {{ strtoupper($trx->transaction_type) }}
                            </span>
                            @if($trx->is_end_of_month && !$trx->is_end_of_year)
                                <span class="period-tag">AKHIR BLN</span>
                            @endif
                            @if($trx->is_end_of_year)
                                <span class="period-tag" style="background:#f1f2f6;color:var(--t-navy);border-color:#c8d0e0">AKHIR THN</span>
                            @endif
                        </div>
                    </td>

                    {{-- Keterangan --}}
                    <td>
                        <div class="fw-semibold" style="color:var(--t-navy);font-size:.84rem">
                            @if($isAutoPph)
                                <i class="bi bi-shield-lock-fill" style="color:#3b82f6;font-size:.75rem"></i>
                            @endif
                            {{ $isAutoPph ? str_replace('[AUTO PPh] ', '', $trx->description) : $trx->description }}
                        </div>
                        <div style="font-size:.73rem;color:var(--t-muted);margin-top:.15rem">
                            {{ $isAutoPph ? 'Dibuat otomatis oleh sistem' : 'oleh ' . ($trx->creator->name ?? 'System') }}
                        </div>
                        @if($isAutoPph)
                            <span class="pph-auto-tag">
                                <i class="bi bi-arrow-right-circle-fill" style="font-size:.6rem"></i> PPh Auto → Utang PPh
                            </span>
                        @elseif($trx->tax_type && $trx->tax_type !== 'none' && ($trx->tax_amount ?? 0) > 0)
                            @php $taxLabels = ['ppn'=>'PPN','pph_21'=>'PPh 21','pph_23'=>'PPh 23','pph_4_ayat_2'=>'PPh 4(2)']; @endphp
                            <span class="tax-tag mt-1 d-inline-block">
                                {{ $taxLabels[$trx->tax_type] ?? $trx->tax_type }}
                                · Rp {{ number_format($trx->tax_amount, 0, ',', '.') }}
                            </span>
                            @if($trx->pph_transaction_id)
                                <span class="pph-auto-tag">
                                    <i class="bi bi-check-circle-fill" style="font-size:.6rem"></i> PPh kredit otomatis ✓
                                </span>
                            @endif
                        @endif
                    </td>

                    {{-- Akun --}}
                    <td class="d-none d-md-table-cell">
                        <span class="coa-code">{{ $trx->account->code ?? '—' }}</span>
                        <div style="font-size:.72rem;color:var(--t-muted);margin-top:.25rem">
                            {{ $trx->account->name ?? '' }}
                        </div>
                    </td>

                    {{-- Entitas --}}
                    <td class="d-none d-lg-table-cell">
                        @if($trx->senderEntity)
                            <div class="entity-from">Dari: <strong>{{ $trx->senderEntity->name }}</strong></div>
                        @endif
                        @if($trx->receiverEntity)
                            <div class="entity-from">Ke: <strong>{{ $trx->receiverEntity->name }}</strong></div>
                        @endif
                        @if(!$trx->senderEntity && !$trx->receiverEntity)
                            <span style="color:#d1d9e6">—</span>
                        @endif
                    </td>

                    {{-- Debit --}}
                    <td class="text-end">
                        @if($trx->transaction_type === 'debit')
                            <span class="amt-debit">{{ number_format($trx->amount, 0, ',', '.') }}</span>
                        @else
                            <span class="amt-blank">—</span>
                        @endif
                    </td>

                    {{-- Kredit --}}
                    <td class="text-end">
                        @if($trx->transaction_type === 'kredit')
                            <span class="amt-kredit">{{ number_format($trx->amount, 0, ',', '.') }}</span>
                        @else
                            <span class="amt-blank">—</span>
                        @endif
                    </td>

                    {{-- Saldo Running --}}
                    <td class="d-none d-md-table-cell text-end">
                        <span class="amt-running {{ $trx->running_balance < 0 ? 'neg' : '' }}">
                            {{ $trx->running_balance < 0 ? '−' : '' }}{{ number_format(abs($trx->running_balance), 0, ',', '.') }}
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            @if($isAutoPph)
                                {{-- Transaksi PPh otomatis: hanya tampilkan icon lock, tidak bisa edit/hapus --}}
                                <span class="act-btn" title="Dibuat otomatis — edit transaksi induknya"
                                      style="cursor:default;opacity:.45;">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                            @else
                                @if($trx->document_path)
                                <a href="{{ route('finance.transactions.document', $trx->id) }}"
                                   target="_blank" class="act-btn" title="Lampiran">
                                    <i class="bi bi-paperclip"></i>
                                </a>
                                @endif
                                <a href="{{ route('finance.transactions.edit', $trx->id) }}"
                                   class="act-btn act-edit" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('finance.transactions.destroy', $trx->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus transaksi ini? Saldo akan dihitung ulang.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="act-btn act-delete" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="t-empty">
                            <div class="t-empty-icon">📒</div>
                            <div class="t-empty-title">Belum ada transaksi</div>
                            <p class="t-empty-sub">Mulai catat pemasukan dan pengeluaran kas.</p>
                            <a href="{{ route('finance.transactions.create') }}" class="t-btn t-btn-primary">
                                <i class="bi bi-plus-lg"></i> Tambah Transaksi Pertama
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($transactions->hasPages())
    <div class="t-pager">
        <span>
            Menampilkan
            <strong>{{ $transactions->firstItem() }}–{{ $transactions->lastItem() }}</strong>
            dari <strong>{{ $transactions->total() }}</strong> transaksi
        </span>
        {{ $transactions->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>

@endsection
