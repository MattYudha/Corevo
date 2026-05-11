@extends('layouts.dashboard')

@section('title', 'Catat Transaksi Baru')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css">
<style>
/* ════════════════════════════════════════════════════
   FINANCE CREATE — Clean Professional
   ════════════════════════════════════════════════════ */
:root {
    --ef-navy:    #1b2a4a;
    --ef-slate:   #3d4e6c;
    --ef-muted:   #7486a4;
    --ef-border:  #e2e7f0;
    --ef-soft:    #f1f4f9;
    --ef-bg:      #f7f9fc;
    --ef-white:   #ffffff;
    --ef-brand:   #1e3a5f;
    --ef-focus:   rgba(30,58,95,0.10);
}

/* ── Page wrapper ──────────────────────────────────── */
.fc-wrap {
    width: 100%;
    padding-bottom: 2rem;
}

/* ── Page Header ───────────────────────────────────── */
.fc-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 1.4rem;
    border-bottom: 1px solid var(--ef-border);
    margin-bottom: 1.75rem;
    gap: 1rem;
}
.fc-header-left {}
.fc-breadcrumb {
    font-size: .75rem;
    color: var(--ef-muted);
    display: flex;
    align-items: center;
    gap: .3rem;
    margin-bottom: .25rem;
}
.fc-breadcrumb a { color: var(--ef-muted); text-decoration: none; }
.fc-breadcrumb a:hover { color: var(--ef-navy); }
.fc-title {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--ef-navy);
    letter-spacing: -.025em;
    margin: 0;
    line-height: 1.2;
}
.fc-subtitle {
    font-size: .78rem;
    color: var(--ef-muted);
    margin: .2rem 0 0;
}

