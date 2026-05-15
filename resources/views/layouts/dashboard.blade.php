<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>COREVO Aratech</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Expires" content="0">

    <link rel="shortcut icon" href="{{ asset('corevo-logo.png') }}" type="image/png">

    <!-- Mazer CSS -->
    <link rel="stylesheet" href="{{ asset('mazer/assets/compiled/css/app.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('mazer/assets/compiled/css/app-dark.css') }}?v={{ time() }}">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/responsive.bootstrap5.min.css') }}">

    <link rel="stylesheet" href="{{ asset('vendor/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">


    <style>
        /* ═══════════════════════════════════════════════════════
           ENTERPRISE SIDEBAR — HRIS Aratech
           ═══════════════════════════════════════════════════════ */

        /* ── Core Variables ── */
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #ffffff;
            --sidebar-border: rgba(0,0,0,0.06);
            --sidebar-active-bg: linear-gradient(135deg, #0d6efd, #0a58ca);
            --sidebar-active-color: #ffffff;
            --sidebar-hover-bg: rgba(13,110,253,0.06);
            --sidebar-text: #374151;
            --sidebar-muted: #9ca3af;
            --sidebar-group-color: #0d6efd;
            --sidebar-group-border: #0d6efd;
            --sidebar-item-radius: 8px;
            --sidebar-transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [data-bs-theme='dark'] {
            --sidebar-bg: #131929;
            --sidebar-border: rgba(255,255,255,0.06);
            --sidebar-hover-bg: rgba(99,179,237,0.08);
            --sidebar-text: #cbd5e1;
            --sidebar-muted: #64748b;
            --sidebar-group-color: #60a5fa;
            --sidebar-group-border: #3b82f6;
        }

        /* ── Sidebar Shell ── */
        #sidebar {
            width: var(--sidebar-width) !important;
            min-width: var(--sidebar-width) !important;
            max-width: var(--sidebar-width) !important;
            transition: width var(--sidebar-transition), transform var(--sidebar-transition);
            overflow: hidden;
        }

        .sidebar-wrapper {
            width: var(--sidebar-width) !important;
            background: var(--sidebar-bg) !important;
            border-right: 1px solid var(--sidebar-border) !important;
            box-shadow: 2px 0 20px rgba(0,0,0,0.04) !important;
            display: flex !important;
            flex-direction: column !important;
            height: 120vh !important;
            overflow: hidden !important;
            position: fixed !important;
            top: 0 !important;
            bottom: -20vh !important;
            left: 0 !important;
            z-index: 1050 !important;
            transition: width var(--sidebar-transition), transform var(--sidebar-transition) !important;
        }

        /* ── Logo Header ── */
        .sidebar-header {
            padding: 20px 16px 14px !important;
            border-bottom: 1px solid var(--sidebar-border);
            flex-shrink: 0;
        }

        .sidebar-header a {
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        #sidebar-logo {
            height: 64px !important;
            width: auto;
            object-fit: contain;
        }

        /* ── Scrollable Menu ── */
        .sidebar-menu {
            flex: 1 1 auto;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 8px 0 24px;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
        }

        .sidebar-wrapper .sidebar-menu::-webkit-scrollbar { width: 4px !important; }
        .sidebar-wrapper .sidebar-menu::-webkit-scrollbar-track { background: transparent !important; }
        .sidebar-wrapper .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.15) !important;
            border-radius: 10px !important;
        }
        [data-bs-theme='dark'] .sidebar-wrapper .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.25) !important;
        }

        /* Removed dark mode logo background as requested */

        .sidebar-menu .menu {
            padding: 0 10px;
            margin: 0;
            list-style: none;
        }

        /* ── Dashboard Link (top-level) ── */
        li.sidebar-item > a.sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px !important;
            border-radius: var(--sidebar-item-radius) !important;
            color: var(--sidebar-text);
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            letter-spacing: normal !important;
            font-family: inherit !important;
            text-decoration: none;
            transition: background var(--sidebar-transition), color var(--sidebar-transition);
            white-space: nowrap !important;
            overflow: hidden !important;
            margin: 2px 0 !important;
        }

        li.sidebar-item > a.sidebar-link i {
            font-size: 1rem;
            flex-shrink: 0;
            width: 20px;
            text-align: center;
            color: var(--sidebar-muted);
            transition: color var(--sidebar-transition);
        }

        li.sidebar-item > a.sidebar-link span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex: 1;
        }

        li.sidebar-item > a.sidebar-link:hover {
            background: var(--sidebar-hover-bg);
            color: var(--sidebar-group-color);
        }

        li.sidebar-item > a.sidebar-link:hover i {
            color: var(--sidebar-group-color);
        }

        /* ── Active state ── */
        li.sidebar-item.active > a.sidebar-link {
            background: var(--sidebar-active-bg) !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(13,110,253,0.3);
        }

        li.sidebar-item.active > a.sidebar-link i {
            color: #fff !important;
        }

        /* ── Menu Group (collapsible section) ── */
        .menu-group {
            background: transparent;
            border-left: none;
            border-radius: 0;
            margin: 4px 0 0;
            padding: 0;
            list-style: none;
        }

        .menu-group-header {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            color: var(--sidebar-group-color);
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity var(--sidebar-transition);
            border-radius: var(--sidebar-item-radius);
            margin: 0 0 2px;
        }

        .menu-group-header:hover { opacity: 0.75; }

        .menu-group-header .group-icon {
            font-size: 0.8rem;
            flex-shrink: 0;
            width: 16px;
            text-align: center;
        }

        .menu-group-header > span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex: 1;
        }

        .menu-group-header .chevron {
            margin-left: auto;
            font-size: 0.65rem;
            flex-shrink: 0;
            transition: transform 0.3s ease;
            opacity: 0.6;
        }

        .menu-group.expanded .menu-group-header .chevron {
            transform: rotate(90deg);
        }

        /* ── Collapsible Items ── */
        .menu-group .menu-group-items {
            list-style: none;
            padding: 0 0 0 8px;
            margin: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease, padding-bottom 0.3s ease;
        }

        .menu-group.expanded .menu-group-items {
            max-height: 800px;
            padding-bottom: 4px;
        }

        /* Sub-item links */
        .menu-group-items .sidebar-item > a.sidebar-link {
            padding: 8px 12px !important;
            font-size: 0.845rem !important;
            font-weight: 450 !important;
            gap: 9px !important;
        }

        .menu-group-items .sidebar-item > a.sidebar-link i {
            font-size: 0.9rem !important;
            width: 18px !important;
        }

        /* Divider inside groups */
        .menu-group-items hr {
            border-color: var(--sidebar-border);
            margin: 4px 8px;
            opacity: 1;
        }

        .logo-dark { display: none; }

        [data-bs-theme='dark'] .logo-light { display: none; }
        [data-bs-theme='dark'] .logo-dark { display: block; }
        
        /* ── Desktop Sidebar Layout ── */
        @media screen and (min-width: 992px) {
            #main { margin-left: var(--sidebar-width); transition: margin-left var(--sidebar-transition); }
            body.sidebar-collapsed #sidebar { width: 0 !important; min-width: 0 !important; }
            body.sidebar-collapsed .sidebar-wrapper { transform: translateX(calc(-1 * var(--sidebar-width))); }
            body.sidebar-collapsed #main { margin-left: 0; }
        }

        /* ── Sidebar Collapse Toggle (embedded «/» button) ── */
        .sidebar-collapse-btn {
            display: none;
        }

        /* Reopen tab — visible only when sidebar is collapsed on desktop */
        #sidebarReopenTab {
            display: none;
        }

        @media screen and (min-width: 992px) {
            .sidebar-collapse-btn {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                width: 28px !important;
                height: 28px !important;
                border-radius: 8px !important;
                border: 1px solid var(--sidebar-border) !important;
                background: transparent !important;
                color: var(--sidebar-muted) !important;
                cursor: pointer !important;
                transition: background var(--sidebar-transition), color var(--sidebar-transition), border-color var(--sidebar-transition) !important;
                flex-shrink: 0 !important;
                font-size: 1.1rem !important;
                font-weight: 400 !important;
                padding: 0 !important;
                line-height: 0 !important;
                margin: 0 !important;
            }
            .sidebar-collapse-btn:hover {
                background: var(--sidebar-group-color);
                color: #fff;
                border-color: var(--sidebar-group-color);
            }

            /* Reopen tab — slim vertical pill on left edge when sidebar hidden */
            #sidebarReopenTab {
                display: flex;
                position: fixed;
                top: 50%;
                left: -48px;
                transform: translateY(-50%);
                z-index: 1200;
                align-items: center;
                justify-content: center;
                width: 20px;
                height: 56px;
                border-radius: 0 8px 8px 0;
                background: var(--sidebar-group-color);
                color: #fff;
                cursor: pointer;
                border: none;
                box-shadow: 2px 0 12px rgba(13,110,253,0.3);
                transition: left var(--sidebar-transition), background 0.2s;
                font-size: 0.75rem;
                font-weight: 700;
                letter-spacing: -1px;
                writing-mode: horizontal-tb;
                opacity: 0;
                pointer-events: none;
            }
            body.sidebar-collapsed #sidebarReopenTab {
                left: 0;
                opacity: 1;
                pointer-events: auto;
            }
            #sidebarReopenTab:hover {
                background: #0a58ca;
                width: 26px;
            }
        }

        /* ── Mobile Sidebar ── */
        @media screen and (max-width: 991px) {
            #sidebar, .sidebar-wrapper {
                z-index: 1050 !important;
            }
        }

        /* ── Mobile Nav Header ── */
        .mobile-nav-header {
            position: sticky;
            top: 0;
            z-index: 9;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            margin-left: -1rem;
            margin-right: -1rem;
        }

        .mobile-top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .app-brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .mobile-burger-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            color: #374151;
            text-decoration: none;
            cursor: pointer;
            background: transparent;
            border: none;
            border-radius: 8px;
            transition: background 0.15s;
        }

        .mobile-burger-btn:hover { background: rgba(0,0,0,0.05); }
        .mobile-burger-btn i { font-size: 1.6rem; line-height: 1; }

        [data-bs-theme='dark'] .mobile-nav-header {
            background: rgba(19, 25, 41, 0.9);
            border-bottom-color: rgba(255,255,255,0.05);
        }
        [data-bs-theme='dark'] .mobile-burger-btn { color: #cbd5e1; }
        [data-bs-theme='dark'] .app-brand span { color: #f1f5f9 !important; }

        /* ── Fix: sidebar-wrapper must never shrink text in dark mode ── */
        .sidebar-wrapper,
        [data-bs-theme='dark'] .sidebar-wrapper {
            width: var(--sidebar-width) !important;
            min-width: var(--sidebar-width) !important;
        }

        /* ── Logout item red styling ── */
        .sidebar-item--danger > a.sidebar-link {
            color: #ef4444 !important;
        }
        .sidebar-item--danger > a.sidebar-link i {
            color: #ef4444 !important;
        }
        .sidebar-item--danger > a.sidebar-link:hover {
            background: rgba(239,68,68,0.08) !important;
        }

        /* ── Mobile vs Desktop Separation ── */
        @media screen and (max-width: 991.98px) {
            body { zoom: 1 !important; }

            /* Sidebar on mobile: slide OFF-screen by default, slide IN when .active is added by Mazer */
            #sidebar {
                display: block !important; /* Keep visible so Mazer can toggle it */
                width: 0 !important;
                min-width: 0 !important;
            }
            .sidebar-wrapper {
                height: 100dvh !important; /* Use dynamic viewport height on mobile */
                bottom: 0 !important;
                top: 0 !important;
                transform: translateX(-100%) !important; /* Hidden off-screen by default */
                transition: transform 0.3s ease !important;
                z-index: 1060 !important;
            }
            .sidebar-wrapper.active {
                transform: translateX(0) !important; /* Slide in when active */
            }
            #main {
                margin-left: 0 !important;
            }
            #sidebarReopenTab {
                display: none !important;
            }
        }
        /* On desktop: apply zoom and show sidebar */
        @media screen and (min-width: 992px) {
            body { zoom: 0.9; }
        }
    </style>

    @stack('styles')
