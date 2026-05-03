@extends('layouts.dashboard')

@section('content')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">Dashboard</h3>

    {{-- PROFILE DROPDOWN --}}
    <div class="dropdown">
        <a href="#" data-bs-toggle="dropdown"
           class="d-flex align-items-center text-decoration-none">
            <img
                src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}"
                class="rounded-circle"
                width="40"
            >
        </a>

        <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width:230px">

            {{-- DARK MODE --}}
            <li class="px-3 py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-moon-fill fs-5"></i>
                        <span>Mode Gelap</span>
                    </div>
                    <input class="form-check-input m-0"
                           type="checkbox"
                           id="darkToggle">
                </div>
            </li>

            <li><hr class="dropdown-divider"></li>

            {{-- PROFILE --}}
            <li>
                <a class="dropdown-item" href="{{ route('my-profile') }}">
                    <i class="bi bi-person me-2"></i> Data Pribadi
                </a>
            </li>

            {{-- LOGOUT --}}
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="dropdown-item text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>

{{-- ================= PAGE CONTENT ================= --}}
<div class="page-content">
@include('partials.dashboard-content')
@include('partials.kpi-trend-content')

{{-- ================= SCRIPT ================= --}}
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.getElementById("darkToggle");
    const html = document.documentElement;

    function applyTheme(theme){
        html.setAttribute("data-bs-theme", theme);
        toggle.checked = theme === "dark";
    }

    applyTheme(localStorage.getItem("theme") || "light");

    toggle.addEventListener("change", () => {
        const theme = toggle.checked ? "dark" : "light";
        localStorage.setItem("theme", theme);
        applyTheme(theme);
    });
});
</script>



@endsection
