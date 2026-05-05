@extends('layouts.dashboard')

@section('title', 'Laporan Keuangan — ' . $periodLabel)

@push('styles')
<style>
/* ══ Design Tokens ═══════════════════════════════════════════════════════ */
:root {
    --bg:        #ffffff;
    --surface:   #f8f9fa;
    --border:    #e9ecef;
    --muted:     #6c757d;
    --body:      #212529;
    --accent:    #1a1f3c;
    --ink:       #344767;
    --radius-lg: 12px;
    --radius-md: 8px;
    --radius-sm: 6px;
    --shadow:    0 1px 4px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04);
}

/* ══ Hero ════════════════════════════════════════════════════════════════ */
.rpt-hero {
    background: var(--accent);
    border-radius: var(--radius-lg);
    padding: 1.5rem 1.75rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}
.rpt-hero::before {
    content: ''; position: absolute;
    top: -60px; right: -60px;
    width: 240px; height: 240px;
    border-radius: 50%;
    background: rgba(255,255,255,.03);
    pointer-events: none;
}
.hero-title { font-size: 1rem; font-weight: 700; color: #fff; margin: 0; letter-spacing: -.01em; }
.hero-sub   { font-size: .75rem; color: rgba(255,255,255,.45); margin: .2rem 0 0; }
.hero-period { color: rgba(255,255,255,.7); font-weight: 600; }

.hero-action {
    display: inline-flex; align-items: center; gap: .35rem;
    font-size: .78rem; font-weight: 600;
    padding: .38rem 1rem; border-radius: var(--radius-md);
    text-decoration: none; transition: opacity .15s;
}
.hero-action:hover { opacity: .85; }
.hero-action-ghost {
    background: rgba(255,255,255,.1);
    color: rgba(255,255,255,.85);
    border: 1px solid rgba(255,255,255,.18);
}

.stat-strip { display: flex; gap: .5rem; flex-wrap: wrap; }
.stat-pill {
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: var(--radius-md);
    padding: .55rem 1rem;
    text-align: center; min-width: 100px;
}
.stat-pill .sp-val { font-size: .95rem; font-weight: 800; color: #fff; line-height: 1; font-variant-numeric: tabular-nums; }
.stat-pill .sp-val.positive { color: rgba(160,239,204,.9); }
.stat-pill .sp-val.negative { color: rgba(255,180,180,.9); }
.stat-pill .sp-lbl { font-size: .6rem; color: rgba(255,255,255,.4); text-transform: uppercase; letter-spacing: .07em; margin-top: .25rem; }

/* ══ Period Filter ═══════════════════════════════════════════════════════ */
.period-bar {
    display: flex; flex-wrap: wrap; gap: .65rem; align-items: center;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: .75rem 1rem;
    margin-bottom: 1.25rem;
}
.period-bar .form-select {
    border-radius: var(--radius-md);
    border: 1.5px solid var(--border);
    font-size: .82rem;
    background: #fff;
    color: var(--body);
}
.period-bar .form-select:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(26,31,60,.1);
    outline: none;
}
.period-label {
    display: inline-flex; align-items: center; gap: .35rem;
    font-size: .73rem; font-weight: 600;
    color: var(--accent);
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: .28rem .75rem;
}

/* ══ KPI Cards ═══════════════════════════════════════════════════════════ */
.kpi-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.15rem 1.25rem;
    position: relative;
    overflow: hidden;
    transition: box-shadow .2s;
}
.kpi-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.08) !important; }
.kpi-card-bar {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: var(--accent);
}
.kpi-card-bar.income  { background: #1a1f3c; }
.kpi-card-bar.expense { background: #374151; }
.kpi-card-bar.balance { background: #6b7280; }
.kpi-card-bar.profit  { background: #9ca3af; }

.kpi-label {
    font-size: .63rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .08em;
    color: var(--muted);
    margin: 0 0 .4rem;
}
.kpi-value {
    font-size: 1.25rem; font-weight: 800;
    color: var(--body);
    line-height: 1.1; margin: 0 0 .25rem;
    font-variant-numeric: tabular-nums;
}
.kpi-value.positive { color: #1a1f3c; }
.kpi-value.negative { color: #dc3545; }
.kpi-sub {
    font-size: .7rem;
    color: var(--muted);
    margin: 0;
}
.kpi-indicator {
    position: absolute;
    right: 1rem; top: 50%;
    transform: translateY(-50%);
    font-size: .65rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .06em;
    border-radius: var(--radius-sm);
    padding: .2rem .55rem;
    border: 1px solid;
}
.kpi-indicator.up   { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }
.kpi-indicator.down { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
.kpi-indicator.flat { background: var(--surface); color: var(--muted); border-color: var(--border); }

/* ══ Section Headers ═════════════════════════════════════════════════════ */
.sec-header {
    display: flex; align-items: center; gap: .65rem;
    padding: 1.25rem 0 .9rem;
    border-bottom: 1px solid var(--border);
    margin-bottom: 1rem;
}
.sec-icon {
    width: 32px; height: 32px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem;
    flex-shrink: 0;
    color: var(--muted);
}
.sec-title { font-size: .88rem; font-weight: 700; color: var(--body); margin: 0; }
.sec-desc  { font-size: .72rem; color: var(--muted); margin: 0; }
.sec-badge {
    margin-left: auto;
    font-size: .65rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .06em;
    border-radius: var(--radius-sm);
    padding: .22rem .65rem;
    background: var(--surface);
    border: 1px solid var(--border);
    color: var(--muted);
}

/* ══ Report Cards ════════════════════════════════════════════════════════ */
.rpt-card {
    background: #fff;
    border: 1px solid var(--border) !important;
    border-radius: var(--radius-lg) !important;
    overflow: hidden;
    height: 100%;
    transition: box-shadow .2s;
    box-shadow: var(--shadow) !important;
}
.rpt-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.08) !important; }
.rpt-card-header {
    padding: 1.1rem 1.3rem .85rem;
    border-bottom: 1px solid var(--border);
}
.rpt-card-body { padding: 1rem 1.3rem 1.3rem; }

/* ══ P&L Rows ════════════════════════════════════════════════════════════ */
.pl-section-label {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    font-size: .62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--muted);
    border-radius: var(--radius-sm);
    padding: .22rem .65rem;
    background: var(--surface);
    border: 1px solid var(--border);
    margin-bottom: .6rem;
}
.pl-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .45rem .25rem;
    border-radius: var(--radius-sm);
    transition: background .1s;
}
.pl-row:hover { background: var(--surface); }
.pl-name { font-size: .8rem; color: var(--muted); }
.pl-val  { font-size: .8rem; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--body); }
.pl-total {
    border-top: 1px solid var(--border);
    margin-top: .3rem;
    padding-top: .45rem;
}
.pl-total .pl-name { font-weight: 700; color: var(--body); font-size: .82rem; }
.pl-total .pl-val  { font-size: .85rem; }

/* ══ Net Box ═════════════════════════════════════════════════════════════ */
.net-box {
    border-radius: var(--radius-md);
    border: 1px solid;
    padding: .9rem 1rem;
    text-align: center;
    margin-top: .75rem;
}
.net-box.profit  { background: #f0fdf4; border-color: #bbf7d0; }
.net-box.loss    { background: #fef2f2; border-color: #fecaca; }
.net-box-label { font-size: .63rem; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; margin: 0 0 .25rem; }
.net-box-label.profit { color: #166534; }
.net-box-label.loss   { color: #991b1b; }
.net-box-value { font-size: 1.3rem; font-weight: 900; font-variant-numeric: tabular-nums; margin: 0; }
.net-box-value.profit { color: #15803d; }
.net-box-value.loss   { color: #dc2626; }

/* ══ Chart Legend ════════════════════════════════════════════════════════ */
.chart-legend {
    display: flex; gap: .75rem; align-items: center; flex-wrap: wrap;
}
.legend-item {
    display: flex; align-items: center; gap: .3rem;
    font-size: .72rem; font-weight: 600; color: var(--muted);
}
.legend-dot {
    width: 8px; height: 8px;
    border-radius: 2px;
    flex-shrink: 0;
}

/* ══ Top List ════════════════════════════════════════════════════════════ */
.top-item {
    display: flex; align-items: center; gap: .7rem;
    padding: .6rem .4rem;
    border-radius: var(--radius-md);
    transition: background .1s;
}
.top-item:hover { background: var(--surface); }
.rank-badge {
    width: 24px; height: 24px;
    border-radius: 50%;
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: .7rem; font-weight: 800;
    background: var(--surface);
    border: 1px solid var(--border);
    color: var(--muted);
}
.rank-badge.r1 { background: var(--accent); color: #fff; border-color: var(--accent); }
.rank-badge.r2 { background: #374151; color: #fff; border-color: #374151; }
.rank-badge.r3 { background: #6b7280; color: #fff; border-color: #6b7280; }

.prog-track { height: 3px; border-radius: 99px; background: var(--border); overflow: hidden; flex: 1; }
.prog-bar   { height: 3px; border-radius: 99px; background: var(--accent); }

/* ══ Empty Mini ══════════════════════════════════════════════════════════ */
.empty-mini { text-align: center; padding: 2rem 0; }
.empty-mini-icon { font-size: 1.25rem; opacity: .2; display: block; margin-bottom: .5rem; }
</style>
@endpush
@include('finance._finance_mobile')

@section('content')

@php
    $saldo = $totalDebit - $totalKredit;
    $np    = $totalRevenue - $totalExpense;
@endphp

{{-- ══ Hero ═════════════════════════════════════════════════════════════ --}}
<div class="rpt-hero">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <p class="hero-title">Laporan Keuangan</p>
            <p class="hero-sub">
                Ringkasan finansial organisasi —
                <span class="hero-period">{{ $periodLabel }}</span>
            </p>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <a href="{{ route('finance.charts.index', ['year' => $year]) }}" class="hero-action hero-action-ghost">
                    <i class="bi bi-bar-chart-line" style="font-size:.75rem"></i> Grafik Analitik
                </a>
                <a href="{{ route('finance.transactions.index') }}" class="hero-action hero-action-ghost">
                    <i class="bi bi-book" style="font-size:.75rem"></i> Buku Kas
                </a>
            </div>
        </div>
        <div class="stat-strip">
            <div class="stat-pill">
                <div class="sp-val positive">Rp {{ number_format($totalDebit/1e6,1) }}jt</div>
                <div class="sp-lbl">Total Masuk</div>
            </div>
            <div class="stat-pill">
                <div class="sp-val negative">Rp {{ number_format($totalKredit/1e6,1) }}jt</div>
                <div class="sp-lbl">Total Keluar</div>
            </div>
            <div class="stat-pill">
                <div class="sp-val {{ $saldo < 0 ? 'negative' : '' }}">
                    {{ $saldo < 0 ? '−' : '' }}Rp {{ number_format(abs($saldo)/1e6,1) }}jt
                </div>
                <div class="sp-lbl">Saldo Bersih</div>
            </div>
            <div class="stat-pill">
                <div class="sp-val {{ $np < 0 ? 'negative' : '' }}">
                    {{ $np < 0 ? '−' : '' }}Rp {{ number_format(abs($np)/1e6,1) }}jt
                </div>
                <div class="sp-lbl">Laba / Rugi</div>
            </div>
        </div>
    </div>
</div>

{{-- ══ Period Filter ═══════════════════════════════════════════════════════ --}}
<form method="GET" action="{{ route('finance.reports.index') }}">
    <div class="period-bar">
        <span style="font-size: .78rem; font-weight: 600; color: var(--body)">Periode Laporan</span>
        <select name="year" class="form-select" style="width: 100px">
            @foreach($availableYears as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <select name="month" class="form-select" style="width: 155px">
            <option value="">Seluruh Tahun</option>
            @foreach(range(1,12) as $m)
                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create(null,$m,1)->translatedFormat('F') }}
                </option>
            @endforeach
        </select>
        <div class="filter-actions">
            <button type="submit" class="btn btn-sm mb-0 fw-semibold"
                    style="background: var(--accent); color: #fff; border-radius: var(--radius-md); padding: .38rem 1.1rem; font-size: .8rem">
                Terapkan
            </button>
            <a href="{{ route('finance.reports.index') }}"
               class="btn btn-sm mb-0"
               style="border: 1.5px solid var(--border); border-radius: var(--radius-md); color: var(--muted); font-size: .8rem; padding: .38rem .85rem;">
                Reset
            </a>
        </div>
        <span class="period-label ms-auto">
            <i class="bi bi-calendar3" style="font-size: .7rem"></i>
            {{ $periodLabel }}
        </span>
    </div>
</form>

{{-- ══ KPI Cards ════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-1">
    {{-- Total Masuk --}}
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-card-bar income"></div>
            <p class="kpi-label">Total Masuk</p>
            <p class="kpi-value positive">Rp {{ number_format($totalDebit,0,',','.') }}</p>
            <p class="kpi-sub">Semua debit / penerimaan kas</p>
        </div>
    </div>

    {{-- Total Keluar --}}
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-card-bar expense"></div>
            <p class="kpi-label">Total Keluar</p>
            <p class="kpi-value {{ $totalKredit > 0 ? 'negative' : '' }}">Rp {{ number_format($totalKredit,0,',','.') }}</p>
            <p class="kpi-sub">Semua kredit / pengeluaran kas</p>
        </div>
    </div>

    {{-- Saldo Bersih --}}
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-card-bar balance"></div>
            <p class="kpi-label">Saldo Bersih</p>
            <p class="kpi-value {{ $saldo < 0 ? 'negative' : 'positive' }}">
                {{ $saldo < 0 ? '−' : '' }}Rp {{ number_format(abs($saldo),0,',','.') }}
            </p>
            <p class="kpi-sub">Debit − Kredit</p>
            <span class="kpi-indicator {{ $saldo >= 0 ? 'up' : 'down' }}">
                {{ $saldo >= 0 ? 'Positif' : 'Defisit' }}
            </span>
        </div>
    </div>

    {{-- Laba / Rugi --}}
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-card-bar profit"></div>
            <p class="kpi-label">Laba / Rugi</p>
            <p class="kpi-value {{ $np < 0 ? 'negative' : 'positive' }}">
                {{ $np < 0 ? '−' : '' }}Rp {{ number_format(abs($np),0,',','.') }}
            </p>
            <p class="kpi-sub">Revenue − Expense</p>
            <span class="kpi-indicator {{ $np >= 0 ? 'up' : 'down' }}">
                {{ $np >= 0 ? 'Laba' : 'Rugi' }}
            </span>
        </div>
    </div>
</div>

{{-- ══ Section: Cashflow & P&L ══════════════════════════════════════════ --}}
<div class="sec-header">
    <div class="sec-icon"><i class="bi bi-graph-up"></i></div>
    <div>
        <p class="sec-title">Cashflow Bulanan & Laba/Rugi</p>
        <p class="sec-desc">Arus kas dan perbandingan revenue vs expense sepanjang {{ $year }}</p>
    </div>
    <span class="sec-badge">{{ $periodLabel }}</span>
</div>

<div class="row g-3 mb-1">
    {{-- Cashflow Chart --}}
    <div class="col-lg-8">
        <div class="rpt-card">
            <div class="rpt-card-header d-flex justify-content-between align-items-start">
                <div>
                    <p class="fw-semibold mb-0" style="font-size: .88rem; color: var(--body)">
                        Cashflow Bulanan {{ $year }}
                    </p>
                    <p class="mb-0" style="font-size: .72rem; color: var(--muted); margin-top: .1rem">
                        Debit masuk vs Kredit keluar setiap bulan
                    </p>
                </div>
                <div class="chart-legend">
                    <span class="legend-item">
                        <span class="legend-dot" style="background: var(--accent)"></span>
                        Masuk
                    </span>
                    <span class="legend-item">
                        <span class="legend-dot" style="background: #6b7280"></span>
                        Keluar
                    </span>
                    <span class="legend-item">
                        <span class="legend-dot" style="background: #9ca3af; border-radius: 50%"></span>
                        Saldo
                    </span>
                </div>
            </div>
            <div class="rpt-card-body">
                <div style="position: relative; height: 260px; width: 100%;">
                    <canvas id="cashflowChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- P&L Panel --}}
    <div class="col-lg-4">
        <div class="rpt-card">
            <div class="rpt-card-header">
                <p class="fw-semibold mb-0" style="font-size: .88rem; color: var(--body)">
                    Laba / Rugi — {{ $periodLabel }}
                </p>
                <p class="mb-0" style="font-size: .72rem; color: var(--muted); margin-top: .1rem">
                    Komparasi revenue dan expense per akun
                </p>
            </div>
            <div class="rpt-card-body">
                {{-- Revenue --}}
                <div class="pl-section-label mb-2">
                    <i class="bi bi-arrow-down-circle" style="font-size: .65rem"></i>
                    Pendapatan
                </div>
                @forelse($revenueByAccount as $r)
                <div class="pl-row">
                    <span class="pl-name">{{ $r->account->name ?? '—' }}</span>
                    <span class="pl-val">{{ number_format($r->total,0,',','.') }}</span>
                </div>
                @empty
                <p style="font-size: .75rem; color: var(--muted); padding: .3rem .25rem">
                    Belum ada data pendapatan.
                </p>
                @endforelse
                <div class="pl-row pl-total mb-3">
                    <span class="pl-name">Total Revenue</span>
                    <span class="pl-val">Rp {{ number_format($totalRevenue,0,',','.') }}</span>
                </div>

                {{-- Expense --}}
                <div class="pl-section-label mb-2">
                    <i class="bi bi-arrow-up-circle" style="font-size: .65rem"></i>
                    Pengeluaran
                </div>
                @forelse($expenseByAccount as $e)
                <div class="pl-row">
                    <span class="pl-name">{{ $e->account->name ?? '—' }}</span>
                    <span class="pl-val">{{ number_format($e->total,0,',','.') }}</span>
                </div>
                @empty
                <p style="font-size: .75rem; color: var(--muted); padding: .3rem .25rem">
                    Belum ada data pengeluaran.
                </p>
                @endforelse
                <div class="pl-row pl-total">
                    <span class="pl-name">Total Expense</span>
                    <span class="pl-val">Rp {{ number_format($totalExpense,0,',','.') }}</span>
                </div>

                {{-- Net Box --}}
                <div class="net-box {{ $np >= 0 ? 'profit' : 'loss' }}">
                    <p class="net-box-label {{ $np >= 0 ? 'profit' : 'loss' }}">
                        {{ $np >= 0 ? 'Laba Bersih' : 'Rugi Bersih' }}
                    </p>
                    <p class="net-box-value {{ $np >= 0 ? 'profit' : 'loss' }}">
                        {{ $np < 0 ? '−' : '' }}Rp {{ number_format(abs($np),0,',','.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ Section: Top 10 ═══════════════════════════════════════════════════ --}}
<div class="sec-header">
    <div class="sec-icon"><i class="bi bi-list-ol"></i></div>
    <div>
        <p class="sec-title">Analisis Pengeluaran</p>
        <p class="sec-desc">10 transaksi & entitas penerima terbesar periode {{ $periodLabel }}</p>
    </div>
    <span class="sec-badge">Top 10</span>
</div>

<div class="row g-3">
    {{-- Top 10 Transaksi --}}
    <div class="col-lg-6">
        <div class="rpt-card">
            <div class="rpt-card-header">
                <p class="fw-semibold mb-0" style="font-size: .88rem; color: var(--body)">
                    10 Pengeluaran Terbesar
                </p>
                <p class="mb-0" style="font-size: .72rem; color: var(--muted); margin-top: .1rem">
                    Transaksi kredit (keluar) dengan nominal tertinggi
                </p>
            </div>
            <div class="rpt-card-body" style="max-height: 360px; overflow-y: auto">
                @php $maxE = $top10Expenses->max('amount') ?: 1; @endphp
                @forelse($top10Expenses as $i => $trx)
                <div class="top-item">
                    <div class="rank-badge {{ $i < 3 ? ['r1','r2','r3'][$i] : '' }}">{{ $i+1 }}</div>
                    <div style="flex: 1; min-width: 0">
                        <p class="fw-semibold mb-1 text-truncate" style="font-size: .82rem; color: var(--body)">
                            {{ $trx->description }}
                        </p>
                        <div class="d-flex align-items-center gap-2">
                            <div class="prog-track">
                                <div class="prog-bar" style="width: {{ ($trx->amount/$maxE)*100 }}%"></div>
                            </div>
                            <span style="font-size: .7rem; color: var(--muted); white-space: nowrap">
                                {{ $trx->transaction_date->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                    <span style="font-size: .82rem; font-weight: 700; color: var(--body); font-variant-numeric: tabular-nums; white-space: nowrap">
                        Rp {{ number_format($trx->amount,0,',','.') }}
                    </span>
                </div>
                @empty
                <div class="empty-mini">
                    <span class="empty-mini-icon"><i class="bi bi-receipt"></i></span>
                    <p style="font-size: .78rem; color: var(--muted)">Tidak ada data pengeluaran pada periode ini.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Top 10 Entitas --}}
    <div class="col-lg-6">
        <div class="rpt-card">
            <div class="rpt-card-header">
                <p class="fw-semibold mb-0" style="font-size: .88rem; color: var(--body)">
                    10 Entitas Penerima Terbesar
                </p>
                <p class="mb-0" style="font-size: .72rem; color: var(--muted); margin-top: .1rem">
                    Vendor / entitas dengan total pembayaran terbesar periode ini
                </p>
            </div>
            <div class="rpt-card-body" style="max-height: 360px; overflow-y: auto">
                @php $maxEnt = $top10Entities->max('total') ?: 1; @endphp
                @forelse($top10Entities as $i => $row)
                <div class="top-item">
                    <div class="rank-badge {{ $i < 3 ? ['r1','r2','r3'][$i] : '' }}">{{ $i+1 }}</div>
                    <div style="flex: 1; min-width: 0">
                        <p class="fw-semibold mb-1 text-truncate" style="font-size: .82rem; color: var(--body)">
                            {{ $row->receiverEntity->name ?? 'Entitas Tidak Diketahui' }}
                        </p>
                        <div class="d-flex align-items-center gap-2">
                            <div class="prog-track">
                                <div class="prog-bar" style="width: {{ ($row->total/$maxEnt)*100 }}%"></div>
                            </div>
                            <span style="font-size: .7rem; color: var(--muted); white-space: nowrap">
                                {{ $row->trx_count }} transaksi
                            </span>
                        </div>
                    </div>
                    <span style="font-size: .82rem; font-weight: 700; color: var(--body); font-variant-numeric: tabular-nums; white-space: nowrap">
                        Rp {{ number_format($row->total,0,',','.') }}
                    </span>
                </div>
                @empty
                <div class="empty-mini">
                    <span class="empty-mini-icon"><i class="bi bi-building"></i></span>
                    <p style="font-size: .78rem; color: var(--muted)">Tidak ada data entitas pengeluaran pada periode ini.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const months  = @json($cashflowMonths);
const debits  = @json($cashflowDebits);
const kredits = @json($cashflowKredits);
const nets    = @json($cashflowNets);

new Chart(document.getElementById('cashflowChart'), {
    type: 'bar',
    data: {
        labels: months,
        datasets: [
            {
                label: 'Debit (Masuk)',
                data: debits,
                backgroundColor: 'rgba(26,31,60,.85)',
                borderRadius: 5,
                borderSkipped: false,
            },
            {
                label: 'Kredit (Keluar)',
                data: kredits,
                backgroundColor: 'rgba(107,114,128,.65)',
                borderRadius: 5,
                borderSkipped: false,
            },
            {
                label: 'Saldo Bersih',
                data: nets,
                type: 'line',
                borderColor: '#9ca3af',
                backgroundColor: 'rgba(156,163,175,.06)',
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: '#6b7280',
                fill: true,
                tension: 0.4,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: c => c.dataset.label + ': Rp ' + Math.abs(c.parsed.y).toLocaleString('id-ID')
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: v => 'Rp ' + (v/1e6).toFixed(1) + 'jt',
                    font: { size: 10 },
                    color: '#9da7b6',
                },
                grid: { color: '#f0f2f5' }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 10 }, color: '#9da7b6' }
            }
        }
    }
});
</script>
@endpush