</head>

<body>
<script src="{{ asset('mazer/assets/static/js/initTheme.js') }}"></script>

<div id="app">

    {{-- Slim reopen tab — appears at left edge when sidebar is collapsed on desktop --}}
    <button id="sidebarReopenTab" title="Buka Sidebar" aria-label="Buka Sidebar">
        &raquo;
    </button>

    <!-- ================= SIDEBAR ================= -->
    <div id="sidebar">
        {{-- DO NOT hardcode 'active' here - JS will add it only on desktop --}}
        <div class="sidebar-wrapper">

            <div class="sidebar-header" style="padding: 18px 14px 14px !important; border-bottom: 1px solid var(--sidebar-border); flex-shrink: 0; position: relative;">
                {{-- Logo — centered --}}
                <div class="d-flex justify-content-center align-items-center w-100">
                    <a href="{{ url('/dashboard') }}" class="d-inline-flex align-items-center justify-content-center text-decoration-none">
                            <img src="{{ asset('corevo-logo.png') }}" id="sidebar-logo" class="logo-light" style="height:80px; width:auto; max-width:150px; object-fit:contain;">
                            <img src="{{ asset('corevo-logo-white.png') }}" id="sidebar-logo" class="logo-dark" style="height:80px; width:auto; max-width:150px; object-fit:contain;">
                    </a>
                </div>

