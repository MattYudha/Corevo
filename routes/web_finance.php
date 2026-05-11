<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Finance\FinancialTransactionController;
use App\Http\Controllers\Finance\FinancialEntityController;
use App\Http\Controllers\Finance\FinancialAccountController;
use App\Http\Controllers\Finance\FinancialReportController;
use App\Http\Controllers\Finance\ClaimController;
use App\Http\Controllers\Finance\PersonalFinanceController;
use App\Constants\Roles;

Route::middleware(['auth', 'verified'])->prefix('finance')->name('finance.')->group(function () {

    // ── 1. Full Admin / HR Access (CRUD Master Data & Full Transaksi) ────────────────
    Route::middleware(['role:' . Roles::MASTER_ADMIN . ',' . Roles::HR_ADMINISTRATOR . ',' . Roles::FINANCE])->group(function () {
        Route::resource('entities', FinancialEntityController::class);
        Route::resource('accounts', FinancialAccountController::class);

        // Admin also handles claim approvals
        Route::post('claims/{claim}/approve', [ClaimController::class, 'approve'])->name('claims.approve');
        Route::post('claims/{claim}/reject', [ClaimController::class, 'reject'])->name('claims.reject');
    });

    // ── 2. Finance Operator Access (Create Transaksi, View Master) ──────────────────
    // Grouping for Manager & Marketing
    Route::middleware(['role:' . Roles::MASTER_ADMIN . ',' . Roles::HR_ADMINISTRATOR . ',Manager / Unit Head,Marketing,' . Roles::FINANCE])->group(function () {
        // Can view master but not CRUD
        Route::get('entities', [FinancialEntityController::class, 'index'])->name('entities.index');
        Route::get('accounts', [FinancialAccountController::class, 'index'])->name('accounts.index');

        // Can create transactions
        Route::get('transactions/create', [FinancialTransactionController::class, 'create'])->name('transactions.create');
        Route::post('transactions', [FinancialTransactionController::class, 'store'])->name('transactions.store');
    });

    // ── 3. Finance View Access (Ledger, Reports, Charts) ──────────────────────────
    // For Admin, HR, Manager, Marketing, and Supervisor
    Route::middleware(['role:' . Roles::MASTER_ADMIN . ',' . Roles::HR_ADMINISTRATOR . ',Manager / Unit Head,Marketing,Supervisor,' . Roles::FINANCE])->group(function () {
        Route::get('transactions', [FinancialTransactionController::class, 'index'])->name('transactions.index');
        Route::get('reports', [FinancialReportController::class, 'index'])->name('reports.index');
        Route::get('charts', [FinancialReportController::class, 'charts'])->name('charts.index');
    });

    // ── 4. Transaction Edit/Delete (Admin + Finance) ──────────────────────────────
    Route::middleware(['role:' . Roles::MASTER_ADMIN . ',' . Roles::HR_ADMINISTRATOR . ',' . Roles::FINANCE])->group(function () {
        Route::get('transactions/{transaction}/edit', [FinancialTransactionController::class, 'edit'])->name('transactions.edit');
        Route::put('transactions/{transaction}', [FinancialTransactionController::class, 'update'])->name('transactions.update');
        Route::delete('transactions/{transaction}', [FinancialTransactionController::class, 'destroy'])->name('transactions.destroy');
    });

    // ── 5. Personal Finance & Claims (All Auth Users) ─────────────────────────────
    Route::get('my-finance', [PersonalFinanceController::class, 'index'])->name('my-finance');
    Route::resource('claims', ClaimController::class)->except(['destroy']);

    // ── 6. Secure Document Download (all roles that can view transactions) ─────────
    Route::get('transactions/{transaction}/document', [FinancialTransactionController::class, 'downloadDocument'])
        ->name('transactions.document')
        ->middleware(['role:' . Roles::MASTER_ADMIN . ',' . Roles::HR_ADMINISTRATOR . ',Manager / Unit Head,Marketing,Supervisor,' . Roles::FINANCE]);
});
