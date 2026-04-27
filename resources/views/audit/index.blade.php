@extends('layouts.dashboard')

@push('styles')
<style>
    /* ── Audit Trail Enterprise UI ───────────────────────── */
    .audit-hero {
        background: linear-gradient(135deg, #1a1f3c 0%, #0d6efd 100%);
        border-radius: 16px;
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .audit-hero::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 200px; height: 200px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
    }
    .audit-hero::after {
        content: '';
        position: absolute;
        bottom: -40px; left: 30%;
        width: 140px; height: 140px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
    }

    /* Stats Cards */
    .audit-stat-card {
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 12px;
        padding: 1rem 1.25rem;
        backdrop-filter: blur(6px);
        transition: background 0.2s;
    }
    .audit-stat-card:hover { background: rgba(255,255,255,0.18); }
    .audit-stat-card .stat-value { font-size: 1.75rem; font-weight: 700; line-height: 1; }
    .audit-stat-card .stat-label { font-size: 0.72rem; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.6px; margin-top: 4px; }

    /* Toggle Button */
    .audit-toggle-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1.1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
        border: 2px solid rgba(255,255,255,0.3);
        background: rgba(255,255,255,0.15);
        color: #fff;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .audit-toggle-btn:hover { background: rgba(255,255,255,0.25); color: #fff; }
    .audit-toggle-btn.on  { border-color: #22c55e; background: rgba(34,197,94,0.2); }
    .audit-toggle-btn.off { border-color: #f97316; background: rgba(249,115,22,0.2); }
    .toggle-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        animation: pulse-dot 1.8s infinite;
    }
    .toggle-dot.on  { background: #22c55e; }
    .toggle-dot.off { background: #f97316; animation: none; }
    @keyframes pulse-dot {
        0%,100% { opacity:1; transform: scale(1); }
        50%      { opacity:0.5; transform: scale(1.3); }
    }

    /* Filter Card */
    .filter-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        padding: 1.1rem 1.4rem;
        margin-bottom: 1.25rem;
    }
    [data-bs-theme='dark'] .filter-card {
        background: #1e2130;
        border-color: rgba(255,255,255,0.08);
    }

    /* Log Table Card */
    .log-card {
        border-radius: 14px;
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 16px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    [data-bs-theme='dark'] .log-card { border-color: rgba(255,255,255,0.08); }

    .log-card .card-header-bar {
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 1rem 1.4rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    [data-bs-theme='dark'] .log-card .card-header-bar {
        background: #1e2130;
        border-bottom-color: rgba(255,255,255,0.07);
    }

    .log-table th {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        color: #6c757d;
        border-bottom: 2px solid #f0f0f0;
        padding: 0.9rem 1rem;
        background: #fafbfc;
        white-space: nowrap;
    }
    [data-bs-theme='dark'] .log-table th {
        background: #252840;
        border-bottom-color: rgba(255,255,255,0.07);
        color: #9ca3af;
    }
    .log-table td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f5f5f7;
        font-size: 0.875rem;
    }
    [data-bs-theme='dark'] .log-table td { border-bottom-color: rgba(255,255,255,0.05); }
    .log-table tbody tr { transition: background 0.15s; }
    .log-table tbody tr:hover { background: rgba(13,110,253,0.04); }
    .log-table tbody tr:last-child td { border-bottom: none; }

    /* Event Badges */
    .badge-event {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 0.3rem 0.7rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    .badge-created { background: #dcfce7; color: #15803d; }
    .badge-updated { background: #fef9c3; color: #a16207; }
    .badge-deleted { background: #fee2e2; color: #b91c1c; }
    .badge-other   { background: #f3f4f6; color: #6b7280; }
    [data-bs-theme='dark'] .badge-created { background: rgba(34,197,94,0.15);  color: #4ade80; }
    [data-bs-theme='dark'] .badge-updated { background: rgba(234,179,8,0.15);  color: #facc15; }
    [data-bs-theme='dark'] .badge-deleted { background: rgba(239,68,68,0.15);  color: #f87171; }

    /* User Avatar */
    .user-avatar {
        width: 28px; height: 28px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
    }
    .user-avatar.system { background: linear-gradient(135deg, #94a3b8, #64748b); }

    /* Detail Button */
    .btn-detail {
        width: 32px; height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dee2e6;
        background: transparent;
        color: #6c757d;
        transition: all 0.2s;
        cursor: pointer;
    }
    .btn-detail:hover { border-color: #0d6efd; color: #0d6efd; background: #e8f0fe; }

    /* Purge Button */
    .btn-purge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 1rem;
        border-radius: 8px;
        border: 1.5px solid #dc3545;
        color: #dc3545;
        background: transparent;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-purge:hover { background: #dc3545; color: #fff; }

    /* Offline Warning Banner */
    .audit-offline-banner {
        background: linear-gradient(90deg, #fff7ed, #ffedd5);
        border: 1px solid #fdba74;
        border-radius: 10px;
        padding: 0.85rem 1.25rem;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.875rem;
    }
    [data-bs-theme='dark'] .audit-offline-banner {
        background: rgba(249,115,22,0.12);
        border-color: rgba(249,115,22,0.3);
        color: #fdba74;
    }

    /* Diff Modal */
    .diff-block {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        font-family: 'Fira Code', 'Courier New', monospace;
        font-size: 0.78rem;
        line-height: 1.6;
        max-height: 280px;
        overflow-y: auto;
        border: 1px solid #e9ecef;
    }
    [data-bs-theme='dark'] .diff-block { background: #1a1f3c; border-color: rgba(255,255,255,0.08); }

    /* Empty State */
    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
        color: #9ca3af;
    }
    .empty-state i { font-size: 3rem; display: block; margin-bottom: 1rem; opacity: 0.4; }

    /* Pagination tweak */
    .pagination { margin: 0; }
    .page-link { border-radius: 8px !important; margin: 0 2px; font-size: 0.82rem; }
</style>
@endpush

@section('content')
<div class="page-content">
<div class="container-fluid px-3 px-md-4">

    {{-- ── Hero Header ──────────────────────────────────── --}}
    <div class="audit-hero">
        <div class="row align-items-center gy-3">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div style="width:44px;height:44px;border-radius:12px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-shield-check" style="font-size:1.35rem;line-height:1;display:flex;"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold">Audit Trail</h4>
                        <p class="mb-0 small" style="opacity:0.75;">Log aktivitas sistem — <strong>{{ number_format($totalLogs) }}</strong> total entri tersimpan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex flex-wrap gap-2 justify-content-md-end align-items-center">
                    {{-- Stats --}}
                    <div class="audit-stat-card text-center" style="min-width:90px;">
                        <div class="stat-value">{{ number_format($totalLogs) }}</div>
                        <div class="stat-label">Total Log</div>
                    </div>
                    <div class="audit-stat-card text-center" style="min-width:90px;">
                        <div class="stat-value">{{ $logs->total() }}</div>
                        <div class="stat-label">Hasil Filter</div>
                    </div>
                    {{-- Toggle Button --}}
                    <form action="{{ route('audit.toggle') }}" method="POST" class="d-inline m-0">
                        @csrf
                        <button type="submit" class="audit-toggle-btn {{ $auditEnabled ? 'on' : 'off' }}">
                            <span class="toggle-dot {{ $auditEnabled ? 'on' : 'off' }}"></span>
                            Logging {{ $auditEnabled ? 'ON' : 'OFF' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Alert Messages ───────────────────────────────── --}}
    @if(session('success'))
    <div class="alert alert-success d-flex align-items-center gap-2 mb-3 rounded-3 border-0 shadow-sm">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <span>{{ session('success') }}</span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ── Offline Warning ───────────────────────────────── --}}
    @if(!$auditEnabled)
    <div class="audit-offline-banner">
        <i class="bi bi-pause-circle-fill fs-5 text-warning flex-shrink-0"></i>
        <div>
            <strong>Audit logging saat ini NONAKTIF.</strong>
            Semua aktivitas sistem tidak akan dicatat hingga logging diaktifkan kembali.
        </div>
    </div>
    @endif

    {{-- ── Filter Bar ───────────────────────────────────── --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('audit.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1 text-muted">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control form-control-sm rounded-3"
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1 text-muted">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-control form-control-sm rounded-3"
                           value="{{ request('date_to') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1 text-muted">Tipe Event</label>
                    <select name="event" class="form-select form-select-sm rounded-3">
                        <option value="">Semua Event</option>
                        <option value="created" {{ request('event') === 'created' ? 'selected' : '' }}>✅ Created</option>
                        <option value="updated" {{ request('event') === 'updated' ? 'selected' : '' }}>✏️ Updated</option>
                        <option value="deleted" {{ request('event') === 'deleted' ? 'selected' : '' }}>🗑️ Deleted</option>
                    </select>
                </div>
                <div class="col-6 col-md-2 d-flex gap-1 align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 flex-fill">
                        <i class="bi bi-funnel-fill me-1"></i>Filter
                    </button>
                    @if(request()->hasAny(['date_from','date_to','event']))
                    <a href="{{ route('audit.index') }}" class="btn btn-outline-secondary btn-sm rounded-3" title="Reset filter">
                        <i class="bi bi-x-lg"></i>
                    </a>
                    @endif
                </div>
                <div class="col-12 col-md-2 d-flex align-items-end justify-content-md-end">
                    <button type="button" class="btn-purge w-100" data-bs-toggle="modal" data-bs-target="#purgeModal">
                        <i class="bi bi-trash3"></i> Purge Log
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- ── Log Table ────────────────────────────────────── --}}
    <div class="log-card">
        <div class="card-header-bar">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-primary"></i>
                <span class="fw-bold">System Activity Log</span>
                @if(request()->hasAny(['date_from','date_to','event']))
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill ms-1 small">Filter Aktif</span>
                @endif
            </div>
            <span class="text-muted small">Halaman {{ $logs->currentPage() }} dari {{ $logs->lastPage() }}</span>
        </div>

        <div class="table-responsive">
            <table class="table log-table mb-0">
                <thead>
                    <tr>
                        <th style="width:165px;">Waktu</th>
                        <th>Pengguna</th>
                        <th style="width:110px;">Event</th>
                        <th>Resource</th>
                        <th style="width:120px;">IP Address</th>
                        <th class="text-center" style="width:70px;">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    @php
                        $event = strtolower($log->event ?? 'other');
                        $badgeClass = match($event) {
                            'created' => 'badge-created',
                            'updated' => 'badge-updated',
                            'deleted' => 'badge-deleted',
                            default   => 'badge-other',
                        };
                        $eventIcon = match($event) {
                            'created' => 'bi-plus-circle-fill',
                            'updated' => 'bi-pencil-fill',
                            'deleted' => 'bi-trash-fill',
                            default   => 'bi-arrow-right-circle-fill',
                        };
                        $userName = $log->user->name ?? null;
                        $isSystem = is_null($userName);
                        $initials = $isSystem ? 'SYS' : strtoupper(substr($userName, 0, 1) . (str_contains($userName, ' ') ? substr(strrchr($userName, ' '), 1, 1) : ''));
                    @endphp
                    <tr>
                        {{-- Waktu --}}
                        <td>
                            <span class="fw-semibold text-dark small">{{ $log->created_at->format('d M Y') }}</span>
                            <br><span class="text-muted" style="font-size:0.72rem;">{{ $log->created_at->format('H:i:s') }}</span>
                        </td>

                        {{-- User --}}
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar {{ $isSystem ? 'system' : '' }}">{{ $initials }}</div>
                                <div>
                                    <div class="fw-semibold small">{{ $isSystem ? 'System' : $userName }}</div>
                                    @if(!$isSystem && $log->user?->email)
                                    <div class="text-muted" style="font-size:0.7rem;">{{ $log->user->email }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Event Badge --}}
                        <td>
                            <span class="badge-event {{ $badgeClass }}">
                                <i class="bi {{ $eventIcon }}" style="font-size:0.65rem;"></i>
                                {{ strtoupper($event) }}
                            </span>
                        </td>

                        {{-- Resource --}}
                        <td>
                            <span class="fw-semibold small">{{ class_basename($log->auditable_type ?? 'Unknown') }}</span>
                            <span class="text-muted small ms-1">#{{ $log->auditable_id }}</span>
                        </td>

                        {{-- IP --}}
                        <td>
                            <code class="small" style="background:transparent;padding:0;">{{ $log->ip_address ?? '-' }}</code>
                        </td>

                        {{-- Detail Button --}}
                        <td class="text-center">
                            <button class="btn-detail view-details"
                                    data-old='@json($log->old_values)'
                                    data-new='@json($log->new_values)'
                                    data-event="{{ ucfirst($event) }}"
                                    data-resource="{{ class_basename($log->auditable_type ?? '') }} #{{ $log->auditable_id }}"
                                    title="Lihat perubahan">
                                <i class="bi bi-code-slash" style="font-size:0.85rem;"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <div class="fw-semibold mb-1">Tidak ada log ditemukan</div>
                                <div class="small">Coba ubah filter atau rentang tanggal</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
        <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted small">
                Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ $logs->total() }} entri
            </span>
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
</div>

{{-- ── View Diff Modal ──────────────────────────────────── --}}
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#1a1f3c,#0d6efd);color:#fff;border:none;">
                <div>
                    <h5 class="modal-title fw-bold mb-0"><i class="bi bi-code-slash me-2"></i>Change Diff</h5>
                    <div id="modalSubtitle" class="small mt-1" style="opacity:0.75;"></div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-danger">BEFORE</span>
                            <span class="small text-muted">Nilai Sebelumnya</span>
                        </div>
                        <pre class="diff-block" id="oldValues"></pre>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-success">AFTER</span>
                            <span class="small text-muted">Nilai Sesudahnya</span>
                        </div>
                        <pre class="diff-block" id="newValues"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Purge Modal ──────────────────────────────────────── --}}
<div class="modal fade" id="purgeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:#dc3545;color:#fff;border:none;">
                <h5 class="modal-title fw-bold mb-0"><i class="bi bi-trash3-fill me-2"></i>Purge Audit Logs</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('audit.purge') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body p-4">
                    <div class="d-flex gap-3 mb-4 p-3 rounded-3" style="background:#fff5f5;border:1px solid #fecaca;">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-4 flex-shrink-0"></i>
                        <div class="small">
                            <strong class="text-danger">Tindakan tidak dapat dibatalkan.</strong><br>
                            Semua log sebelum tanggal yang dipilih akan dihapus permanen dari database.
                        </div>
                    </div>
                    <div>
                        <label class="form-label fw-semibold small">Hapus log sebelum tanggal:</label>
                        <input type="date" name="purge_before" class="form-control rounded-3"
                               required max="{{ now()->format('Y-m-d') }}">
                        <div class="form-text">Contoh: pilih 30 hari lalu untuk hapus semua log lama.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-3 px-4"
                        onclick="return confirm('Konfirmasi: Hapus semua log sebelum tanggal ini secara permanen?')">
                        <i class="bi bi-trash3 me-1"></i>Hapus Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const detailsModal = new bootstrap.Modal(document.getElementById('detailsModal'));

    document.querySelectorAll('.view-details').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const oldData = JSON.parse(this.dataset.old || 'null') || {};
            const newData = JSON.parse(this.dataset.new || 'null') || {};

            document.getElementById('oldValues').textContent =
                Object.keys(oldData).length ? JSON.stringify(oldData, null, 2) : '(tidak ada data)';
            document.getElementById('newValues').textContent =
                Object.keys(newData).length ? JSON.stringify(newData, null, 2) : '(tidak ada data)';
            document.getElementById('modalSubtitle').textContent =
                (this.dataset.event || '') + ' — ' + (this.dataset.resource || '');

            detailsModal.show();
        });
    });
});
</script>
@endpush
@endsection