{{-- «/» collapse button — absolute right so it does NOT affect centering --}}
                <button class="sidebar-collapse-btn burger-btn" id="sidebarCollapseBtn"
                        title="Tutup Sidebar" aria-label="Toggle Sidebar"
                        style="position:absolute; top:50%; right:14px; transform:translateY(-50%);">
                    &laquo;
                </button>
            </div>

            <div class="sidebar-menu">
                <ul class="menu">

                    @php
                        $user = Auth::user();
                        $role = session('role');
                        $isMasterAdmin = $role === \App\Constants\Roles::MASTER_ADMIN;
                        $isAdmin = in_array($role, \App\Constants\Roles::ADMIN_ROLES);
                        $isManager = $role === \App\Constants\Roles::MANAGER_UNIT_HEAD;
                        $isSupervisor = $role === \App\Constants\Roles::SUPERVISOR;
                        $isEmployee = $role === \App\Constants\Roles::EMPLOYEE;
                        $isMarketing = $role === \App\Constants\Roles::MARKETING;
                        $isFinanceRole = $role === \App\Constants\Roles::FINANCE;

                        $isDevOrAdmin = $isAdmin || $isMasterAdmin;
                        $isManagerOrAdmin = $isAdmin || $isMasterAdmin || $isManager;
                        $isMarketingOrAdmin = $isAdmin || $isMasterAdmin || $isMarketing;
                        $isStaff = $isManagerOrAdmin || $isSupervisor || $isEmployee || $isMarketing;

                        $activeDashboard = request()->is('dashboard');
                        $activePayroll = request()->is('payrolls*');
                        $activeEmployees = request()->is('employees*');
                        $activeEmployeeApprovals = request()->is('employee-approvals*');
                        $activeDepartments = request()->is('departments*');
                        $activeOfficeLocations = request()->is('office-locations*');
                        $activeRoles = request()->is('roles*');
                        $activeTasks = request()->is('tasks*');
                        $activeLeaveRequests = request()->is('leave-requests*');
                        $activeIncidents = request()->is('incidents*');

                        $activeKpiDashboard = request()->is('kpi/dashboard*') || request()->is('kpi-dashboard*');
                        $activeKpiTeam = request()->is('kpi/team*');
                        $activeKpiDepartment = request()->is('kpi/department*');
                        $activeKpiPending = request()->is('kpi/pending*') || request()->is('kpi/pending-approvals*');

                        $activeFinanceTransactions = request()->is('finance/transactions*');
                        $activeFinanceEntities = request()->is('finance/entities*');
                        $activeFinanceAccounts = request()->is('finance/accounts*');
                        $activeFinanceReports = request()->is('finance/reports*');
                        $activeFinanceCharts = request()->is('finance/charts*');
                        $activeFinanceClaims = request()->is('finance/claims*');
                        $activeMyFinance = request()->is('finance/my-finance*');

                        $activeInventoryCategories = request()->is('inventory-categories*');
                        $activeInventories = request()->is('inventories*');
                        $activeInventoryUsage = request()->is('inventory-usage-logs*');
                        $activeInventoryRequests = request()->is('inventory-requests*');
                        $activeVendors = request()->is('vendors*');
                        $activeProcurements = request()->is('procurements*');
                        $activeInventoryDispatches = request()->is('inventory-dispatches*');
                        $activeLogisticsShipments = request()->is('logistics-shipments*');

                        $activeLetters = request()->is('letters*');
                        $activeLetterTemplates = request()->is('letter-templates*');
                        $activeLetterConfigs = request()->is('letter-configurations*');
                        $activeLetterArchives = request()->is('letter-archives*');
                        $activeSignatureLogs = request()->is('signature-logs*');

                        $activeReportsExec = request()->is('reports/executive*');
                        $activeReportsMonthly = request()->is('reports/monthly-recap*');

                        // Work Log Menu
                        $activeWorkLogs = request()->is('work-logs*');

                        // Overtime Menu
                        $overtimes = request()->is('overtimes*');

                        // Dynamic visibility checks
                        $hasInventoryAccess = $user->hasAnyAccess(['inventory', 'inventory_logs', 'inventory_usage', 'inventory_requests']);
                        $hasKpiAccess = $user->hasAccess('hr_reports');
                        $hasAttendanceAccess = $user->hasAccess('attendance');

                        // Visibility helpers
                        $isMasterAdmin = $user->isMasterAdmin();
                        $showPayrollGroup = $isMasterAdmin || $isAdmin || $isFinanceRole || $isManager || $hasKpiAccess;
                        $showInventoryLogs = $isMasterAdmin || $isAdmin || $isManager || $user->hasAccess('inventory_logs');
                        $showInventoryAdmin = $isMasterAdmin || $isAdmin || $isFinanceRole || $user->hasAccess('inventory');

                        $systemMenuActive = $activeRoles || request()->is('audit-trail*') || request()->is('system*');
                        $hrMenuActive = $activeEmployees || $activeEmployeeApprovals || $activeDepartments || $activeOfficeLocations || $activeTasks || $activeLeaveRequests || $activeIncidents;
                        $kpiMenuActive = $activeKpiDashboard || $activeKpiTeam || $activeKpiDepartment || $activeKpiPending;
                        $financeMenuActive = $activeFinanceTransactions || $activeFinanceEntities || $activeFinanceAccounts || $activeFinanceReports || $activeFinanceCharts || $activeFinanceClaims;
                        $inventoryMenuActive = $activeInventoryCategories || $activeInventories || $activeInventoryUsage || $activeInventoryRequests || $activeVendors || $activeProcurements || $activeInventoryDispatches || $activeLogisticsShipments;
                        $lettersMenuActive = $activeLetters || $activeLetterTemplates || $activeLetterConfigs || $activeLetterArchives || $activeSignatureLogs;
                        $reportsMenuActive = $activeReportsExec || $activeReportsMonthly;
                        $personalMenuActive = request()->is('my-profile') || request()->is('presences') || request()->is('knowledge-base*') || $activeMyFinance || $activeFinanceClaims || $activeWorkLogs || $overtimes;
                    @endphp

                    <!-- DASHBOARD -->
                    <li class="sidebar-item {{ $activeDashboard ? 'active' : '' }}">
                        <a href="{{ url('/dashboard') }}" class="sidebar-link">
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <!-- SYSTEM SETTINGS (Master Admin Only) -->
                    @if($isMasterAdmin)
                    <li class="menu-group expanded">
                        <div class="menu-group-header">
                            <i class="bi bi-gear-fill group-icon"></i>
                            <span>System Settings</span>
                            <i class="bi bi-chevron-right chevron"></i>
                        </div>
                        <ul class="menu-group-items">
                            <li class="sidebar-item {{ $activeRoles ? 'active' : '' }}">
                                <a href="{{ url('/roles') }}" class="sidebar-link">
                                    <i class="bi bi-shield-lock"></i>
                                    <span>Roles & Permissions</span>
                                </a>
                            </li>
                            <li class="sidebar-item {{ request()->is('audit-trail*') ? 'active' : '' }}">
                                <a href="{{ url('/audit-trail') }}" class="sidebar-link">
                                    <i class="bi bi-history"></i>
                                    <span>Audit Trail</span>
                                </a>
                            </li>
                            <li class="sidebar-item {{ request()->is('system*') ? 'active' : '' }}">
                                <a href="{{ url('/system') }}" class="sidebar-link">
                                    <i class="bi bi-cpu"></i>
                                    <span>System Management</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif

                    <!-- HR Administrator MANAGEMENT -->
                    <li class="menu-group expanded">
                        <div class="menu-group-header">
                            <i class="bi bi-people-fill group-icon"></i>
                            <span>HR Administrator Management</span>
                            <i class="bi bi-chevron-right chevron"></i>
                        </div>
                        <ul class="menu-group-items">
                            @if($isAdmin || $isManager)
                            <li class="sidebar-item {{ $activeEmployees ? 'active' : '' }}">
                                <a href="{{ url('/employees') }}" class="sidebar-link">
                                    <i class="bi bi-people-fill"></i>
                                    <span>Employees</span>
                                </a>
                            </li>
                            @endif
                            @if($isAdmin || $isMasterAdmin)
                            <li class="sidebar-item {{ $activeEmployeeApprovals ? 'active' : '' }}">
                                <a href="{{ url('/employee-approvals') }}" class="sidebar-link">
                                    <i class="bi bi-check-circle"></i>
                                    <span>Update Approvals</span>
                                </a>
                            </li>
                            <li class="sidebar-item {{ $activeDepartments ? 'active' : '' }}">
                                <a href="{{ url('/departments') }}" class="sidebar-link">
                                    <i class="bi bi-building"></i>
                                    <span>Departments</span>
                                </a>
                            </li>
                            <li class="sidebar-item {{ $activeOfficeLocations ? 'active' : '' }}">
                                <a href="{{ url('/office-locations') }}" class="sidebar-link">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <span>Office Locations</span>
                                </a>
                            </li>
                            @endif
                            <li class="sidebar-item {{ $activeTasks ? 'active' : '' }}">
                                <a href="{{ url('/tasks') }}" class="sidebar-link">
                                    <i class="bi bi-list-task"></i>
                                    <span>Tasks</span>
                                </a>
                            </li>
                            <li class="sidebar-item {{ $activeLeaveRequests ? 'active' : '' }}">
                                <a href="{{ url('/leave-requests') }}" class="sidebar-link">
                                    <i class="bi bi-calendar-x"></i>
                                    <span>Leave Requests</span>
                                </a>
                            </li>
                            <li class="sidebar-item {{ $activeIncidents ? 'active' : '' }}">
                                <a href="{{ url('/incidents') }}" class="sidebar-link">
                                    <i class="bi bi-award"></i>
                                    <span>Incidents & Awards</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- PAYROLL & KPI -->
                    @if($showPayrollGroup)
                    <li class="menu-group expanded">
                        <div class="menu-group-header">
                            <i class="bi bi-cash-stack group-icon"></i>
                            <span>Payroll & KPI</span>
                            <i class="bi bi-chevron-right chevron"></i>
                        </div>
                        <ul class="menu-group-items">
                            @if($isAdmin || $isMasterAdmin || $isFinanceRole)
                            <li class="sidebar-item {{ $activePayroll ? 'active' : '' }}">
                                <a href="{{ url('/payrolls') }}" class="sidebar-link">
                                    <i class="bi bi-currency-dollar"></i>
                                    <span>Payrolls</span>
                                </a>
                            </li>
                            @endif
                            @if($user->hasAccess('hr_reports') || $isAdmin || $isManager)
                            <li class="sidebar-item {{ $activeKpiDashboard ? 'active' : '' }}">
                                <a href="{{ url('/kpi/dashboard') }}" class="sidebar-link">
                                    <i class="bi bi-speedometer2"></i>
                                    <span>KPI Dashboard</span>
                                </a>
                            </li>
                            @endif
                            @if($isMasterAdmin || $isManager)
                            <li class="sidebar-item {{ $activeKpiTeam ? 'active' : '' }}">
                                <a href="{{ url('/kpi/team') }}" class="sidebar-link">
                                    <i class="bi bi-people"></i>
                                    <span>Team KPI</span>
                                </a>
                            </li>
                            <li class="sidebar-item {{ $activeKpiDepartment ? 'active' : '' }}">
                                <a href="{{ url('/kpi/department') }}" class="sidebar-link">
                                    <i class="bi bi-diagram-3"></i>
                                    <span>Department KPI</span>
                                </a>
                            </li>
                            <li class="sidebar-item {{ $activeKpiPending ? 'active' : '' }}">
                                <a href="{{ url('/kpi/pending') }}" class="sidebar-link">
                                    <i class="bi bi-hourglass-split"></i>
                                    <span>Pending Approvals</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    <!-- BUKU KAS & INVENTORY MODULE -->
                    @if($isAdmin || $isMasterAdmin || $isManager || $isMarketing || $isSupervisor || $isFinanceRole || $hasInventoryAccess)
                    <li class="menu-group expanded">
                        <div class="menu-group-header">
                            <i class="bi bi-wallet2 group-icon"></i>
                            <span>Finance & Inventory</span>
                            <i class="bi bi-chevron-right chevron"></i>
                        </div>
                        <ul class="menu-group-items">
                            @if($isAdmin || $isMasterAdmin || $isManager || $isMarketing || $isFinanceRole)
                            <li class="sidebar-item {{ $activeFinanceTransactions ? 'active' : '' }}">
                                <a href="{{ url('/finance/transactions') }}" class="sidebar-link">
                                    <i class="bi bi-pencil-square"></i>
                                    <span>Cashbook (Ledger)</span>
                                </a>
                            </li>
                            @endif
                            
                            @if($isAdmin || $isMasterAdmin || $isManager || $isMarketing || $isFinanceRole)
                            <li class="sidebar-item {{ $activeFinanceEntities ? 'active' : '' }}">
                                <a href="{{ url('/finance/entities') }}" class="sidebar-link">
                                    <i class="bi bi-building"></i>
                                    <span>Master Entities</span>
                                </a>
                            </li>
                            <li class="sidebar-item {{ $activeFinanceAccounts ? 'active' : '' }}">
                                <a href="{{ url('/finance/accounts') }}" class="sidebar-link">
                                    <i class="bi bi-journal-bookmark"></i>
                                    <span>Account Categories (CoA)</span>
                                </a>
                            </li>
                            @endif

                            <li class="sidebar-item {{ $activeFinanceReports ? 'active' : '' }}">
                                <a href="{{ url('/finance/reports') }}" class="sidebar-link">
                                    <i class="bi bi-file-earmark-spreadsheet"></i>
                                    <span>Financial Reports</span>
                                </a>
                            </li>

                            @if($isAdmin || $isMasterAdmin || $isManager || $isMarketing || $isFinanceRole)
                            <li class="sidebar-item {{ $activeFinanceCharts ? 'active' : '' }}">
                                <a href="{{ url('/finance/charts') }}" class="sidebar-link">
                                    <i class="bi bi-graph-up-arrow"></i>
                                    <span>Analytical Charts</span>
                                </a>
                            </li>
                            @endif

                            {{-- Admin: Claim Management --}}
                            @if($isAdmin || $isMasterAdmin)
                            <li class="sidebar-item {{ $activeFinanceClaims ? 'active' : '' }}">
                                <a href="{{ url('/finance/claims') }}" class="sidebar-link">
                                    <i class="bi bi-clipboard-check"></i>
                                    <span>Manage Expense Claims</span>
                                </a>
                            </li>
                            @endif
                            @if($hasInventoryAccess || $showInventoryAdmin || $showInventoryLogs)
                            <hr class="mx-3 my-1 border-light opacity-25">
                            <li class="px-3 text-xs fw-bold mt-2 mb-1" style="color:var(--sidebar-muted);">INVENTORY</li>
                            @endif
                            @if($showInventoryAdmin)
                            <li class="sidebar-item {{ $activeInventoryCategories ? 'active' : '' }}">
                                <a href="{{ url('/inventory-categories') }}" class="sidebar-link">
                                    <i class="bi bi-tags"></i>
                                    <span>Categories</span>
                                </a>
                            </li>
                            <li class="sidebar-item {{ $activeInventories ? 'active' : '' }}">
                                <a href="{{ url('/inventories') }}" class="sidebar-link">
                                    <i class="bi bi-boxes"></i>
                                    <span>Inventories</span>
                                </a>
                            </li>
                            @endif
                            @if($showInventoryLogs)
                            <li class="sidebar-item {{ $activeInventoryUsage ? 'active' : '' }}">
                                <a href="{{ url('/inventory-usage-logs') }}" class="sidebar-link">
                                    <i class="bi bi-journal-text"></i>
                                    <span>Usage Logs</span>
                                </a>
                            </li>
                            @endif
                            <li class="sidebar-item {{ $activeInventoryRequests ? 'active' : '' }}">
                                <a href="{{ url('/inventory-requests') }}" class="sidebar-link">
                                    <i class="bi bi-cart-plus"></i>
                                    <span>Requests</span>
                                </a>
                            </li>
                            @if($isMasterAdmin || $isFinanceRole || $user->hasAccess('inventory'))
                            <hr class="mx-3 my-1 border-light opacity-25">
                            <li class="sidebar-item {{ $activeVendors ? 'active' : '' }}">
                                <a href="{{ url('/vendors') }}" class="sidebar-link">
                                    <i class="bi bi-shop"></i>
                                    <span>Vendors</span>
                                </a>
                            </li>
                            <li class="sidebar-item {{ $activeProcurements ? 'active' : '' }}">
                                <a href="{{ url('/procurements') }}" class="sidebar-link">
                                    <i class="bi bi-file-earmark-medical"></i>
                                    <span>Procurements</span>
                                </a>
                            </li>
                            <li class="sidebar-item {{ $activeInventoryDispatches ? 'active' : '' }}">
                                <a href="{{ url('/inventory-dispatches') }}" class="sidebar-link">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Releases & Barcode</span>
                                </a>
                            </li>
                            <li class="sidebar-item {{ $activeLogisticsShipments ? 'active' : '' }}">
                                <a href="{{ url('/logistics-shipments') }}" class="sidebar-link">
                                    <i class="bi bi-truck"></i>
                                    <span>Logistics Tracking</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    <!-- LETTERS -->
                    <li class="menu-group expanded">
                        <div class="menu-group-header">
                            <i class="bi bi-envelope-fill group-icon"></i>
                            <span>Letters</span>
                            <i class="bi bi-chevron-right chevron"></i>
                        </div>
                        <ul class="menu-group-items">
                            <li class="sidebar-item {{ $activeLetters ? 'active' : '' }}">
                                <a href="{{ url('/letters') }}" class="sidebar-link">
                                    <i class="bi bi-envelope-fill"></i>
                                    <span>Letters</span>
                                </a>
                            </li>
                            @if($isAdmin)
                            <li class="sidebar-item {{ $activeLetterTemplates ? 'active' : '' }}">
                                <a href="{{ url('/letter-templates') }}" class="sidebar-link">
                                    <i class="bi bi-file-earmark-ruled"></i>
                                    <span>Templates</span>
                                </a>
                            </li>
                            <li class="sidebar-item {{ $activeLetterConfigs ? 'active' : '' }}">
                                <a href="{{ url('/letter-configurations') }}" class="sidebar-link">
                                    <i class="bi bi-gear"></i>
                                    <span>Configurations</span>
                                </a>
                            </li>
                            @endif
                            @if($isDevOrAdmin)
                            <li class="sidebar-item {{ $activeLetterArchives ? 'active' : '' }}">
                                <a href="{{ url('/letter-archives') }}" class="sidebar-link">
                                    <i class="bi bi-archive"></i>
                                    <span>Archives</span>
                                </a>
                            </li>
                            @endif
                            <li class="sidebar-item {{ $activeSignatureLogs ? 'active' : '' }}">
                                <a href="{{ url('/signature-logs') }}" class="sidebar-link">
                                    <i class="bi bi-pen"></i>
                                    <span>Signature Logs</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- REPORTS -->
                    @if($isManagerOrAdmin)
                    <li class="menu-group expanded">
                        <div class="menu-group-header">
                            <i class="bi bi-file-earmark-text group-icon"></i>
                            <span>Reports</span>
                            <i class="bi bi-chevron-right chevron"></i>
                        </div>
                        <ul class="menu-group-items">
                            @if($isAdmin)
                            <li class="sidebar-item {{ $activeReportsExec ? 'active' : '' }}">
                                <a href="{{ url('/reports/executive') }}" class="sidebar-link">
                                    <i class="bi bi-graph-up"></i>
                                    <span>Executive Report</span>
                                </a>
                            </li>
                            @endif
                            <li class="sidebar-item {{ $activeReportsMonthly ? 'active' : '' }}">
                                <a href="{{ url('/reports/monthly-recap') }}" class="sidebar-link">
                                    <i class="bi bi-file-earmark-text"></i>
                                    <span>Monthly Report</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif

                    <!-- PERSONAL -->
                    <li class="menu-group expanded">
                        <div class="menu-group-header">
                            <i class="bi bi-person-fill group-icon"></i>
                            <span>Personal</span>
                            <i class="bi bi-chevron-right chevron"></i>
                        </div>
                        <ul class="menu-group-items">

                            <li class="sidebar-item {{ request()->is('my-profile') ? 'active' : '' }}">
                                <a href="{{ url('/my-profile') }}" class="sidebar-link">
                                    <i class="bi bi-person-fill"></i>
                                    <span>My Profile</span>
                                </a>
                            </li>

                            <li class="sidebar-item {{ request()->is('presences') ? 'active' : '' }}">
                                <a href="{{ url('/presences') }}" class="sidebar-link">
                                    <i class="bi bi-table"></i>
                                    <span>Presences</span>
                                </a>
                            </li>

                            <li class="sidebar-item {{ request()->is('knowledge-base*') ? 'active' : '' }}">
                                <a href="{{ url('/knowledge-base') }}" class="sidebar-link">
                                    <i class="bi bi-book"></i>
                                    <span>Knowledge Base</span>
                                </a>
                            </li>

                            <hr class="mx-3 my-1 border-light opacity-25">

                            <li class="sidebar-item {{ $activeWorkLogs ? 'active' : '' }}">
                                <a href="{{ route('work-logs.index') }}" class="sidebar-link">
                                    <i class="bi bi-journal-check"></i>
                                    <span>My Activities</span>
                                </a>
                            </li>
                            
                            <li class="sidebar-item {{ $overtimes ? 'active' : '' }}">
                                <a href="{{ route('overtimes.index') }}" class="sidebar-link">
                                    <i class="bi bi-clock"></i>
                                    <span>My Overtime</span>
                                </a>
                            </li>

                            <li class="sidebar-item {{ $activeMyFinance ? 'active' : '' }}">
                                <a href="{{ url('/finance/my-finance') }}" class="sidebar-link">
                                    <i class="bi bi-wallet-fill"></i>
                                    <span>My Finance</span>
                                </a>
                            </li>
                            <li class="sidebar-item {{ $activeFinanceClaims ? 'active' : '' }}">
                                <a href="{{ url('/finance/claims') }}" class="sidebar-link">
                                    <i class="bi bi-receipt"></i>
                                    <span>My Expense Claims</span>
                                </a>
                            </li>
                            <li class="sidebar-item sidebar-item--danger">
                                <a href="{{ url('/logout') }}" class="sidebar-link">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Logout</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>

        </div>
    </div>
    <!-- ================= END SIDEBAR ================= -->

    <!-- ================= MAIN ================= -->
    <div id="main">

        <header class="mobile-nav-header d-xl-none">
            <div class="mobile-top-bar">
                <div class="app-brand">
                    <img src="{{ asset('corevo-logo.png') }}" alt="Logo" class="logo-light" style="height: 30px;" onerror="this.style.display='none'">
                    <img src="{{ asset('corevo-logo-white.png') }}" alt="Logo" class="logo-dark" style="height: 30px;" onerror="this.style.display='none'">
                    {{-- <span class="fw-bolder" style="color: #1a1f3c; font-size: 1.05rem; letter-spacing: -0.02em;">Aratech </span> --}}
                </div>
                <button class="burger-btn mobile-burger-btn" aria-label="Buka menu navigasi">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </header>

        @yield('content')

        <footer class="footer clearfix mb-0 text-muted">
            <div class="float-start">
                <p>2025 &copy; PT Aratech Nusantara Indonesia</p>
            </div>
            <div class="float-end">
                <p>Crafted by <a href="https://aratechnology.id">Aratech</a></p>
            </div>
        </footer>
    </div>

