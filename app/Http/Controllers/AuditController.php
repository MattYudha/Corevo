<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    private function authorizeAdmin()
    {
        if (!auth()->user()->isMasterAdmin()) {
            abort(403, 'Unauthorized: Only Master Admins can manage Audit Trail.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = AuditLog::with('user', 'auditable')->latest();

        // Date filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        $logs           = $query->paginate(20)->withQueryString();
        $totalLogs      = AuditLog::count();
        $auditEnabled   = AuditLog::isEnabled();

        return view('audit.index', compact('logs', 'totalLogs', 'auditEnabled'));
    }

    /**
     * Toggle audit logging on/off.
     */
    public function toggleEnabled(Request $request)
    {
        $this->authorizeAdmin();

        $current = AuditLog::isEnabled();
        AuditLog::setEnabled(!$current);

        $status = !$current ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('audit.index')->with('success', "Audit logging berhasil $status.");
    }

    /**
     * Purge audit logs by date range.
     */
    public function purge(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'purge_before' => 'required|date|before_or_equal:today',
        ]);

        $deleted = AuditLog::whereDate('created_at', '<=', $request->purge_before)->delete();

        return redirect()->route('audit.index')
            ->with('success', "$deleted log berhasil dihapus (sebelum {$request->purge_before}).");
    }
}
