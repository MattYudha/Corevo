<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancialTransaction;
use App\Models\FinancialEntity;
use App\Models\FinancialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FinancialTransactionController extends Controller
{
    /**
     * Apply common filters efficiently to ensure summary and table data are in sync.
     */
    private function applyTransactionFilters(Request $request, $query)
    {
        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }
        if ($request->filled('type')) {
            $query->where('transaction_type', $request->type);
        }
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }
        return $query;
    }

    /**
     * Display a listing of transactions (Buku Kas Ledger).
     */
    public function index(Request $request)
    {
        $query = FinancialTransaction::with(['senderEntity', 'receiverEntity', 'account', 'creator'])
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc');

        $query = $this->applyTransactionFilters($request, $query);

        $transactions = $query->paginate(20)->withQueryString();

        // Summary totals: Clone query to perfectly match table filters
        $summaryQuery = FinancialTransaction::query();
        $summaryQuery = $this->applyTransactionFilters($request, $summaryQuery);

        $totalDebit  = (clone $summaryQuery)->where('transaction_type', 'debit')->sum('amount');
        $totalKredit = (clone $summaryQuery)->where('transaction_type', 'kredit')->sum('amount');

        $accounts = FinancialAccount::orderBy('code')->get();

        return view('finance.transactions.index', compact(
            'transactions', 'totalDebit', 'totalKredit', 'accounts'
        ));
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create()
    {
        $entities = FinancialEntity::orderBy('name')->get();
        $accounts = FinancialAccount::orderBy('code')->get();
        return view('finance.transactions.create', compact('entities', 'accounts'));
    }

    /**
     * Pastikan kolom pph_transaction_id benar-benar ada di database.
     * Workaround darurat jika proses migrate di production gagal atau ter-skip.
     */
    private function ensurePphColumnExists()
    {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('financial_transactions', 'pph_transaction_id')) {
            try {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE financial_transactions ADD pph_transaction_id BIGINT UNSIGNED NULL AFTER tax_amount');
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE financial_transactions ADD CONSTRAINT ft_pph_fk FOREIGN KEY (pph_transaction_id) REFERENCES financial_transactions(id) ON DELETE SET NULL');
            } catch (\Throwable $e) {
                \Log::error('Gagal force add column: ' . $e->getMessage());
            }
        }
    }

    /**
     * Store a newly created transaction and recalculate running balances.
     * Jika transaksi mengandung PPh (21/23/4 ayat 2), otomatis buat
     * transaksi kredit ke akun Utang PPh.
     */
    public function store(Request $request)
    {
        $this->ensurePphColumnExists();
        $validated = $request->validate([
            'transaction_date'   => 'required|date',
            'description'        => 'required|string|max:500',
            'sender_entity_id'   => 'nullable|exists:financial_entities,id',
            'receiver_entity_id' => 'nullable|exists:financial_entities,id',
            'account_id'         => 'required|exists:financial_accounts,id',
            'transaction_type'   => 'required|in:debit,kredit',
            'amount'             => 'required|numeric|min:0',
            'dpp_amount'         => 'nullable|numeric|min:0',
            'tax_type'           => 'nullable|in:none,ppn,pph_21,pph_23,pph_4_ayat_2',
            'tax_amount'         => 'nullable|numeric|min:0',
            'document'           => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('document')) {
            $validated['document_path'] = $request->file('document')->store('finance/documents', 'local');
        }

        $validated['created_by'] = Auth::id();
        unset($validated['document']);
        $hadPph = false;

        try {
            DB::transaction(function () use ($validated, &$hadPph) {
                // Lock account to prevent concurrent modifications
                FinancialAccount::where('id', $validated['account_id'])->lockForUpdate()->first();

                $transaction = FinancialTransaction::create($validated);
                $this->recalculateRunningBalance($transaction->account_id, $transaction->transaction_date);

                // ── Auto-create PPh transaction ─────────────────────────
                if ($this->shouldCreatePph($validated)) {
                    $pphTrx = $this->createPphTransaction($transaction, $validated);
                    // Link kembali ke transaksi induk
                    $transaction->pph_transaction_id = $pphTrx->id;
                    $transaction->saveQuietly();
                    $hadPph = true;
                }
            });
        } catch (\Throwable $e) {
            \Log::error('Finance Store Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }

        $msg = 'Transaksi berhasil disimpan.';
        if ($hadPph) {
            $msg .= ' Transaksi PPh otomatis telah dibuat.';
        }

        return redirect()->route('finance.transactions.index')->with('success', $msg);
    }

    /**
     * Show the form for editing a transaction.
     */
    public function edit(FinancialTransaction $transaction)
    {
        // Cegah edit langsung transaksi PPh auto-generated
        if ($transaction->isAutoPph()) {
            return redirect()->route('finance.transactions.index')
                ->with('error', 'Transaksi PPh otomatis tidak dapat diedit langsung. Edit transaksi induknya.');
        }

        $entities = FinancialEntity::orderBy('name')->get();
        $accounts = FinancialAccount::orderBy('code')->get();
        return view('finance.transactions.edit', compact('transaction', 'entities', 'accounts'));
    }

    /**
     * Update a transaction and recalculate running balances.
     * Sync transaksi PPh terkait jika ada perubahan nilai pajak.
     */
    public function update(Request $request, FinancialTransaction $transaction)
    {
        $this->ensurePphColumnExists();

        // Cegah update langsung transaksi PPh auto-generated
        if ($transaction->isAutoPph()) {
            return redirect()->route('finance.transactions.index')
                ->with('error', 'Transaksi PPh otomatis tidak dapat diedit langsung. Edit transaksi induknya.');
        }

        $validated = $request->validate([
            'transaction_date'   => 'required|date',
            'description'        => 'required|string|max:500',
            'sender_entity_id'   => 'nullable|exists:financial_entities,id',
            'receiver_entity_id' => 'nullable|exists:financial_entities,id',
            'account_id'         => 'required|exists:financial_accounts,id',
            'transaction_type'   => 'required|in:debit,kredit',
            'amount'             => 'required|numeric|min:0',
            'dpp_amount'         => 'nullable|numeric|min:0',
            'tax_type'           => 'nullable|in:none,ppn,pph_21,pph_23,pph_4_ayat_2',
            'tax_amount'         => 'nullable|numeric|min:0',
            'document'           => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('document')) {
            if ($transaction->document_path) {
                Storage::disk('local')->delete($transaction->document_path);
            }
            $validated['document_path'] = $request->file('document')->store('finance/documents', 'local');
        } elseif ($request->boolean('remove_document')) {
            if ($transaction->document_path) {
                Storage::disk('local')->delete($transaction->document_path);
            }
            $validated['document_path'] = null;
        }

        unset($validated['document']);
        $oldAccountId = $transaction->account_id;
        $oldDate      = $transaction->transaction_date;
        $newAccountId = $validated['account_id'];
        $newDate      = $validated['transaction_date'];

        try {
            DB::transaction(function () use ($validated, $transaction, $oldAccountId, $oldDate, $newAccountId, $newDate) {
                // Lock accounts menggunakan urutan ascending untuk cegah deadlock
                $accountsToLock = array_unique([$oldAccountId, $newAccountId]);
                sort($accountsToLock);
                FinancialAccount::whereIn('id', $accountsToLock)->orderBy('id', 'asc')->lockForUpdate()->get();

                $transaction->update($validated);

                // Recalculate balance transaksi utama
                if ($oldAccountId != $newAccountId) {
                    $this->recalculateRunningBalance($oldAccountId, $oldDate);
                    $this->recalculateRunningBalance($newAccountId, $newDate);
                } else {
                    $earliestDate = strtotime($oldDate) <= strtotime($newDate) ? $oldDate : $newDate;
                    $this->recalculateRunningBalance($newAccountId, $earliestDate);
                }

                // ── Sync transaksi PPh ───────────────────────────────────
                $existingPphId = $transaction->pph_transaction_id;
                $shouldHavePph = $this->shouldCreatePph($validated);

                if ($existingPphId) {
                    $existingPph = FinancialTransaction::find($existingPphId);

                    if (!$shouldHavePph) {
                        // PPh dihapus dari transaksi induk → hapus transaksi PPh
                        if ($existingPph) {
                            $pphAccountId = $existingPph->account_id;
                            $pphDate      = $existingPph->transaction_date;
                            $existingPph->delete();
                            $this->recalculateRunningBalance($pphAccountId, $pphDate);
                        }
                        $transaction->pph_transaction_id = null;
                        $transaction->saveQuietly();
                    } else {
                        // Update nilai PPh yang sudah ada
                        if ($existingPph) {
                            $pphAccountId = $existingPph->account_id;
                            $pphDate      = $existingPph->transaction_date;
                            $pphLabel     = FinancialTransaction::PPH_LABELS[$validated['tax_type']] ?? 'PPh';

                            $existingPph->update([
                                'amount'           => $validated['tax_amount'],
                                'transaction_date' => $validated['transaction_date'],
                                'description'      => FinancialTransaction::AUTO_PPH_PREFIX . ' ' . $pphLabel . ' atas: ' . $validated['description'],
                            ]);

                            $earliestPphDate = strtotime($pphDate) <= strtotime($validated['transaction_date'])
                                ? $pphDate : $validated['transaction_date'];
                            $this->recalculateRunningBalance($pphAccountId, $earliestPphDate);
                        }
                    }
                } elseif ($shouldHavePph) {
                    // Belum ada transaksi PPh tapi sekarang ada → buat baru
                    $pphTrx = $this->createPphTransaction($transaction, $validated);
                    $transaction->pph_transaction_id = $pphTrx->id;
                    $transaction->saveQuietly();
                }
            });
        } catch (\Throwable $e) {
            \Log::error('Finance Update Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }

        return redirect()->route('finance.transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Delete a transaction and recalculate running balances.
     * Jika ada transaksi PPh terkait, hapus juga secara otomatis.
     */
    public function destroy(FinancialTransaction $transaction)
    {
        // Cegah hapus langsung transaksi PPh auto-generated
        if ($transaction->isAutoPph()) {
            return redirect()->route('finance.transactions.index')
                ->with('error', 'Transaksi PPh otomatis tidak dapat dihapus langsung. Hapus transaksi induknya.');
        }

        $accountId = $transaction->account_id;
        $trxDate   = $transaction->transaction_date;

        DB::transaction(function () use ($transaction, $accountId, $trxDate) {
            FinancialAccount::where('id', $accountId)->lockForUpdate()->first();

            // ── Hapus transaksi PPh terkait terlebih dahulu ──────────
            if ($transaction->pph_transaction_id) {
                $pphTrx = FinancialTransaction::find($transaction->pph_transaction_id);
                if ($pphTrx) {
                    $pphAccountId = $pphTrx->account_id;
                    $pphDate      = $pphTrx->transaction_date;
                    $pphTrx->delete();
                    $this->recalculateRunningBalance($pphAccountId, $pphDate);
                }
            }

            if ($transaction->document_path) {
                Storage::disk('local')->delete($transaction->document_path);
            }
            $transaction->delete();
            $this->recalculateRunningBalance($accountId, $trxDate);
        });

        return redirect()->route('finance.transactions.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }

    /**
     * Securely download document associated with transaction.
     */
    public function downloadDocument(FinancialTransaction $transaction)
    {
        if (!$transaction->document_path) {
            abort(404, 'Dokumen tidak ditemukan.');
        }
        if (!Storage::disk('local')->exists($transaction->document_path)) {
            abort(404, 'File fisik tidak ditemukan di server.');
        }
        return response()->file(Storage::disk('local')->path($transaction->document_path));
    }

    // ══════════════════════════════════════════════════════════════
    // Private Helpers — PPh Auto-Transaction
    // ══════════════════════════════════════════════════════════════

    /**
     * Cek apakah perlu membuat transaksi PPh otomatis.
     * Hanya untuk PPh (bukan PPN), dan tax_amount harus > 0.
     */
    private function shouldCreatePph(array $data): bool
    {
        return in_array($data['tax_type'] ?? '', FinancialTransaction::PPH_TYPES)
            && ($data['tax_amount'] ?? 0) > 0;
    }

    /**
     * Dapatkan atau buat akun Utang PPh (kode 2100, kategori liability).
     */
    private function getPphAccountId(): int
    {
        $account = FinancialAccount::where('code', '2100')->first();

        if (!$account) {
            $account = FinancialAccount::create([
                'code'        => '2100',
                'name'        => 'Utang PPh',
                'category'    => 'liability',
                'description' => 'Kewajiban PPh yang dipotong dan belum disetor ke negara.',
            ]);
        }

        return $account->id;
    }

    /**
     * Buat transaksi kredit PPh otomatis dari transaksi induk.
     */
    private function createPphTransaction(FinancialTransaction $parent, array $data): FinancialTransaction
    {
        $pphAccountId = $this->getPphAccountId();
        $pphLabel     = FinancialTransaction::PPH_LABELS[$data['tax_type']] ?? 'PPh';

        FinancialAccount::where('id', $pphAccountId)->lockForUpdate()->first();

        $pphTrx = FinancialTransaction::create([
            'transaction_date' => $data['transaction_date'],
            'description'      => FinancialTransaction::AUTO_PPH_PREFIX . ' ' . $pphLabel . ' atas: ' . $data['description'],
            'account_id'       => $pphAccountId,
            'transaction_type' => 'kredit',
            'amount'           => $data['tax_amount'],
            'created_by'       => $data['created_by'] ?? Auth::id(),
        ]);

        $this->recalculateRunningBalance($pphAccountId, $pphTrx->transaction_date);

        return $pphTrx;
    }

    // ══════════════════════════════════════════════════════════════
    // Running Balance Recalculation
    // ══════════════════════════════════════════════════════════════

    /**
     * Recalculate running balance incrementally per account.
     */
    private function recalculateRunningBalance(int $accountId, $startDate = null): void
    {
        $initialBalance = '0.00';
        $query = FinancialTransaction::where('account_id', $accountId);

        if ($startDate) {
            $dateString = is_numeric($startDate)
                ? date('Y-m-d', $startDate)
                : \Carbon\Carbon::parse($startDate)->toDateString();

            $lastBefore = FinancialTransaction::where('account_id', $accountId)
                ->whereDate('transaction_date', '<', $dateString)
                ->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            if ($lastBefore) {
                $initialBalance = (string) $lastBefore->running_balance;
            }

            $query->whereDate('transaction_date', '>=', $dateString);
        }

        $transactions = $query->orderBy('transaction_date', 'asc')->orderBy('id', 'asc')->get();

        $balance     = $initialBalance;
        $dirtyMonths = [];

        foreach ($transactions as $trx) {
            $balance = $trx->transaction_type === 'debit'
                ? bcadd($balance, (string) $trx->amount, 2)
                : bcsub($balance, (string) $trx->amount, 2);

            $dirtyMonths[\Carbon\Carbon::parse($trx->transaction_date)->format('Y-m')] = true;

            $trx->running_balance = $balance;
            $trx->is_end_of_month = false;
            $trx->is_end_of_year  = false;
            $trx->saveQuietly();
        }

        foreach (array_keys($dirtyMonths) as $monthKey) {
            $year  = substr($monthKey, 0, 4);
            $month = substr($monthKey, 5, 2);

            $endOfMonthTrx = FinancialTransaction::where('account_id', $accountId)
                ->whereYear('transaction_date', $year)
                ->whereMonth('transaction_date', $month)
                ->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            if ($endOfMonthTrx) {
                $endOfMonthTrx->is_end_of_month = true;
                $endOfMonthTrx->is_end_of_year  = ($month == '12');
                $endOfMonthTrx->saveQuietly();
            }
        }
    }
}