</div>

<!-- ================= JS ================= -->
<script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/responsive.bootstrap5.min.js') }}"></script>
<script src="{{ asset('mazer/assets/compiled/js/app.js') }}?v={{ time() }}"></script>
<script src="{{ asset('vendor/flatpickr/flatpickr.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const bodyEl = document.body;
    const sidebarWrapper = $('#sidebar .sidebar-wrapper');
    const DESKTOP_BREAKPOINT = 992;

    // ── Restore sidebar state from localStorage ──────────────────────
    const SIDEBAR_KEY = 'hris_sidebar_collapsed';
    if (window.innerWidth >= DESKTOP_BREAKPOINT) {
        if (localStorage.getItem(SIDEBAR_KEY) === '1') {
            bodyEl.classList.add('sidebar-collapsed');
        }
    }

    function isDesktopViewport() {
        return window.innerWidth >= DESKTOP_BREAKPOINT;
    }

    function syncSidebarLayout() {
        // Only force open if not explicitly collapsed by user (optional, but keep it simple for now as requested)
        if (isDesktopViewport()) {
            if (!bodyEl.classList.contains('sidebar-collapsed')) {
                sidebarWrapper.addClass('active');
            }
            return;
        }

        sidebarWrapper.removeClass('active');
    }

    syncSidebarLayout();
    $(window).on('resize', syncSidebarLayout);

    $(document).on('click', '.burger-btn', function(e) {
        e.preventDefault();

        if (isDesktopViewport()) {
            bodyEl.classList.toggle('sidebar-collapsed');
            localStorage.setItem(SIDEBAR_KEY, bodyEl.classList.contains('sidebar-collapsed') ? '1' : '0');
        } else {
            sidebarWrapper.toggleClass('active');
        }
    });

    // Desktop toggle — now handled by burger-btn class (see above)
    // Also wire reopen tab
    document.getElementById('sidebarReopenTab')?.addEventListener('click', function() {
        bodyEl.classList.remove('sidebar-collapsed');
        localStorage.setItem(SIDEBAR_KEY, '0');
        syncCollapseIcon();
    });

    function syncCollapseIcon() {
        const btn = document.getElementById('sidebarCollapseBtn');
        if (!btn) return;
        const isCollapsed = bodyEl.classList.contains('sidebar-collapsed');
        btn.innerHTML = isCollapsed ? '&raquo;' : '&laquo;';
        btn.title = isCollapsed ? 'Buka Sidebar' : 'Tutup Sidebar';
    }

    // Sync icon on load
    syncCollapseIcon();

    // Sync icon after burger-btn click
    $(document).on('click', '.burger-btn', function() {
        setTimeout(syncCollapseIcon, 50);
    });

    // Close mobile sidebar when clicking outside
    $(document).on('click', function(e) {
        if (window.innerWidth < 1200) {
            if (!$(e.target).closest('#sidebar').length && !$(e.target).closest('.burger-btn').length) {
                sidebarWrapper.removeClass('active');
            }
        }
    });

    flatpickr(".date", { dateFormat: "Y-m-d" });

    // Global AJAX Setup for CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Global DataTable Defaults
    $.extend(true, $.fn.dataTable.defaults, {
        responsive: true,
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Data tidak ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            search: "Cari:",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        },
        drawCallback: function(settings) {
            // Re-initialize tooltips or any global UI components if needed
        }
    });

    // Make all standard tables responsive by default on mobile
    $(document).ready(function() {
        $('table.table').each(function() {
            if (!$(this).parent().hasClass('table-responsive') && !$(this).parent().hasClass('dt-scroll')) {
                $(this).wrap('<div class="table-responsive"></div>');
            }
        });
    });

    // Handle AJAX Errors Globally
    $(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
        if (jqXHR.status === 403) {
            Swal.fire('Unauthorized', 'Anda tidak memiliki akses untuk tindakan ini.', 'error');
        } else if (jqXHR.status === 500) {
            Swal.fire('Server Error', 'Terjadi kesalahan pada server. Silakan coba lagi nanti.', 'error');
        }
    });

    // Global Delete Confirmation Helper
    window.confirmDelete = function(formOrUrl, message = 'Apakah Anda yakin ingin menghapus data ini?') {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                if (typeof formOrUrl === 'string') {
                    window.location.href = formOrUrl;
                } else {
                    formOrUrl.submit();
                }
            }
        });
        return false;
    };

    // Menu group collapse/expand toggle
    document.querySelectorAll('.menu-group-header').forEach(function(header) {
        header.addEventListener('click', function() {
            this.closest('.menu-group').classList.toggle('expanded');
        });
    });

    // Theme Toggle Logic (with null-safety)
    const themeToggle = document.getElementById('theme-toggle');
    const themeToggleIcon = document.getElementById('theme-toggle-icon');

    function updateIcon(theme) {
        if (!themeToggleIcon) return;
        if (theme === 'dark') {
            themeToggleIcon.className = 'bi bi-moon-fill';
        } else {
            themeToggleIcon.className = 'bi bi-sun-fill';
        }
    }

    // Sync icon on load
    updateIcon(localStorage.getItem('theme') || 'light');

    if (themeToggle) {
        themeToggle.addEventListener('click', (e) => {
            e.preventDefault();
            const currentTheme = localStorage.getItem('theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            localStorage.setItem('theme', newTheme);

            if (newTheme === 'dark') {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.setAttribute('data-bs-theme', 'light');
                document.documentElement.classList.remove('dark');
            }

            updateIcon(newTheme);
        });
    }
  // FIX: paksa sidebar & hamburger tetap aman di dark mode
function fixHamburgerDarkMode() {
    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';

    if (isDark) {
        $('.mobile-nav-header').show();
        $('.burger-btn').show();
    }
}

// jalankan saat load
fixHamburgerDarkMode();

// jalankan setiap theme berubah
const observer = new MutationObserver(() => {
    fixHamburgerDarkMode();
});
observer.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['data-bs-theme']
});

