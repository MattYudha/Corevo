<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancialClaim;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClaimController extends Controller
{
    // ── Index: daftar klaim milik user (atau semua jika admin) ───
    public function index(Request $request)
    {
        $user     = auth()->user();
        $isAdmin  = $user->isAdmin();
        $employee = $user->employee;

        $query = FinancialClaim::with(['employee', 'reviewer', 'account'])
            ->when(!$isAdmin, function($q) use ($employee) {
                if ($employee) {
                    $q->where('employee_id', $employee->id);
                } else {
                    $q->where(fn($sq) => $sq->whereNull('employee_id')->orWhere('employee_id', 0));
                }
            })
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->category, fn($q, $c) => $q->where('category', $c))
            ->latest()
            ->paginate(15);

        $statsQuery = FinancialClaim::query()
            ->when(!$isAdmin, function($q) use ($employee) {
                if ($employee) {
                    $q->where('employee_id', $employee->id);
                } else {
                    $q->where(fn($sq) => $sq->whereNull('employee_id')->orWhere('employee_id', 0));
                }
            });

        $stats = [
            'pending'  => (clone $statsQuery)->pending()->count(),
            'approved' => (clone $statsQuery)->approved()->count(),
            'rejected' => (clone $statsQuery)->rejected()->count(),
            'total_approved_amount' => (clone $statsQuery)->approved()->sum('amount'),
        ];

        return view('finance.claims.index', compact('query', 'stats', 'isAdmin'));
    }

    // ── Create form ──────────────────────────────────────────────
    public function create()
    {
        $accounts = FinancialAccount::where('category', 'expense')->orderBy('code')->get();
        return view('finance.claims.create', compact('accounts'));
    }

    // ── Store (ajukan klaim baru) ────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'required|in:transport,meals,operational,equipment,other',
            'amount'      => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:1000',
            'account_id'  => 'required|exists:financial_accounts,id',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
        ]);

        // Handle file upload — bypass move_uploaded_file() issue on Docker WSL2/Windows volumes
        unset($validated['attachment']); // jangan mass-assign UploadedFile object

        if ($request->hasFile('attachment')) {
            $file     = $request->file('attachment');
            
            if (!$file->isValid()) {
                return back()->withErrors(['attachment' => 'File tidak valid. Error code PHP: ' . $file->getError()])->withInput();
            }

            $filename = $file->hashName();
            $subPath  = 'claims/attachments/' . $filename;

            try {
                $content = file_get_contents($file->getRealPath());
                if ($content === false) {
                    return back()->withErrors(['attachment' => 'Gagal membaca isi file dari directory temp.'])->withInput();
                }

                // Path penuh tempat file akan disimpan
                $fullPath = storage_path("app/public/" . $subPath);

                // Pastikan direktori ada (bikin murni pakai PHP jika belum ada)
                $dir = dirname($fullPath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                // Tulis langsung pakai fungsi native (bypass Flysystem Laravel)
                $stored = file_put_contents($fullPath, $content);

                if ($stored === false) {
                    $error = error_get_last();
                    return back()
                        ->withErrors(['attachment' => "Gagal write file: " . ($error['message'] ?? 'Unknown error')])
                        ->withInput();
                }

                $validated['attachment_path'] = $subPath;
            } catch (\Exception $e) {
                return back()
                    ->withErrors(['attachment' => 'Exception: ' . $e->getMessage()])
                    ->withInput();
            }
        }

        $validated['employee_id'] = auth()->user()->employee?->id;
        $validated['status']      = 'pending';

        FinancialClaim::create($validated);

        return redirect()->route('finance.claims.index')
            ->with('success', 'Klaim biaya berhasil diajukan dan sedang menunggu persetujuan.');
    }

    // ── Edit (tampilkan form edit) ───────────────────────────────
    public function edit(FinancialClaim $claim)
    {
        $this->authorizeClaimAccess($claim);
        
        if (!$claim->isPending()) {
            return redirect()->route('finance.claims.index')->with('error', 'Hanya klaim dengan status pending yang dapat diedit.');
        }

        $accounts = FinancialAccount::where('category', 'expense')->orderBy('code')->get();
        return view('finance.claims.edit', compact('claim', 'accounts'));
    }

    // ── Update (simpan perubahan klaim) ──────────────────────────
    public function update(Request $request, FinancialClaim $claim)
    {
        $this->authorizeClaimAccess($claim);

        if (!$claim->isPending()) {
            return redirect()->route('finance.claims.index')->with('error', 'Hanya klaim dengan status pending yang dapat diedit.');
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'required|in:transport,meals,operational,equipment,other',
            'amount'      => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:1000',
            'account_id'  => 'required|exists:financial_accounts,id',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
        ]);

        unset($validated['attachment']);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            
            if (!$file->isValid()) {
                return back()->withErrors(['attachment' => 'File tidak valid. Error code PHP: ' . $file->getError()])->withInput();
            }

            $filename = $file->hashName();
            $subPath  = 'claims/attachments/' . $filename;

            try {
                $content = file_get_contents($file->getRealPath());
                if ($content === false) {
                    return back()->withErrors(['attachment' => 'Gagal membaca isi file dari directory temp.'])->withInput();
                }

                $fullPath = storage_path("app/public/" . $subPath);
                $dir = dirname($fullPath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                $stored = file_put_contents($fullPath, $content);
                if ($stored === false) {
                    return back()->withErrors(['attachment' => "Gagal write file."])->withInput();
                }

                // Hapus attachment lama jika ada
                if ($claim->attachment_path && file_exists(storage_path("app/public/" . $claim->attachment_path))) {
                    @unlink(storage_path("app/public/" . $claim->attachment_path));
                }

                $validated['attachment_path'] = $subPath;
            } catch (\Exception $e) {
                return back()->withErrors(['attachment' => 'Exception: ' . $e->getMessage()])->withInput();
            }
        }

        $claim->update($validated);

        return redirect()->route('finance.claims.index')
            ->with('success', 'Klaim biaya berhasil diperbarui.');
    }

    public function show(FinancialClaim $claim)
    {
        $this->authorizeClaimAccess($claim);
        $claim->load(['employee', 'reviewer', 'account', 'transaction']);
        
        $isAdmin = auth()->user()->isAdmin();
        return view('finance.claims.show', compact('claim', 'isAdmin'));
    }

    // ── Admin: Approve ────────────────────────────────────────────
    public function approve(Request $request, FinancialClaim $claim)
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Hanya admin yang dapat menyetujui klaim.');

        if (!$claim->isPending()) {
            return back()->with('error', 'Klaim ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'review_notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($claim, $request) {

            // ── 1. Buat jurnal transaksi otomatis ─────────────────
            $description = "Reimburse klaim: {$claim->title} — {$claim->employee->full_name}";

            $transaction = FinancialTransaction::create([
                'transaction_date'   => now()->toDateString(),
                'transaction_type'   => 'kredit',          // uang keluar
                'amount'             => $claim->amount,
                'description'        => $description,
                'account_id'         => $claim->account_id,
                'receiver_entity_id' => null,              // bisa dikembangkan ke entitas karyawan
                'sender_entity_id'   => null,
                'created_by'         => auth()->id(),
                'is_end_of_month'    => false,
                'is_end_of_year'     => false,
                'running_balance'    => 0,                 // akan dihitung ulang
            ]);

            // ── 2. Recalculate running balance ────────────────────
            $this->recalculateRunningBalance();

            // ── 3. Update status klaim ────────────────────────────
            $claim->update([
                'status'         => 'approved',
                'reviewed_by'    => auth()->id(),
                'reviewed_at'    => now(),
                'review_notes'   => $request->review_notes,
                'transaction_id' => $transaction->id,
            ]);
        });

        return redirect()->route('finance.claims.index')
            ->with('success', "Klaim #{$claim->id} disetujui dan jurnal transaksi otomatis telah dibuat.");
    }

    // ── Admin: Reject ─────────────────────────────────────────────
    public function reject(Request $request, FinancialClaim $claim)
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Hanya admin yang dapat menolak klaim.');

        if (!$claim->isPending()) {
            return back()->with('error', 'Klaim ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'review_notes' => 'required|string|max:500',
        ]);

        $claim->update([
            'status'       => 'rejected',
            'reviewed_by'  => auth()->id(),
            'reviewed_at'  => now(),
            'review_notes' => $request->review_notes,
        ]);

        return redirect()->route('finance.claims.index')
            ->with('info', "Klaim #{$claim->id} telah ditolak.");
    }

    // ── Private: recalculate all running balances ─────────────────
    private function recalculateRunningBalance(): void
    {
        $transactions = FinancialTransaction::orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        $balance = 0;
        foreach ($transactions as $trx) {
            $balance += ($trx->transaction_type === 'debit') ? $trx->amount : -$trx->amount;
            $trx->update(['running_balance' => $balance]);
        }
    }

    // ── Private: authorize access ─────────────────────────────────
    private function authorizeClaimAccess(FinancialClaim $claim): void
    {
        if (!auth()->user()->isAdmin()) {
            $empId = auth()->user()->employee?->id;
            abort_if($claim->employee_id !== $empId, 403, 'Anda tidak berhak mengakses klaim ini.');
        }
    }
}
