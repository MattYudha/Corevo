<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancialAccount;
use Illuminate\Http\Request;

class FinancialAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $accounts = FinancialAccount::
            when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%"))
            ->when($request->category, fn($q, $c) => $q->where('category', $c))
            ->orderBy('code', 'asc')
            ->paginate(15);

        return view('finance.accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('finance.accounts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|in:asset,liability,equity,revenue,expense',
            'code' => 'required|string|max:50|unique:financial_accounts,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? $request->is_active : true;

        $account = FinancialAccount::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Akun berhasil ditambahkan.',
                'data' => $account
            ]);
        }

        return redirect()->route('finance.accounts.index')->with('success', 'Akun/CoA berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FinancialAccount $account)
    {
        return view('finance.accounts.edit', compact('account'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FinancialAccount $account)
    {
        $validated = $request->validate([
            'category' => 'required|string|in:asset,liability,equity,revenue,expense',
            'code' => 'required|string|max:50|unique:financial_accounts,code,' . $account->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $account->update($validated);

        return redirect()->route('finance.accounts.index')->with('success', 'Akun/CoA berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FinancialAccount $account)
    {
        // 1. Role Check (Master Admin / Finance)
        $role = session('role');
        $isAdmin = in_array(strtolower($role), ['master admin', 'super admin', 'finance']);
        $isAuthAdmin = (auth()->check() && (auth()->user()->isMasterAdmin() || auth()->user()->isFinance()));

        if (!$isAdmin && !$isAuthAdmin) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }
            abort(403);
        }

        // 2. Data Integrity Check
        if ($account->transactions()->exists()) {
            $msg = 'Akun tidak dapat dihapus karena sudah memiliki transaksi terkait.';
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $msg]);
            }
            return redirect()->route('finance.accounts.index')->with('error', $msg);
        }

        $account->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Akun/CoA berhasil dihapus.'
            ]);
        }

        return redirect()->route('finance.accounts.index')->with('success', 'Akun/CoA berhasil dihapus.');
    }
}
