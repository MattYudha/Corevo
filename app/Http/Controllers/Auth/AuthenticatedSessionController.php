<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Set role & employee id into session so views (sidebar) can check session('role')
        // This ensures menus that rely on session('role') will show after login.
        try {
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($user) {
                $employeeId = $user->employee_id ?? null;
                if ($employeeId) {
                    $employee = \App\Models\Employee::find($employeeId);
                    if ($employee && $employee->role) {
                        $request->session()->put('role', $employee->role->title);
                        $request->session()->put('employee_id', $employee->id);
                    }
                }
            }
        } catch (\Throwable $e) {
            // don't break login if session can't be set; menu will still safely fallback
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        // Explicitly clear custom session keys to prevent stale role data
        $request->session()->forget(['role', 'employee_id']);

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
