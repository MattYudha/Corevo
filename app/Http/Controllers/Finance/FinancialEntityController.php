<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancialEntity;
use Illuminate\Http\Request;

class FinancialEntityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FinancialEntity::query();

        // Filter by search keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_info', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by type (ignore unknown types like 'employee')
        $validTypes = ['internal', 'bank', 'vendor', 'client', 'other'];
        if ($request->filled('type') && in_array($request->type, $validTypes)) {
            $query->where('type', $request->type);
        }

        $entities = $query->latest()->paginate(15)->withQueryString();

        return view('finance.entities.index', compact('entities'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('finance.entities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:internal,bank,vendor,client,other',
            'name' => 'required|string|max:255',
            'contact_info' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        // Check for duplicate (case-insensitive & trimmed)
        $name = trim($validated['name']);
        $exists = FinancialEntity::whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->where('type', $validated['type'])
            ->exists();

        if ($exists) {
            $msg = 'Entitas dengan nama dan tipe tersebut sudah ada.';
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg
                ], 422);
            }
            return back()->withInput()->with('error', $msg);
        }

        $entity = FinancialEntity::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Entitas berhasil ditambahkan.',
                'data' => $entity
            ]);
        }

        return redirect()->route('finance.entities.index')->with('success', 'Entitas kas & keuangan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FinancialEntity $entity)
    {
        return view('finance.entities.edit', compact('entity'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FinancialEntity $entity)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:internal,bank,vendor,client,other',
            'name' => 'required|string|max:255',
            'contact_info' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $entity->update($validated);

        return redirect()->route('finance.entities.index')->with('success', 'Entitas kas & keuangan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FinancialEntity $entity)
    {
        $user = auth()->user();
        $role = session('role');
        $isMasterAdmin = ($user->isMasterAdmin() || $role === \App\Constants\Roles::MASTER_ADMIN);
        $isFinance = ($user->isFinance() || $role === \App\Constants\Roles::FINANCE);

        if (!$isMasterAdmin && !$isFinance) {
            abort(403, 'Hanya Master Admin atau Finance yang dapat menghapus entitas.');
        }

        // Add check if there are associated transactions, prevent deletion if any (optional, but good practice).
        if ($entity->sentTransactions()->exists() || $entity->receivedTransactions()->exists()) {
            return redirect()->route('finance.entities.index')->with('error', 'Entitas tidak dapat dihapus karena memiliki transaksi yang terkait.');
        }

        $entity->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Entitas berhasil dihapus.'
            ]);
        }

        return redirect()->route('finance.entities.index')->with('success', 'Entitas kas & keuangan berhasil dihapus.');
    }
}
