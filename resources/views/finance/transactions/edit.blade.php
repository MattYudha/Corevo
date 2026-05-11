@extends('layouts.dashboard')

@section('title', 'Edit Transaksi Kas')

@push('styles')
<style>
.form-hero {
    background: linear-gradient(135deg,#1a1f3c 0%,#2d3561 100%);
    border-radius: 16px; padding: 1.2rem 1.6rem; margin-bottom: 1.25rem;
}
.form-hero .fh-title { font-size:1rem; font-weight:800; color:#fff; margin:0; }
.form-hero .fh-sub   { font-size:.74rem; color:rgba(255,255,255,.5); margin:.2rem 0 0; }
.form-section-title {
    font-size:.68rem; font-weight:800; letter-spacing:.09em; text-transform:uppercase;
    color:#8392ab; border-left:3px solid #fb6340; padding-left:.65rem; margin-bottom:1rem;
}
.fin-label { font-size:.78rem; font-weight:700; color:#344767; margin-bottom:.4rem; display:block; }
.fin-input {
    border-radius:10px; border:1.5px solid #e4e8f0; font-size:.85rem;
    padding:.6rem .9rem; transition:border-color .15s, box-shadow .15s; width:100%; background:#fff;
}
.fin-input:focus { border-color:#5e72e4; box-shadow:0 0 0 3px rgba(94,114,228,.12); outline:none; }
.fin-input.is-invalid { border-color:#f5365c; }

/* Debit/Kredit toggle */
.type-toggle { display:flex; gap:.6rem; }
.type-radio  { display:none; }
.type-btn {
    flex:1; padding:.75rem 1.2rem; border-radius:12px; cursor:pointer;
    border:2px solid #e4e8f0; background:#fff; transition:all .15s;
    display:flex; align-items:center; justify-content:center; gap:.6rem;
    font-size:.85rem; font-weight:700; color:#8392ab; user-select:none;
}
.type-btn .tb-icon  { font-size:1.3rem; }
.type-btn .tb-label small { font-size:.65rem; font-weight:500; display:block; }
.type-radio[value="debit"]:checked  + .type-btn { border-color:#1aae6f; background:#e2faf0; color:#1aae6f; box-shadow:0 0 0 3px rgba(26,174,111,.12); }
.type-radio[value="kredit"]:checked + .type-btn { border-color:#f5365c; background:#fce8e8; color:#f5365c; box-shadow:0 0 0 3px rgba(245,54,92,.12); }
.type-btn:hover { border-color:#adb5bd; background:#f8f9fa; }

.amount-preview {
    background:#f4f6fb; border-radius:10px; padding:.65rem 1rem;
    font-size:1.3rem; font-weight:900; text-align:center;
    border:1.5px solid #e4e8f0; transition:color .2s, background .2s;
}
.amount-preview.debit  { color:#1aae6f; background:#e6fbf2; border-color:#b0f0d4; }
.amount-preview.kredit { color:#f5365c; background:#fce8e8; border-color:#f5bfc8; }

/* Edit warning badge */
.edit-warn {
    background:#fff4de; border:1.5px solid #f5dfa0; border-radius:10px;
    padding:.75rem 1.1rem; margin-bottom:1.25rem;
    display:flex; align-items:center; gap:.75rem;
}
.edit-warn .ew-icon { font-size:1.3rem; flex-shrink:0; }
.edit-warn p { margin:0; font-size:.8rem; color:#856404; }
/* Quick Add Button */
.btn-quick-add {
    background: none; border: none; color: #5e72e4; font-size: .65rem;
    font-weight: 700; padding: 0; transition: color .15s;
    display: inline-flex; align-items: center; gap: .2rem;
}
.btn-quick-add:hover { color: #233dd2; text-decoration: underline; }
/* Upload Zone */
.ef-upload-zone {
    border:2px dashed #dce1ec; border-radius:8px; padding:1.6rem 1rem;
    text-align:center; cursor:pointer; background:#f5f7fa; transition:all .2s;
}
.ef-upload-zone:hover,.ef-upload-zone.drag-over { border-color:#8baed6; background:#f0f5fb; }
.ef-upload-icon { font-size:1.5rem; color:#8392ab; margin-bottom:.3rem; }
.ef-upload-text { font-size:.83rem; font-weight:600; color:#3d4e6c; }
.ef-upload-hint { font-size:.7rem; color:#8392ab; margin-top:.2rem; }
.ef-upload-name {
    margin-top:.5rem; display:inline-flex; align-items:center; gap:.4rem;
    background:#e8f2fd; color:#1a5fb4; border-radius:5px;
    padding:.3rem .75rem; font-size:.75rem; font-weight:600;
}
.ef-file-hidden { display:none; }
.existing-doc {
    display:flex; align-items:center; gap:.5rem;
    padding:.6rem 1rem; background:#eef7f2; border:1px solid #b0f0d4;
    border-radius:7px; font-size:.8rem; font-weight:600; color:#1aae6f;
    margin-bottom:.5rem;
}
</style>
@endpush

@section('content')

<div class="form-hero shadow" style="background:linear-gradient(135deg,#2d1f0a 0%,#7d4e1a 60%,#8b3a1f 100%)">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <p class="fh-title">✏️ Edit Transaksi Kas</p>
            <p class="fh-sub">Ubah data transaksi — saldo running akan dihitung ulang otomatis</p>
        </div>
        <a href="{{ route('finance.transactions.index') }}" class="btn btn-sm mb-0"
           style="background:rgba(255,255,255,.15);color:#fff;border-radius:8px;font-size:.78rem;padding:.4rem 1rem;border:1px solid rgba(255,255,255,.25)">
            ← Kembali ke Ledger
        </a>
    </div>
</div>

{{-- Edit warning --}}
<div class="edit-warn">
    <div class="ew-icon">⚠️</div>
    <div>
        <p><strong>Perhatian:</strong> Setiap perubahan pada transaksi ini akan memicu kalkulasi ulang saldo running balance seluruh transaksi yang ada.</p>
    </div>
</div>

@if($errors->any())
    <div class="alert mb-3 text-white py-2" style="background:#f5365c;border-radius:10px;font-size:.84rem">
        <i class="bi bi-exclamation-circle-fill me-1"></i>
        <strong>Periksa kembali isian:</strong>
        <ul class="mb-0 mt-1 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

@if(session('error'))
    <div class="alert mb-3 text-white py-2" style="background:#f5365c;border-radius:10px;font-size:.84rem">
        <i class="bi bi-exclamation-circle-fill me-1"></i>
        <strong>Gagal Memperbarui:</strong> {{ session('error') }}
    </div>
@endif

<div class="row g-3">
    {{-- ── FORM ──────── --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden">
            <div style="height:3px;background:linear-gradient(90deg,#fb6340,#ffd600,#1aae6f)"></div>
            <div class="card-body p-4">
                <form action="{{ route('finance.transactions.update', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    {{-- ① Tipe --}}
                    <p class="form-section-title">① Tipe Transaksi</p>
                    <div class="type-toggle mb-4">
                        @php $currentType = old('transaction_type', $transaction->transaction_type); @endphp
                        <input type="radio" name="transaction_type" id="type_debit" value="debit"
                               class="type-radio" {{ $currentType === 'debit' ? 'checked':'' }}>
                        <label for="type_debit" class="type-btn">
                            <span class="tb-icon">📥</span>
                            <span class="tb-label">DEBIT<small>Uang Masuk / Penerimaan</small></span>
                        </label>
                        <input type="radio" name="transaction_type" id="type_kredit" value="kredit"
                               class="type-radio" {{ $currentType === 'kredit' ? 'checked':'' }}>
                        <label for="type_kredit" class="type-btn">
                            <span class="tb-icon">📤</span>
                            <span class="tb-label">KREDIT<small>Uang Keluar / Pembayaran</small></span>
                        </label>
                    </div>

                    {{-- ② Tanggal & Akun --}}
                    <p class="form-section-title">② Tanggal & Akun</p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-5">
                            <label class="fin-label" for="transaction_date">Tanggal Transaksi <span class="text-danger">*</span></label>
                            <input type="date" name="transaction_date" id="transaction_date"
                                   class="fin-input @error('transaction_date') is-invalid @enderror"
                                   value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-7">
                            <label class="fin-label" for="account_id">Akun / CoA <span class="text-danger">*</span></label>
                            <select name="account_id" id="account_id" class="fin-input @error('account_id') is-invalid @enderror" required>
                                <option value="">— Pilih Akun —</option>
                                @php $cats = ['asset'=>'Harta','liability'=>'Kewajiban','equity'=>'Modal','revenue'=>'Pendapatan','expense'=>'Biaya']; @endphp
                                @foreach($cats as $cat => $label)
                                    @if($accounts->where('category',$cat)->count())
                                    <optgroup label="{{ $label }} ({{ ucfirst($cat) }})">
                                        @foreach($accounts->where('category',$cat) as $acc)
                                            <option value="{{ $acc->id }}" {{ old('account_id', $transaction->account_id) == $acc->id ? 'selected':'' }}>
                                                [{{ $acc->code }}] {{ $acc->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- ③ Nominal --}}
                    <p class="form-section-title">③ Nominal</p>
                    <div class="mb-4">
                        <label class="fin-label" for="amount">Jumlah (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group mb-2">
                            <span class="input-group-text fw-bold" style="background:#f4f6fb;border:1.5px solid #e4e8f0;border-right:0;border-radius:10px 0 0 10px;font-size:.85rem;color:#344767">Rp</span>
                            <input type="number" name="amount" id="amount"
                                   class="fin-input @error('amount') is-invalid @enderror"
                                   style="border-left:0;border-radius:0 10px 10px 0"
                                   value="{{ old('amount', $transaction->amount) }}"
                                   min="0" step="any" required
                                   oninput="updateAmountPreview(this.value)">
                        </div>
                        <div class="amount-preview {{ $currentType }}" id="amountPreview">
                            Rp {{ number_format(old('amount', $transaction->amount),0,',','.') }}
                        </div>
                    </div>

                    {{-- ④ Keterangan --}}
                    <p class="form-section-title">④ Keterangan</p>
                    <div class="mb-4">
                        <label class="fin-label" for="description">Deskripsi / Keterangan <span class="text-danger">*</span></label>
                        <input type="text" name="description" id="description"
                               class="fin-input @error('description') is-invalid @enderror"
                               value="{{ old('description', $transaction->description) }}" required>
                    </div>

                    {{-- ⑤ Entitas --}}
                    <p class="form-section-title">⑤ Entitas (Opsional)</p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="fin-label d-flex justify-content-between align-items-center">
                                <span>Dari (Pengirim)</span>
                                <button type="button" class="btn-quick-add" onclick="openEntityModal('sender')">
                                    <i class="bi bi-plus-circle-fill"></i> Tambah Baru
                                </button>
                            </label>
                            <select name="sender_entity_id" id="sender_entity_id" class="fin-input">
                                <option value="">— Tidak Ada —</option>
                                @foreach($entities as $ent)
                                    <option value="{{ $ent->id }}" {{ old('sender_entity_id', $transaction->sender_entity_id) == $ent->id ? 'selected':'' }}>
                                        {{ $ent->name }} ({{ ucfirst($ent->type) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="fin-label d-flex justify-content-between align-items-center">
                                <span>Ke (Penerima)</span>
                                <button type="button" class="btn-quick-add" onclick="openEntityModal('receiver')">
                                    <i class="bi bi-plus-circle-fill"></i> Tambah Baru
                                </button>
                            </label>
                            <select name="receiver_entity_id" id="receiver_entity_id" class="fin-input">
                                <option value="">— Tidak Ada —</option>
                                @foreach($entities as $ent)
                                    <option value="{{ $ent->id }}" {{ old('receiver_entity_id', $transaction->receiver_entity_id) == $ent->id ? 'selected':'' }}>
                                        {{ $ent->name }} ({{ ucfirst($ent->type) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- ⑥ Pajak Coretax --}}
                    <p class="form-section-title">⑥ Informasi Pajak <small style="text-transform:none;font-weight:400;font-size:.7rem">(Coretax – Opsional)</small></p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="fin-label" for="dpp_amount">DPP (Dasar Pengenaan Pajak)</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold" style="background:#f4f6fb;border:1.5px solid #e4e8f0;border-right:0;border-radius:10px 0 0 10px;font-size:.85rem;color:#344767">Rp</span>
                                <input type="number" name="dpp_amount" id="dpp_amount"
                                       class="fin-input {{ $errors->has('dpp_amount') ? 'is-invalid' : '' }}"
                                       style="border-left:0;border-radius:0 10px 10px 0"
                                       value="{{ old('dpp_amount', $transaction->dpp_amount) }}"
                                       placeholder="0" min="0" step="any" oninput="recalcTax()">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="fin-label" for="tax_type">Jenis Pajak</label>
                            <select name="tax_type" id="tax_type" class="fin-input {{ $errors->has('tax_type') ? 'is-invalid' : '' }}" onchange="recalcTax()">
                                <option value="none" {{ old('tax_type', $transaction->tax_type ?? 'none') === 'none' ? 'selected':'' }}>— Tidak Ada —</option>
                                <option value="ppn" {{ old('tax_type', $transaction->tax_type) === 'ppn' ? 'selected':'' }}>PPN (11%)</option>
                                <option value="pph_21" {{ old('tax_type', $transaction->tax_type) === 'pph_21' ? 'selected':'' }}>PPh 21</option>
                                <option value="pph_23" {{ old('tax_type', $transaction->tax_type) === 'pph_23' ? 'selected':'' }}>PPh 23</option>
                                <option value="pph_4_ayat_2" {{ old('tax_type', $transaction->tax_type) === 'pph_4_ayat_2' ? 'selected':'' }}>PPh 4 Ayat 2</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="fin-label" for="tax_amount">Nominal Pajak</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold" style="background:#f4f6fb;border:1.5px solid #e4e8f0;border-right:0;border-radius:10px 0 0 10px;font-size:.85rem;color:#344767">Rp</span>
                                <input type="number" name="tax_amount" id="tax_amount"
                                       class="fin-input {{ $errors->has('tax_amount') ? 'is-invalid' : '' }}"
                                       style="border-left:0;border-radius:0 10px 10px 0"
                                       value="{{ old('tax_amount', $transaction->tax_amount) }}"
                                       placeholder="Auto / Manual" min="0" step="any">
                            </div>
                        </div>
                    </div>

                    {{-- ── PPh Auto-Transaction Notice ── --}}
                    <div id="pphAutoNotice" style="display:{{ (in_array(old('tax_type', $transaction->tax_type), ['pph_21','pph_23','pph_4_ayat_2']) && old('tax_amount', $transaction->tax_amount) > 0) ? 'block' : 'none' }};margin-bottom:1.5rem;">
                        <div style="
                            display:flex;align-items:flex-start;gap:.65rem;
                            background:#eff6ff;border:1px solid #bfdbfe;border-left:3px solid #3b82f6;
                            border-radius:8px;padding:.75rem 1rem;font-size:.78rem;color:#1e40af;
                        ">
                            <i class="bi bi-info-circle-fill" style="font-size:.9rem;flex-shrink:0;margin-top:.05rem;"></i>
                            <div>
                                <div style="font-weight:700;margin-bottom:.2rem;">Transaksi PPh Otomatis</div>
                                <div style="color:#3b82f6;">
                                    Sistem akan otomatis menyinkronkan transaksi <strong>Kredit</strong> ke akun
                                    <strong>Utang PPh (2100)</strong> senilai
                                    <strong id="pphNoticeAmount">Rp {{ number_format(old('tax_amount', $transaction->tax_amount), 0, ',', '.') }}</strong> saat disimpan.
                                    Nilai ini akan masuk ke <strong>Total Uang Keluar</strong> &amp; laporan keuangan.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ⑦ Lampiran Dokumen --}}
                    <p class="form-section-title">⑦ Lampiran Dokumen <small style="text-transform:none;font-weight:400;font-size:.7rem">(Bukti Transfer / Faktur)</small></p>
                    <div class="mb-4">
                        @if($transaction->document_path)
                            <div class="existing-doc">
                                <i class="bi bi-file-earmark-check-fill"></i>
                                Dokumen terlampir:
                                <a href="{{ route('finance.transactions.document', $transaction->id) }}" target="_blank" class="ms-1">Lihat / Unduh</a>
                            </div>
                            <div class="form-check mb-2">
                                <input type="checkbox" name="remove_document" id="remove_document" value="1" class="form-check-input">
                                <label class="form-check-label text-sm text-danger" for="remove_document">Hapus dokumen terlampir</label>
                            </div>
                        @endif
                        <div class="ef-upload-zone" id="uploadZone" onclick="document.getElementById('document').click()">
                            <div class="ef-upload-icon"><i class="bi bi-cloud-upload"></i></div>
                            <div class="ef-upload-text">{{ $transaction->document_path ? 'Klik untuk ganti dokumen' : 'Klik untuk pilih file' }}</div>
                            <div class="ef-upload-hint">Format: PDF, JPG, PNG — Maks. 5 MB</div>
                            <div class="ef-upload-name" id="uploadName" style="display:none"></div>
                        </div>
                        <input type="file" name="document" id="document" class="ef-file-hidden"
                               accept=".pdf,.jpg,.jpeg,.png" onchange="showFileName(this)">
                        @error('document')<p class="text-danger" style="font-size:.75rem;margin-top:.3rem">{{ $message }}</p>@enderror
                    </div>

                    {{-- ⑧ Period marker --}}
                    <p class="form-section-title">⑧ Penanda Periode</p>
                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   id="is_end_of_month" name="is_end_of_month" value="1"
                                   {{ old('is_end_of_month', $transaction->is_end_of_month) ? 'checked':'' }}>
                            <label class="form-check-label text-sm" for="is_end_of_month">🗓 Akhir Bulan</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   id="is_end_of_year" name="is_end_of_year" value="1"
                                   {{ old('is_end_of_year', $transaction->is_end_of_year) ? 'checked':'' }}>
                            <label class="form-check-label text-sm" for="is_end_of_year">📅 Akhir Tahun</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('finance.transactions.index') }}" class="btn btn-outline-secondary mb-0" style="border-radius:9px">Batal</a>
                        <button type="submit" class="btn mb-0 px-4 text-white" style="border-radius:9px;background:#fb6340">
                            <i class="bi bi-save me-1"></i>Perbarui Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── SIDEBAR ────── --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3" style="border-radius:14px;overflow:hidden">
            <div style="height:3px;background:linear-gradient(90deg,#fb6340,#ffd600)"></div>
            <div class="card-body p-3">
                <p class="text-xs fw-bold mb-3" style="color:#8392ab;letter-spacing:.08em;text-transform:uppercase">📋 Data Transaksi Saat Ini</p>
                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                    <span class="text-xs text-muted">ID Transaksi</span>
                    <span class="fw-bold text-xs" style="color:#344767">#{{ $transaction->id }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                    <span class="text-xs text-muted">Tipe</span>
                    <span class="badge" style="background:{{ $transaction->transaction_type==='debit'?'#e2faf0':'#fce8e8' }};color:{{ $transaction->transaction_type==='debit'?'#1aae6f':'#f5365c' }};border-radius:6px;font-size:.68rem;font-weight:800">
                        {{ strtoupper($transaction->transaction_type) }}
                    </span>
                </div>
                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                    <span class="text-xs text-muted">Nominal Asal</span>
                    <span class="fw-bold text-sm" style="color:#344767">Rp {{ number_format($transaction->amount,0,',','.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                    <span class="text-xs text-muted">Saldo Running</span>
                    <span class="fw-bold text-sm" style="color:{{ $transaction->running_balance < 0 ? '#f5365c' : '#5e72e4' }}">
                        Rp {{ number_format($transaction->running_balance,0,',','.') }}
                    </span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-xs text-muted">Dibuat oleh</span>
                    <span class="text-xs fw-bold" style="color:#344767">{{ $transaction->creator->name ?? 'System' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── MODAL QUICK ADD ENTITY ──────────────────── --}}
<div class="modal fade" id="quickEntityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color:#1a1f3c;font-size:1rem">Tambah Entitas Cepat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickEntityForm">
                <div class="modal-body p-4">
                    <input type="hidden" id="target_dropdown" value="">
                    
                    <div class="mb-3">
                        <label class="fin-label">Nama Entitas <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="entity_name" class="fin-input" placeholder="Contoh: PT Sumber Rejeki" required>
                    </div>

                    <div class="mb-3">
                        <label class="fin-label">Tipe Entitas <span class="text-danger">*</span></label>
                        <select name="type" id="entity_type" class="fin-input" required>
                            <option value="vendor">Vendor / Supplier</option>
                            <option value="bank">Bank</option>
                            <option value="client">Client / Pelanggan</option>
                            <option value="internal">Internal Perusahaan</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="fin-label">Info Kontak <span class="opt">(Opsional)</span></label>
                        <input type="text" name="contact_info" id="entity_contact" class="fin-input" placeholder="Email / No HP">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn text-xs fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn px-4 text-white text-xs fw-bold" style="background:#fb6340; border-radius:8px">
                        <i class="bi bi-save me-1"></i> Simpan Entitas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function updateAmountPreview(val) {
    const preview = document.getElementById('amountPreview');
    const num = parseFloat(val) || 0;
    preview.textContent = 'Rp ' + num.toLocaleString('id-ID');
    const t = document.querySelector('input[name="transaction_type"]:checked');
    preview.className = 'amount-preview ' + (t ? t.value : '');
}
document.querySelectorAll('input[name="transaction_type"]').forEach(r => {
    r.addEventListener('change', () => updateAmountPreview(document.getElementById('amount').value));
});

const TAX_RATES = { ppn: 0.11, pph_21: 0.05, pph_23: 0.02, pph_4_ayat_2: 0.1 };
const DEDUCTION_TYPES = ['pph_21', 'pph_23', 'pph_4_ayat_2'];
const PPH_TYPES = ['pph_21', 'pph_23', 'pph_4_ayat_2'];

function recalcTax() {
    // Strip titik ribuan sebelum parse agar "10.000.000" → 10000000 (bukan 10)
    const rawDpp = (document.getElementById('dpp_amount').value || '').replace(/\./g, '').replace(',', '.');
    const dpp   = parseFloat(rawDpp) || 0;
    const type  = document.getElementById('tax_type').value;
    const taxEl = document.getElementById('tax_amount');
    const amtEl = document.getElementById('amount');
    
    if (type === 'none' || dpp === 0) {
        updatePphNotice(0, false);
        return;
    }
    
    const tax = Math.round(dpp * (TAX_RATES[type] || 0));
    taxEl.value = tax;
    const total = DEDUCTION_TYPES.includes(type) ? dpp - tax : dpp + tax;
    amtEl.value = total;
    updateAmountPreview(total);
    
    updatePphNotice(tax, PPH_TYPES.includes(type));
}

function updatePphNotice(taxAmount, show) {
    const notice     = document.getElementById('pphAutoNotice');
    const amountSpan = document.getElementById('pphNoticeAmount');
    if (!notice) return;
    if (show && taxAmount > 0) {
        notice.style.display = 'block';
        if (amountSpan) {
            amountSpan.textContent = 'Rp ' + Number(taxAmount).toLocaleString('id-ID');
        }
    } else {
        notice.style.display = 'none';
    }
}
function showFileName(input) {
    const nameEl = document.getElementById('uploadName');
    const zone   = document.getElementById('uploadZone');
    if (input.files && input.files[0]) {
        nameEl.innerHTML = '<i class="bi bi-file-earmark-check"></i> ' + input.files[0].name;
        nameEl.style.display = 'inline-flex';
        zone.style.borderColor = '#2d6a4f';
        zone.style.background  = '#eef7f2';
    }
}
const zone = document.getElementById('uploadZone');
if (zone) {
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
    zone.addEventListener('drop', e => {
        e.preventDefault(); zone.classList.remove('drag-over');
        const f = document.getElementById('document'); f.files = e.dataTransfer.files; showFileName(f);
    });
}

/* ── Quick Add Entity Logic ─────────────────── */
const quickModal = new bootstrap.Modal(document.getElementById('quickEntityModal'));

function openEntityModal(target) {
    document.getElementById('target_dropdown').value = target;
    document.getElementById('quickEntityForm').reset();
    quickModal.show();
}

document.getElementById('quickEntityForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    const target = document.getElementById('target_dropdown').value;
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>...';

    const formData = {
        _token: '{{ csrf_token() }}',
        name: document.getElementById('entity_name').value,
        type: document.getElementById('entity_type').value,
        contact_info: document.getElementById('entity_contact').value
    };

    fetch('{{ route("finance.entities.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            const newOption = new Option(`${result.data.name} (${result.data.type})`, result.data.id, true, true);
            document.getElementById('sender_entity_id').add(new Option(newOption.text, newOption.value));
            document.getElementById('receiver_entity_id').add(new Option(newOption.text, newOption.value));
            document.getElementById(`${target}_entity_id`).value = result.data.id;
            quickModal.hide();
            alert('Entitas baru ditambahkan!');
        }
    })
    .catch(error => alert('Galat saat menyimpan.'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});
</script>
@endpush