/* ── Header action buttons ─────────────────────────── */
.fc-header-actions {
    display: flex;
    align-items: center;
    gap: .75rem;
    flex-shrink: 0;
}
.fc-btn {
    display: inline-flex;
    align-items: center;
    gap: .38rem;
    padding: .5rem 1.2rem;
    font-size: .8rem;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    border: 1px solid transparent;
    transition: all .15s;
    text-decoration: none;
    font-family: inherit;
    white-space: nowrap;
    line-height: 1;
}
.fc-btn-ghost {
    background: var(--ef-white);
    border-color: var(--ef-border);
    color: var(--ef-slate);
}
.fc-btn-ghost:hover { background: var(--ef-bg); color: var(--ef-navy); }
.fc-btn-primary {
    background: var(--ef-brand);
    border-color: var(--ef-brand);
    color: #fff;
    box-shadow: 0 2px 8px rgba(30,58,95,.2);
}
.fc-btn-primary:hover { background: #142840; border-color: #142840; box-shadow: 0 4px 12px rgba(30,58,95,.3); }
.fc-btn-primary:disabled { opacity: .6; cursor: not-allowed; box-shadow: none; }

/* ── Error banner ──────────────────────────────────── */
.fc-error-banner {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-left: 3px solid #ef4444;
    border-radius: 8px;
    padding: .85rem 1rem;
    margin-bottom: 1.5rem;
    font-size: .8rem;
    color: #991b1b;
}

/* ── Section card ──────────────────────────────────── */
.fc-section {
    background: var(--ef-white);
    border: 1px solid var(--ef-border);
    border-radius: 12px;
    box-shadow: 0 1px 6px rgba(0,0,0,.04);
    overflow: visible;
    margin-bottom: 1.25rem;
    transition: box-shadow .2s;
    position: relative;
}
.fc-section:focus-within {
    box-shadow: 0 2px 14px rgba(30,58,95,.08);
    border-color: #c8d5e8;
}
.fc-section-header {
    display: flex;
    align-items: center;
    gap: .55rem;
    padding: .75rem 1.5rem;
    border-bottom: 1px solid var(--ef-border);
    background: #fafbfd;
    border-radius: 12px 12px 0 0;
}
.fc-section-icon {
    width: 26px; height: 26px;
    border-radius: 6px;
    background: #eef2f8;
    display: flex; align-items: center; justify-content: center;
    font-size: .75rem; color: var(--ef-brand); flex-shrink: 0;
}
.fc-section-label {
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .12em;
    color: var(--ef-muted);
    margin: 0;
}
.fc-section-body { padding: 1.5rem; }

/* ── Form fields ───────────────────────────────────── */
.fc-field { margin-bottom: 1.25rem; }
.fc-field:last-child { margin-bottom: 0; }
.fc-label {
    display: block;
    font-size: .78rem;
    font-weight: 600;
    color: var(--ef-slate);
    margin-bottom: .45rem;
}
.fc-label .req { color: #c0392b; font-weight: 400; margin-left: .1rem; }
.fc-label .opt { font-size: .7rem; font-weight: 400; color: var(--ef-muted); margin-left: .3rem; }
.fc-input, .fc-select, .fc-textarea {
    display: block;
    width: 100%;
    padding: .55rem .85rem;
    font-size: .85rem;
    font-family: inherit;
    color: var(--ef-navy);
    background: var(--ef-white);
    border: 1px solid var(--ef-border);
    border-radius: 8px;
    transition: border-color .15s, box-shadow .15s;
    appearance: none;
    -webkit-appearance: none;
    line-height: 1.5;
}
.fc-textarea {
    resize: vertical;
    min-height: 88px;
    line-height: 1.6;
}
.fc-input:focus, .fc-select:focus, .fc-textarea:focus {
    outline: none;
    border-color: var(--ef-brand);
    box-shadow: 0 0 0 3px var(--ef-focus);
}
.fc-input.fc-error, .fc-select.fc-error, .fc-textarea.fc-error {
    border-color: #e74c3c !important;
    box-shadow: 0 0 0 3px rgba(231,76,60,.1) !important;
}
.fc-field-hint { font-size: .72rem; color: var(--ef-muted); margin-top: .3rem; }
.fc-field-error { font-size: .72rem; color: #c0392b; margin-top: .3rem; display: flex; align-items: center; gap: .25rem; }
.fc-select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' fill='%237486a4' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right .8rem center;
    padding-right: 2.25rem;
    cursor: pointer;
}

/* ── Label row with quick-add ──────────────────────── */
.fc-label-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: .45rem;
}
.fc-label-row .fc-label { margin-bottom: 0; }
.btn-quick-add {
    background: none; border: none; color: var(--ef-brand);
    font-size: .7rem; font-weight: 600; padding: 0; cursor: pointer;
    display: inline-flex; align-items: center; gap: .2rem;
    transition: color .15s;
}
.btn-quick-add:hover { color: #0d2a4a; text-decoration: underline; }

/* ── Char counter ──────────────────────────────────── */
.fc-input-meta { display: flex; justify-content: space-between; margin-top: .3rem; }
.fc-char-counter { font-size: .7rem; color: var(--ef-muted); }

/* ── Prefix input ──────────────────────────────────── */
.fc-prefix-group { display: flex; }
.fc-prefix {
    display: flex; align-items: center;
    padding: .55rem .8rem;
    font-size: .82rem; font-weight: 600; color: var(--ef-muted);
    background: var(--ef-soft); border: 1px solid var(--ef-border);
    border-right: 0; border-radius: 8px 0 0 8px; white-space: nowrap;
}
.fc-prefix-group .fc-input { border-radius: 0 8px 8px 0; border-left-color: transparent; }
.fc-prefix-group .fc-input:focus { border-left-color: var(--ef-brand); }

/* ════════════════════════════════════════════════════
   DEBIT / KREDIT — Clean Segmented
   ════════════════════════════════════════════════════ */
.seg-wrap {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .75rem;
}
.seg-radio { position: absolute; opacity: 0; width: 0; height: 0; }
.seg-card {
    display: flex;
    align-items: center;
    gap: .9rem;
    padding: .9rem 1.1rem;
    border: 1.5px solid var(--ef-border);
    border-radius: 10px;
    cursor: pointer;
    background: var(--ef-white);
    transition: all .18s ease;
    user-select: none;
    position: relative;
}
.seg-card:hover { border-color: #b0bfcf; background: #f8fafc; }
.seg-icon {
    width: 36px; height: 36px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: .95rem; flex-shrink: 0;
    background: var(--ef-soft); color: var(--ef-muted);
    transition: all .18s;
}
.seg-texts {}
.seg-main { font-size: .88rem; font-weight: 700; color: var(--ef-navy); display: block; transition: color .18s; }
.seg-sub  { font-size: .72rem; color: var(--ef-muted); display: block; }
.seg-check { position: absolute; top: .6rem; right: .75rem; font-size: .7rem; opacity: 0; transition: opacity .18s; }

/* Debit active */
.seg-card.seg-active-debit {
    border-color: #2d6a4f;
    background: #f0faf5;
    box-shadow: 0 0 0 3px rgba(45,106,79,.08);
}
.seg-card.seg-active-debit .seg-icon { background: #d4eddf; color: #155c38; }
.seg-card.seg-active-debit .seg-main { color: #155c38; }
.seg-card.seg-active-debit .seg-check { opacity: 1; color: #2d6a4f; }

/* Kredit active */
.seg-card.seg-active-kredit {
    border-color: #9d2129;
    background: #fdf5f5;
    box-shadow: 0 0 0 3px rgba(157,33,41,.08);
}
.seg-card.seg-active-kredit .seg-icon { background: #f8d7da; color: #7b1d22; }
.seg-card.seg-active-kredit .seg-main { color: #7b1d22; }
.seg-card.seg-active-kredit .seg-check { opacity: 1; color: #9d2129; }

/* ════════════════════════════════════════════════════
   NOMINAL — Compact Power Input
   ════════════════════════════════════════════════════ */
.nominal-wrap {
    display: flex;
    align-items: center;
    gap: 0;
    border: 1px solid var(--ef-border);
    border-radius: 8px;
    overflow: hidden;
    transition: border-color .15s, box-shadow .15s;
    background: var(--ef-white);
}
.nominal-wrap:focus-within {
    border-color: var(--ef-brand);
    box-shadow: 0 0 0 3px var(--ef-focus);
}
.nominal-prefix-box {
    padding: 0 .9rem;
    font-size: .82rem;
    font-weight: 700;
    color: var(--ef-muted);
    background: var(--ef-soft);
    border-right: 1px solid var(--ef-border);
    height: 48px;
    display: flex;
    align-items: center;
    flex-shrink: 0;
    white-space: nowrap;
}
.nominal-input {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    padding: 0 1rem;
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--ef-navy);
    letter-spacing: -.02em;
    font-family: inherit;
    caret-color: var(--ef-brand);
    height: 48px;
    transition: color .18s;
}
.nominal-input::placeholder { color: #c8d0e0; font-weight: 600; font-size: 1.2rem; }
.nominal-input.nominal-debit  { color: #155c38; }
.nominal-input.nominal-kredit { color: #7b1d22; }
.terbilang-helper {
    font-size: .72rem;
    color: var(--ef-muted);
    font-style: italic;
    margin-top: .35rem;
    min-height: .9rem;
}

/* ── Section divider ───────────────────────────────── */
.fc-divider { border: none; border-top: 1px solid var(--ef-border); margin: 1.25rem 0; }

/* ── Tax collapsible ───────────────────────────────── */
.tax-trigger {
    display: flex; align-items: center; gap: .5rem;
    width: 100%; background: #fafbfd; border: none;
    font-family: inherit; font-size: .68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .12em; color: var(--ef-muted);
    cursor: pointer; padding: .75rem 1.5rem;
    border-bottom: 1px solid var(--ef-border);
    transition: color .15s, background .15s; text-align: left;
}
.tax-trigger:hover { color: var(--ef-navy); background: #f5f7fa; }
.tax-icon { font-size: .68rem; }
.tax-badge {
    display: none; background: #dbeafe; color: #1e40af;
    font-size: .6rem; font-weight: 700; padding: .12rem .5rem;
    border-radius: 20px; text-transform: none; letter-spacing: 0;
}

/* ── Upload zone ───────────────────────────────────── */
.fc-upload {
    border: 1.5px dashed var(--ef-border);
    border-radius: 8px; padding: 1.5rem 1rem;
    text-align: center; cursor: pointer;
    background: var(--ef-bg); transition: all .18s;
}
.fc-upload:hover, .fc-upload.drag-over { border-color: #8baed6; background: #f0f5fb; }
.fc-upload-icon { font-size: 1.35rem; color: var(--ef-muted); margin-bottom: .4rem; }
.fc-upload-text { font-size: .82rem; font-weight: 600; color: var(--ef-slate); }
.fc-upload-hint { font-size: .72rem; color: var(--ef-muted); margin-top: .2rem; }
.fc-upload-name {
    margin-top: .5rem; display: none;
    align-items: center; justify-content: center; gap: .35rem;
    background: #e8f2fd; color: #1a5fb4;
    border-radius: 5px; padding: .3rem .75rem;
    font-size: .73rem; font-weight: 600;
}
.fc-file-hidden { display: none; }

/* ── Tom Select Override ───────────────────────────── */
.ts-wrapper { width: 100%; }
.ts-control {
    border: 1px solid var(--ef-border) !important;
    border-radius: 8px !important; box-shadow: none !important;
    padding: .55rem .85rem !important; font-size: .85rem !important;
    color: var(--ef-navy) !important; background: var(--ef-white) !important;
    min-height: unset !important; cursor: pointer !important;
    transition: border-color .15s, box-shadow .15s !important;
}
.ts-control:focus, .ts-wrapper.focus .ts-control {
    border-color: var(--ef-brand) !important;
    box-shadow: 0 0 0 3px var(--ef-focus) !important; outline: none !important;
}
.ts-dropdown {
    border: 1px solid var(--ef-border) !important;
    border-radius: 10px !important;
    box-shadow: 0 8px 28px rgba(0,0,0,.13) !important;
    font-size: .85rem !important; margin-top: 3px !important;
    z-index: 9999 !important;
    position: absolute !important;
}
.ts-dropdown .option { padding: .5rem .85rem !important; }
.ts-dropdown .option:hover, .ts-dropdown .option.active { background: var(--ef-bg) !important; }
.ts-dropdown .option.selected { background: #eef4ff !important; color: var(--ef-brand) !important; font-weight: 600 !important; }
.ts-no-results { padding: .75rem .85rem; font-size: .8rem; color: var(--ef-muted); text-align: center; }
.ts-add-btn {
    display: block; margin: .4rem auto 0; background: none;
    border: 1px dashed var(--ef-border); border-radius: 6px;
    color: var(--ef-brand); font-size: .75rem; font-weight: 600;
    padding: .28rem .8rem; cursor: pointer;
}

/* ── Modal ─────────────────────────────────────────── */
.ef-modal .modal-content { border-radius: 12px; border: 1px solid var(--ef-border); overflow: hidden; }
.ef-modal .modal-header { display: flex; align-items: center; gap: .55rem; padding: .95rem 1.25rem; border-bottom: 1px solid var(--ef-border); background: #fafbfd; }
.ef-modal .modal-header-icon { width: 28px; height: 28px; background: var(--ef-soft); border: 1px solid var(--ef-border); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: .75rem; color: var(--ef-muted); flex-shrink: 0; }
.ef-modal .modal-title-text { font-size: .85rem; font-weight: 700; color: var(--ef-navy); margin: 0; }
.ef-modal .modal-sub { font-size: .7rem; color: var(--ef-muted); margin: 0; }
.ef-modal .modal-close { background: none; border: none; color: var(--ef-muted); font-size: .85rem; cursor: pointer; padding: .2rem; border-radius: 4px; margin-left: auto; transition: all .15s; }
.ef-modal .modal-close:hover { background: var(--ef-soft); color: var(--ef-navy); }
.ef-modal .modal-body { padding: 1.25rem; }
.ef-modal .modal-footer { display: flex; justify-content: flex-end; gap: .5rem; padding: .85rem 1.25rem; border-top: 1px solid var(--ef-border); background: #fafbfd; }

/* ── Toast ─────────────────────────────────────────── */
#ef-toast-container { position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999; display: flex; flex-direction: column; gap: .55rem; pointer-events: none; }
.ef-toast { display: flex; align-items: flex-start; gap: .65rem; min-width: 270px; max-width: 340px; padding: .8rem .9rem; background: #fff; border: 1px solid var(--ef-border); border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,.10); pointer-events: all; opacity: 0; transform: translateY(12px); transition: opacity .22s, transform .22s; }
.ef-toast.show { opacity: 1; transform: translateY(0); }
.ef-toast.hide { opacity: 0; transform: translateY(12px); }
.ef-toast-icon { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: .78rem; flex-shrink: 0; }
.ef-toast-icon.success { background: #dcfce7; color: #15803d; }
.ef-toast-icon.error   { background: #fee2e2; color: #dc2626; }
.ef-toast-icon.info    { background: #e0f2fe; color: #0369a1; }
.ef-toast-body { flex: 1; min-width: 0; }
.ef-toast-title { font-size: .78rem; font-weight: 700; color: var(--ef-navy); margin: 0 0 .1rem; }
.ef-toast-msg   { font-size: .72rem; color: var(--ef-muted); margin: 0; }
.ef-toast-close { background: none; border: none; color: var(--ef-muted); font-size: .78rem; cursor: pointer; padding: 0; opacity: .6; }

/* ── Mobile ─────────────────────────────────────────── */
@media (max-width: 991px) {
    .fc-header { flex-direction: column; align-items: flex-start; gap: 1.25rem; }
}
@media (max-width: 767px) {
    .fc-wrap { padding-bottom: 3rem; }
    .fc-header-actions { width: 100%; flex-direction: column-reverse; gap: .6rem; }
    .fc-header-actions .fc-btn { width: 100%; justify-content: center; padding: .65rem 1rem; }
    .fc-section-body { padding: 1.25rem; }
    .fc-input, .fc-select, .fc-textarea { font-size: 1rem !important; }
    .nominal-input { font-size: 1.25rem; height: 54px; }
    .nominal-prefix-box { height: 54px; }
    .seg-wrap { grid-template-columns: 1fr; }
    .seg-card { padding: .75rem .9rem; }
}

/* ── External Entity Delete ── */
.fc-label-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}
.btn-entity-delete {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
    border-radius: 6px;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-entity-delete:hover {
    background: #fca5a5;
    color: #991b1b;
}
.btn-entity-delete i {
    font-size: 0.95rem;
}
.ts-dropdown .entity-delete-btn:hover {
    background: #fee2e2;
}
</style>
@endpush

@section('content')

<script>
window.FINANCE_TRX_CONFIG = {
    csrf:            '{{ csrf_token() }}',
    entityStoreUrl:  '{{ route("finance.entities.store") }}',
    entityDeleteUrl: '{{ url("finance/entities") }}',
    accountDeleteUrl: '{{ url("finance/accounts") }}',
    accountStoreUrl: '{{ route("finance.accounts.store") }}',
    canDeleteEntity: {{ (
        in_array(strtolower(session('role')), ['master admin', 'super admin', 'finance']) || 
        (Auth::check() && (Auth::user()->isMasterAdmin() || Auth::user()->isFinance()))
    ) ? 'true' : 'false' }}
};
</script>

<div class="fc-wrap">

    {{-- ── Page Header ─────────────────────────────────── --}}
    <div class="fc-header">
        <div class="fc-header-left">
            <div class="fc-breadcrumb">
                <a href="{{ route('finance.transactions.index') }}">Buku Kas</a>
                <span>/</span>
                <span>Transaksi Baru</span>
            </div>
            <h1 class="fc-title">Catat Transaksi Baru</h1>
            <p class="fc-subtitle">Isi semua kolom wajib lalu simpan.</p>
        </div>
        <div class="fc-header-actions">
            <a href="{{ route('finance.transactions.index') }}" class="fc-btn fc-btn-ghost" id="btnBatal">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <button type="submit" form="trxForm" class="fc-btn fc-btn-primary" id="submitBtn">
                <i class="bi bi-check2-circle"></i> Simpan Transaksi
            </button>
        </div>
    </div>

    {{-- ── Error Banner ─────────────────────────────────── --}}
    @if($errors->any())
    <div class="fc-error-banner">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Terdapat kesalahan:</strong>
        <ul class="mb-0 mt-1 ps-4" style="line-height:1.8">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    @if(session('error'))
    <div class="fc-error-banner">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Gagal Menyimpan:</strong> {{ session('error') }}
    </div>
    @endif

    <form action="{{ route('finance.transactions.store') }}" method="POST" id="trxForm" enctype="multipart/form-data">
    @csrf

    {{-- ══ SECTION 1 — Tipe & Nominal ══ --}}
    <div class="fc-section">
        <div class="fc-section-header">
            <div class="fc-section-icon"><i class="bi bi-lightning-charge"></i></div>
            <p class="fc-section-label">Tipe &amp; Nominal</p>
        </div>
        <div class="fc-section-body">

            {{-- Tipe Transaksi --}}
            <div class="fc-field">
                <span class="fc-label">Tipe Transaksi <span class="req">*</span></span>
                <div class="seg-wrap">
                    <input type="radio" name="transaction_type" id="type_debit"
                           value="debit" class="seg-radio"
                           {{ old('transaction_type','debit') === 'debit' ? 'checked':'' }}>
                    <label for="type_debit" class="seg-card">
                        <div class="seg-icon"><i class="bi bi-arrow-down-left-circle-fill"></i></div>
                        <div class="seg-texts">
                            <span class="seg-main">Debit</span>
                            <span class="seg-sub">Uang Masuk / Penerimaan</span>
                        </div>
                        <i class="bi bi-check-circle-fill seg-check"></i>
                    </label>

                    <input type="radio" name="transaction_type" id="type_kredit"
                           value="kredit" class="seg-radio"
                           {{ old('transaction_type') === 'kredit' ? 'checked':'' }}>
                    <label for="type_kredit" class="seg-card">
                        <div class="seg-icon"><i class="bi bi-arrow-up-right-circle-fill"></i></div>
                        <div class="seg-texts">
                            <span class="seg-main">Kredit</span>
                            <span class="seg-sub">Uang Keluar / Pembayaran</span>
                        </div>
                        <i class="bi bi-check-circle-fill seg-check"></i>
                    </label>
                </div>
            </div>

            {{-- Nominal --}}
            <div class="fc-field" style="margin-bottom:0">
                <label class="fc-label" for="amount_display">Nominal <span class="req">*</span></label>
                <div class="nominal-wrap">
                    <div class="nominal-prefix-box">Rp</div>
                    <input type="text" id="amount_display"
                           inputmode="numeric" autocomplete="off"
                           placeholder="0"
                           class="{{ $errors->has('amount') ? 'fc-error' : '' }} nominal-input"
                           value="{{ old('amount') ? number_format(old('amount'), 0, ',', '.') : '' }}">
                </div>
                <input type="hidden" name="amount" id="amount" value="{{ old('amount') }}">
                <p class="terbilang-helper" id="terbilang_helper"></p>
                @error('amount')<p class="fc-field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>@enderror
            </div>

        </div>
    </div>

    {{-- ══ SECTION 2 — Tanggal & Akun ══ --}}
    <div class="fc-section">
        <div class="fc-section-header">
            <div class="fc-section-icon"><i class="bi bi-calendar3"></i></div>
            <p class="fc-section-label">Tanggal &amp; Kategori Akun</p>
        </div>
        <div class="fc-section-body">
            <div class="row g-4 align-items-start">
                <div class="col-lg-4 col-md-5 fc-field">
                    <label class="fc-label" for="transaction_date">Tanggal Transaksi <span class="req">*</span></label>
                    <input type="date" name="transaction_date" id="transaction_date"
                           class="fc-input {{ $errors->has('transaction_date') ? 'fc-error' : '' }}"
                           value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                    @error('transaction_date')<p class="fc-field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>
                <div class="col-lg-8 col-md-7 fc-field">
                    <div class="fc-label-row">
                        <label class="fc-label" for="account_id">Akun / Kategori <span class="opt">(CoA)</span> <span class="req">*</span></label>
                        <div class="fc-label-actions">
                            @if(in_array(strtolower(session('role')), ['master admin', 'super admin', 'finance']) || (Auth::check() && (Auth::user()->isMasterAdmin() || Auth::user()->isFinance())))
                                <button type="button" class="btn-entity-delete" id="btn_delete_account" style="display:none" title="Hapus Akun terpilih" onclick="handleAccountDelete()">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                            <button type="button" class="btn-quick-add" onclick="openAccountModal()">
                                <i class="bi bi-plus-circle-fill"></i> Tambah Baru
                            </button>
                        </div>
                    </div>
                    <select name="account_id" id="account_id"
                            class="fc-select {{ $errors->has('account_id') ? 'fc-error' : '' }}" required>
                        <option value="">— Pilih Kategori Akun —</option>
                        @php $cats = ['asset'=>'Harta','liability'=>'Kewajiban','equity'=>'Modal','revenue'=>'Pendapatan','expense'=>'Biaya']; @endphp
                        @foreach($cats as $cat => $catLabel)
                            @if($accounts->where('category',$cat)->count())
                            <optgroup label="{{ $catLabel }} ({{ ucfirst($cat) }})">
                                @foreach($accounts->where('category',$cat) as $acc)
                                    <option value="{{ $acc->id }}" {{ old('account_id') == $acc->id ? 'selected':'' }}>
                                        [{{ $acc->code }}] {{ $acc->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                            @endif
                        @endforeach
                    </select>
                    @error('account_id')<p class="fc-field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    {{-- ══ SECTION 3 — Keterangan, Entitas & Dokumen ══ --}}
    <div class="fc-section">
        <div class="fc-section-header">
            <div class="fc-section-icon"><i class="bi bi-card-text"></i></div>
            <p class="fc-section-label">Keterangan &amp; Entitas</p>
        </div>
        <div class="fc-section-body">

            <div class="fc-field">
                <label class="fc-label" for="description">Keterangan Transaksi <span class="req">*</span></label>
                <textarea name="description" id="description"
                          class="fc-textarea {{ $errors->has('description') ? 'fc-error' : '' }}"
                          placeholder="Contoh: Bayar sewa kantor April 2026" required>{{ old('description') }}</textarea>
                <div class="fc-input-meta">
                    <p class="fc-field-hint">Deskripsi jelas membantu rekonsiliasi &amp; audit.</p>
                    <span class="fc-char-counter" id="desc_counter">0 / 500</span>
                </div>
                @error('description')<p class="fc-field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>@enderror
            </div>

            <hr class="fc-divider">

            <p class="fc-label" style="font-size:.68rem;text-transform:uppercase;letter-spacing:.1em;color:var(--ef-muted);margin-bottom:.9rem;">
                <i class="bi bi-people me-1"></i>Entitas Terkait <span style="text-transform:none;letter-spacing:0;font-weight:400;">(Opsional)</span>
            </p>
            <div class="row g-3">
                <div class="col-md-6 fc-field">
                    <div class="fc-label-row">
                        <label class="fc-label" for="sender_entity_id">Pengirim <span class="opt">(Dari)</span></label>
                        <div class="fc-label-actions">
                            @if(in_array(strtolower(session('role')), ['master admin', 'super admin', 'finance']) || (Auth::check() && (Auth::user()->isMasterAdmin() || Auth::user()->isFinance())))
                                <button type="button" class="btn-entity-delete" id="btn_delete_sender" style="display:none" title="Hapus Entitas terpilih" onclick="handleExternalDelete('sender')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                            <button type="button" class="btn-quick-add" onclick="openEntityModal('sender')">
                                <i class="bi bi-plus-circle-fill"></i> Tambah Baru
                            </button>
                        </div>
                    </div>
                    <select name="sender_entity_id" id="sender_entity_id"
                            class="fc-select {{ $errors->has('sender_entity_id') ? 'fc-error' : '' }}">
                        <option value="">Tidak ada — lewati</option>
                        @foreach($entities as $ent)
                            <option value="{{ $ent->id }}" {{ old('sender_entity_id') == $ent->id ? 'selected':'' }}>
                                {{ $ent->name }} ({{ ucfirst($ent->type) }})
                            </option>
                        @endforeach
                    </select>
                    @error('sender_entity_id')<p class="fc-field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>
                <div class="col-md-6 fc-field">
                    <div class="fc-label-row">
                        <label class="fc-label" for="receiver_entity_id">Penerima <span class="opt">(Ke)</span></label>
                        <div class="fc-label-actions">
                            @if(in_array(strtolower(session('role')), ['master admin', 'super admin', 'finance']) || (Auth::check() && (Auth::user()->isMasterAdmin() || Auth::user()->isFinance())))
                                <button type="button" class="btn-entity-delete" id="btn_delete_receiver" style="display:none" title="Hapus Entitas terpilih" onclick="handleExternalDelete('receiver')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                            <button type="button" class="btn-quick-add" onclick="openEntityModal('receiver')">
                                <i class="bi bi-plus-circle-fill"></i> Tambah Baru
                            </button>
                        </div>
                    </div>
                    <select name="receiver_entity_id" id="receiver_entity_id"
                            class="fc-select {{ $errors->has('receiver_entity_id') ? 'fc-error' : '' }}">
                        <option value="">Tidak ada — lewati</option>
                        @foreach($entities as $ent)
                            <option value="{{ $ent->id }}" {{ old('receiver_entity_id') == $ent->id ? 'selected':'' }}>
                                {{ $ent->name }} ({{ ucfirst($ent->type) }})
                            </option>
                        @endforeach
                    </select>
                    @error('receiver_entity_id')<p class="fc-field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>@enderror
                </div>
            </div>

            <hr class="fc-divider">

            <p class="fc-label" style="font-size:.68rem;text-transform:uppercase;letter-spacing:.1em;color:var(--ef-muted);margin-bottom:.9rem;">
                <i class="bi bi-paperclip me-1"></i>Lampiran <span style="text-transform:none;letter-spacing:0;font-weight:400;">(Opsional)</span>
            </p>
            <div class="fc-field" style="margin-bottom:0">
                <div class="fc-upload" id="uploadZone" onclick="document.getElementById('document').click()">
                    <div class="fc-upload-icon"><i class="bi bi-cloud-upload"></i></div>
                    <div class="fc-upload-text">Klik atau seret file ke sini</div>
                    <div class="fc-upload-hint">PDF, JPG, PNG — Maks. 5 MB</div>
                    <div class="fc-upload-name" id="uploadName"></div>
                </div>
                <input type="file" name="document" id="document"
                       class="fc-file-hidden {{ $errors->has('document') ? 'fc-error' : '' }}"
                       accept=".pdf,.jpg,.jpeg,.png" onchange="showFileName(this)">
                @error('document')<p class="fc-field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>@enderror
            </div>

        </div>
    </div>

    {{-- ══ SECTION 4 — Pajak (Collapsible) ══ --}}
    <div class="fc-section">
        <button type="button" class="tax-trigger" id="taxCollapseBtn" onclick="toggleTax()">
            <div class="fc-section-icon" style="margin-right:.15rem"><i class="bi bi-receipt-cutoff"></i></div>
            <span id="taxCollapseIcon" class="tax-icon">▸</span>
            Informasi Pajak
            <span style="text-transform:none;letter-spacing:0;font-weight:400;font-size:.68rem;">(Coretax – Opsional)</span>
            <span class="tax-badge" id="taxBadge">● Diisi</span>
        </button>
        <div id="taxSection" style="display:none">
            <div class="fc-section-body">
                <div class="row g-3 mb-0">
                    <div class="col-md-4 fc-field">
                        <label class="fc-label" for="dpp_amount">DPP <span class="opt">(Dasar Pengenaan Pajak)</span></label>
                        <div class="fc-prefix-group">
                            <span class="fc-prefix">Rp</span>
                            <input type="number" name="dpp_amount" id="dpp_amount"
                                   class="fc-input {{ $errors->has('dpp_amount') ? 'fc-error' : '' }}"
                                   value="{{ old('dpp_amount') }}" placeholder="0" min="0" step="any"
                                   oninput="recalcTax()">
                        </div>
                        <p class="fc-field-hint">Nominal sebelum pajak.</p>
                        @error('dpp_amount')<p class="fc-field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>@enderror
                    </div>
                    <div class="col-md-4 fc-field">
                        <label class="fc-label" for="tax_type">Jenis Pajak</label>
                        <select name="tax_type" id="tax_type"
                                class="fc-select {{ $errors->has('tax_type') ? 'fc-error' : '' }}"
                                onchange="recalcTax()">
                            <option value="none" {{ old('tax_type','none') === 'none' ? 'selected':'' }}>— Tidak Ada Pajak —</option>
                            <option value="ppn"          {{ old('tax_type') === 'ppn'          ? 'selected':'' }}>PPN (11%)</option>
                            <option value="pph_21"       {{ old('tax_type') === 'pph_21'       ? 'selected':'' }}>PPh 21 — Karyawan</option>
                            <option value="pph_23"       {{ old('tax_type') === 'pph_23'       ? 'selected':'' }}>PPh 23 — Jasa</option>
                            <option value="pph_4_ayat_2" {{ old('tax_type') === 'pph_4_ayat_2' ? 'selected':'' }}>PPh 4(2) — Sewa</option>
                        </select>
                        @error('tax_type')<p class="fc-field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>@enderror
                    </div>
                    <div class="col-md-4 fc-field">
                        <label class="fc-label" for="tax_amount">Nominal Pajak <span class="opt">(Auto)</span></label>
                        <div class="fc-prefix-group">
                            <span class="fc-prefix">Rp</span>
                            <input type="number" name="tax_amount" id="tax_amount"
                                   class="fc-input {{ $errors->has('tax_amount') ? 'fc-error' : '' }}"
                                   value="{{ old('tax_amount') }}" placeholder="Otomatis" min="0" step="any">
                        </div>
                        <p class="fc-field-hint">Otomatis dihitung, bisa diedit.</p>
                        @error('tax_amount')<p class="fc-field-error"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- ── PPh Auto-Transaction Notice ── --}}
                <div id="pphAutoNotice" style="display:none;margin-top:.85rem;">
                    <div style="
                        display:flex;align-items:flex-start;gap:.65rem;
                        background:#eff6ff;border:1px solid #bfdbfe;border-left:3px solid #3b82f6;
                        border-radius:8px;padding:.75rem 1rem;font-size:.78rem;color:#1e40af;
                    ">
                        <i class="bi bi-info-circle-fill" style="font-size:.9rem;flex-shrink:0;margin-top:.05rem;"></i>
                        <div>
                            <div style="font-weight:700;margin-bottom:.2rem;">Transaksi PPh Otomatis</div>
                            <div style="color:#3b82f6;">
                                Sistem akan otomatis membuat transaksi <strong>Kredit</strong> ke akun
                                <strong>Utang PPh (2100)</strong> senilai
                                <strong id="pphNoticeAmount">—</strong> saat disimpan.
                                Nilai ini akan masuk ke <strong>Total Uang Keluar</strong> &amp; laporan keuangan.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </form>

</div>

<div id="ef-toast-container"></div>

{{-- ── MODAL: Quick Add Entity ──────────────────────────── --}}
<div class="modal fade ef-modal" id="quickEntityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-icon"><i class="bi bi-person-plus"></i></div>
                <div>
                    <p class="modal-title-text">Tambah Entitas Cepat</p>
                    <p class="modal-sub">Entitas tersimpan &amp; siap dipilih</p>
                </div>
                <button type="button" class="modal-close" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
            </div>
            <form id="quickEntityForm">
                <div class="modal-body">
                    <input type="hidden" id="target_dropdown" value="">
                    <div class="fc-field">
                        <label class="fc-label" for="entity_name">Nama Entitas <span class="req">*</span></label>
                        <input type="text" id="entity_name" name="name" class="fc-input" placeholder="Contoh: PT Sumber Rejeki" required autofocus>
                    </div>
                    <div class="fc-field">
                        <label class="fc-label" for="entity_type">Tipe <span class="req">*</span></label>
                        <select id="entity_type" name="type" class="fc-select" required>
                            <option value="vendor">Vendor / Supplier</option>
                            <option value="bank">Bank</option>
                            <option value="client">Client / Pelanggan</option>
                            <option value="internal">Internal</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    <div class="fc-field" style="margin-bottom:0">
                        <label class="fc-label" for="entity_contact">Kontak <span class="opt">(Opsional)</span></label>
                        <input type="text" id="entity_contact" name="contact_info" class="fc-input" placeholder="Email / No. HP">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="fc-btn fc-btn-ghost" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="fc-btn fc-btn-primary" id="entitySubmitBtn">
                        <i class="bi bi-check2"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── MODAL: Quick Add Account ─────────────────────────── --}}
<div class="modal fade ef-modal" id="quickAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-icon"><i class="bi bi-folder-plus"></i></div>
                <div>
                    <p class="modal-title-text">Tambah Akun CoA</p>
                    <p class="modal-sub">Akun langsung bisa dipilih setelah disimpan</p>
                </div>
                <button type="button" class="modal-close" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
            </div>
            <form id="quickAccountForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-4 fc-field">
                            <label class="fc-label" for="account_code">Kode <span class="req">*</span></label>
                            <input type="text" id="account_code" class="fc-input" placeholder="1001" required>
                        </div>
                        <div class="col-8 fc-field">
                            <label class="fc-label" for="account_name">Nama Akun <span class="req">*</span></label>
                            <input type="text" id="account_name" class="fc-input" placeholder="Kas Operasional" required>
                        </div>
                    </div>
                    <div class="fc-field" style="margin-bottom:0">
                        <label class="fc-label" for="account_category">Kategori <span class="req">*</span></label>
                        <select id="account_category" class="fc-select" required>
                            <option value="asset">Harta (Asset)</option>
                            <option value="liability">Kewajiban (Liability)</option>
                            <option value="equity">Modal (Equity)</option>
                            <option value="revenue">Pendapatan (Revenue)</option>
                            <option value="expense">Biaya (Expense)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="fc-btn fc-btn-ghost" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="fc-btn fc-btn-primary" id="accountSubmitBtn">
                        <i class="bi bi-check2"></i> Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script src="{{ asset('js/finance-transaction-create.js') }}?v={{ time() }}"></script>
@endpush