// ── Mobile Sidebar Fix ──
// 1. On desktop: add 'active' class to show sidebar on load
// 2. On mobile: keep sidebar closed by default (no 'active' class)
document.addEventListener('DOMContentLoaded', function() {
    const sidebarWrapper = document.querySelector('.sidebar-wrapper');
    if (sidebarWrapper && window.innerWidth >= 992) {
        sidebarWrapper.classList.add('active');
    }

    // Mobile close button: always call hide() directly to prevent backdrop bug
    const mobileCloseBtn = document.getElementById('mobileSidebarClose');
    if (mobileCloseBtn) {
        mobileCloseBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (window.sidebar) {
                window.sidebar.hide();
            }
        });
    }
});

// auto session check
document.addEventListener('visibilitychange', function() {
    // check when user returns to this tab
    if (document.visibilityState === 'visible') {

        // silently check session status using ajax
        fetch(window.location.href, { 
            method: 'GET', 
            credentials: 'same-origin',
            headers: { 
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            } 
        })
        .then(response => {

            // 401 = unauthenticated / 419 = token expired
            if (response.status === 401 || response.status === 419) {
                console.log("Session expired. Kicking user to login...");
                window.location.href = '/login'; 
            }
        })
        .catch(error => console.log('Session check ignored.'));
    }
});
</script>

@stack('scripts')

</body>
</html>
