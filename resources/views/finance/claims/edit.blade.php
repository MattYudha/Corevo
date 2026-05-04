@extends('layouts.dashboard')

@section('title', 'Edit Klaim Biaya')

@push('styles')
<style>
.form-hero {
    background: linear-gradient(135deg,#1a1f3c 0%,#2d3561 100%);
    border-radius: 16px; padding: 1.2rem 1.6rem; margin-bottom: 1.25rem;
}
.form-hero .fh-title { font-size:1rem; font-weight:800; color:#fff; margin:0; }
.form-hero .fh-sub   { font-size:.74rem; color:rgba(255,255,255,.5); margin:.2rem 0 0; }

.fin-label { font-size:.78rem; font-weight:700; color:#344767; margin-bottom:.4rem; display:block; }
.fin-input {
    border-radius:10px; border:1.5px solid #e4e8f0; font-size:.85rem;
    padding:.6rem .9rem; transition:border-color .15s, box-shadow .15s; width:100%;
}
.fin-input:focus { border-color:#5e72e4; box-shadow:0 0 0 3px rgba(94,114,228,.12); outline:none; }

.cat-card-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(140px, 1fr)); gap:.6rem; }
.cat-card-opt { display:none; }
.cat-card-lbl {
    display:flex; flex-direction:column; align-items:center; gap:.3rem;
    border:2px solid #e4e8f0; border-radius:11px; padding:.9rem .5rem;
    cursor:pointer; transition:all .15s; text-align:center; background:#fff;
}
.cat-card-lbl .cc-icon { font-size:1.6rem; }
.cat-card-lbl .cc-name { font-size:.78rem; font-weight:800; color:#344767; }
.cat-card-opt:checked + .cat-card-lbl { border-color:#5e72e4; background:#f0f2ff; box-shadow:0 0 0 3px rgba(94,114,228,.12); }

/* Quick Add Button */
.btn-quick-add {
    background: none; border: none; color: #5e72e4; font-size: .68rem;
    font-weight: 700; padding: 0; transition: color .15s;
    display: inline-flex; align-items: center; gap: .25rem;
}
.btn-quick-add:hover { color: #233dd2; text-decoration: underline; }
</style>
@endpush

@section('content')

<div class="form-hero shadow">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <p class="fh-title">✏️ Edit Klaim Biaya</p>
            <p class="fh-sub">Perbarui informasi klaim biaya operasional Anda</p>
        </div>
        <a href="{{ route('finance.claims.index') }}" class="btn btn-sm mb-0"
           style="background:rgba(255,255,255,.15);color:#fff;border-radius:8px;font-size:.78rem;padding:.4rem 1rem;border:1px solid rgba(255,255,255,.25)">
            ← Kembali
        </a>
    </div>
</div>

@if($errors->any())
    <div class="alert mb-3 text-white py-2" style="background:#f5365c;border-radius:10px;font-size:.84rem">
        <i class="bi bi-exclamation-circle-fill me-1"></i>
        <strong>Mohon periksa kembali inputan Anda:</strong>
        <ul class="mb-0 mt-1" style="font-size:.80rem">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm" style="border-radius:14px">
            <div class="card-body p-4">
                <form action="{{ route('finance.claims.update', $claim->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="fin-label">Kategori Klaim <span class="text-danger">*</span></label>
                        <div class="cat-card-grid">
                            @php
                                $categories = [
                                    'transport'   => ['🚗', 'Transport'],
                                    'meals'       => ['🍱', 'Makan'],
                                    'operational' => ['⚙️', 'Operasional'],
                                    'equipment'   => ['📦', 'Peralatan'],
                                    'other'       => ['❓', 'Lainnya'],
                                ];
                            @endphp
                            @foreach($categories as $slug => $data)
                            <div class="cat-item">
                                <input type="radio" name="category" id="cat_{{ $slug }}" value="{{ $slug }}" class="cat-card-opt" required {{ old('category', $claim->category) == $slug ? 'checked' : '' }}>
                                <label for="cat_{{ $slug }}" class="cat-card-lbl w-100">
                                    <span class="cc-icon">{{ $data[0] }}</span>
                                    <span class="cc-name">{{ $data[1] }}</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="fin-label" for="title">Judul Klaim <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="fin-input @error('title') is-invalid @enderror" value="{{ old('title', $claim->title) }}" placeholder="Contoh: Transport Meeting Klien" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="fin-label" for="amount">Nominal (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" id="amount" class="fin-input @error('amount') is-invalid @enderror" value="{{ old('amount', $claim->amount) }}" placeholder="0" required>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="fin-label d-flex justify-content-between align-items-center" for="account_id">
                            <span>Akun Biaya <span class="text-danger">*</span></span>
                            @if(auth()->user()->isMasterAdmin() || auth()->user()->isFinance())
                            <button type="button" class="btn-quick-add" onclick="quickModal.show()">
                                <i class="bi bi-plus-circle-fill"></i> Tambah Baru
                            </button>
                            @endif
                        </label>
                        <select name="account_id" id="account_id" class="fin-input @error('account_id') is-invalid @enderror" required>
                            <option value="">— Pilih Akun Biaya —</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ old('account_id', $claim->account_id) == $acc->id ? 'selected' : '' }}>
                                    [{{ $acc->code }}] {{ $acc->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-muted mt-1">Pilih kategori biaya yang paling sesuai dengan pengajuan Anda.</p>
                        @error('account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="fin-label" for="description">Deskripsi Lengkap</label>
                        <textarea name="description" id="description" rows="3" class="fin-input" placeholder="Jelaskan detail pengeluaran...">{{ old('description', $claim->description) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="fin-label" for="attachment">Bukti Pendukung (Foto Struk/PDF) <span class="text-muted fw-normal">(Biarkan kosong jika tidak diubah)</span></label>
                        <input type="file" name="attachment" id="attachment" class="fin-input" accept="image/*,.pdf">
                        @if($claim->attachment_path)
                            <p class="text-xs mt-2">File saat ini: <a href="{{ asset('storage/' . $claim->attachment_path) }}" target="_blank" class="text-primary text-decoration-underline"><i class="bi bi-file-earmark-text"></i> Lihat Bukti</a></p>
                        @endif
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('finance.claims.index') }}" class="btn btn-outline-secondary mb-0" style="border-radius:9px">Batal</a>
                        <button type="submit" class="btn btn-warning mb-0 px-4" style="border-radius:9px; color:white;">
                            <i class="bi bi-save me-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ── MODAL QUICK ADD ACCOUNT ────────────────── --}}
@if(auth()->user()->isMasterAdmin() || auth()->user()->isFinance())
<div class="modal fade" id="quickAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color:#1a1f3c">Tambah Akun Biaya Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickAccountForm">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="fin-label">Kode Akun <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="acc_code" class="fin-input" placeholder="Contoh: 5-1004" required>
                        <p class="text-xs text-muted mt-1">Kode unik untuk mengidentifikasi akun (CoA).</p>
                    </div>

                    <div class="mb-3">
                        <label class="fin-label">Nama Akun <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="acc_name" class="fin-input" placeholder="Contoh: Biaya Makan Siang" required>
                    </div>

                    <div class="mb-0">
                        <label class="fin-label">Deskripsi <span class="opt">(Opsional)</span></label>
                        <textarea name="description" id="acc_desc" class="fin-input" placeholder="Detail kegunaan akun ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn text-xs fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn px-4 text-white text-xs fw-bold" style="background:#5e72e4; border-radius:8px">
                        <i class="bi bi-save me-1"></i> Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
@if(auth()->user()->isMasterAdmin() || auth()->user()->isFinance())
const quickModal = new bootstrap.Modal(document.getElementById('quickAccountModal'));

document.getElementById('quickAccountForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

    const formData = {
        _token: '{{ csrf_token() }}',
        category: 'expense', // Fixed for claims
        code: document.getElementById('acc_code').value,
        name: document.getElementById('acc_name').value,
        description: document.getElementById('acc_desc').value,
        is_active: 1
    };

    fetch('{{ route("finance.accounts.store") }}', {
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
            // Add to dropdown and select it
            const select = document.getElementById('account_id');
            const newOption = new Option(`[${result.data.code}] ${result.data.name}`, result.data.id, true, true);
            select.add(newOption);
            
            quickModal.hide();
            alert('Akun biaya baru berhasil ditambahkan!');
        } else {
            alert('Gagal: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan. Pastikan kode akun belum digunakan.');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});
@endif
</script>
@endpush
