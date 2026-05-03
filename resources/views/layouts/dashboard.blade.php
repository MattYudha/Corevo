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
        /* Active highlight */
        .sidebar-item.active > a.sidebar-link {
            background: #0d6efd;
            color: #fff !important;
            border-radius: 10px;
        }
        .sidebar-item.active > a.sidebar-link i {
            color: #fff !important;
        }

        /* Menu group styles */
        .menu-group {
            background: rgba(13, 110, 253, 0.04);
            border-left: 3px solid #0d6efd;
            border-radius: 0 10px 10px 0;
            margin: 8px 10px 8px 5px;
            padding: 0;
            list-style: none;
        }
        .menu-group-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px 10px 12px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #0d6efd;
            opacity: 0.85;
            cursor: pointer;
            user-select: none;
            transition: opacity 0.2s;
        }
        .menu-group-header:hover {
            opacity: 1;
        }
        .menu-group-header i.group-icon {
            font-size: 0.85rem;
        }
        .menu-group-header .chevron {
            margin-left: auto;
            font-size: 0.7rem;
            transition: transform 0.3s ease;
        }
        .menu-group.expanded .menu-group-header .chevron {
            transform: rotate(90deg);
        }

        /* Collapse/expand items */
        .menu-group .menu-group-items {
            list-style: none;
            padding: 0 6px 0 6px;
            margin: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease, padding-bottom 0.35s ease;
        }
        .menu-group.expanded .menu-group-items {
            max-height: 500px;
            padding-bottom: 6px;
        }
        .menu-group .menu-group-items .sidebar-item {
            margin: 0;
        }
        .menu-group .menu-group-items .sidebar-link {
            padding: 8px 12px;
            font-size: 0.88rem;
        }

        /* Dark mode group adjustments */
        [data-bs-theme='dark'] .menu-group {
            background: rgba(255, 255, 255, 0.04);
            border-left-color: #5e9bff;
        }
        [data-bs-theme='dark'] .menu-group-header {
            color: #7db4ff;
        }

        /* Desktop sidebar layout */
        @media screen and (min-width: 992px) {
            #sidebar { width: 300px; transition: all 0.3s ease; overflow-x: hidden; }
            #main { margin-left: 300px; transition: all 0.3s ease; }
            body.sidebar-collapsed #sidebar { width: 0; transform: translateX(-300px); }
            body.sidebar-collapsed #main { margin-left: 0; }
        }

        /* Desktop sidebar toggle button */
        #desktopSidebarToggle {
            display: none;
        }
        @media screen and (min-width: 992px) {
            #desktopSidebarToggle {
                display: flex;
                position: fixed;
                top: 12px;
                left: 12px;
                z-index: 1100;
                width: 36px;
                height: 36px;
                align-items: center;
                justify-content: center;
                background: rgba(255,255,255,0.92);
                border: 1px solid rgba(0,0,0,0.1);
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                cursor: pointer;
                color: #1a1f3c;
                transition: background 0.2s, left 0.3s;
                text-decoration: none;
            }
            #desktopSidebarToggle:hover { background: #0d6efd; color: #fff; }
            body.sidebar-collapsed #desktopSidebarToggle { left: 12px; }
            [data-bs-theme='dark'] #desktopSidebarToggle {
                background: rgba(30,30,45,0.92);
                color: #f8fafc;
                border-color: rgba(255,255,255,0.1);
            }
        }

        /* Mobile sidebar overlap fix + scrollable */
        @media screen and (max-width: 991px) {
            #sidebar, .sidebar-wrapper {
                z-index: 1050 !important;
            }

            /* Make sidebar wrapper scrollable on iOS/mobile */
            .sidebar-wrapper {
                display: flex;
                flex-direction: column;
                height: 100vh !important;
                max-height: 100vh !important;
                overflow: hidden !important;
            }

            /* Sticky header within sidebar */
            .sidebar-wrapper .sidebar-header {
                flex-shrink: 0;
            }

            /* Scrollable menu area */
            .sidebar-wrapper .sidebar-menu {
                flex: 1 1 auto;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                -webkit-overflow-scrolling: touch; /* Critical for iOS momentum scroll */
                overscroll-behavior: contain;
                padding-bottom: env(safe-area-inset-bottom, 24px); /* iPhone notch/home bar */
                padding-bottom: max(24px, env(safe-area-inset-bottom));
            }

            /* Custom thin scrollbar for sidebar on mobile */
            .sidebar-wrapper .sidebar-menu::-webkit-scrollbar {
                width: 3px;
            }
            .sidebar-wrapper .sidebar-menu::-webkit-scrollbar-track {
                background: transparent;
            }
            .sidebar-wrapper .sidebar-menu::-webkit-scrollbar-thumb {
                background: rgba(0, 0, 0, 0.15);
                border-radius: 10px;
            }
            [data-bs-theme='dark'] .sidebar-wrapper .sidebar-menu::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.15);
            }
        }

        .mobile-nav-header {
            position: sticky;
            top: 0;
            z-index: 9;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 0.85rem 1.25rem;
            margin-bottom: 1.25rem;
            margin-left: -1rem; /* Negate the generic mobile padding of #main if any */
            margin-right: -1rem;
        }

        .mobile-top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            margin: 0 auto;
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
            width: 40px;
            height: 40px;
            color: #1a1f3c; /* Navy dark */
            text-decoration: none;
            cursor: pointer;
            background: transparent;
            border: none;
            border-radius: 8px;
            transition: background 0.15s, transform 0.15s;
        }

        .mobile-burger-btn:hover,
        .mobile-burger-btn:active {
            background: rgba(0, 0, 0, 0.04);
            transform: scale(0.95);
        }

        .mobile-burger-btn i {
            font-size: 1.8rem;
            line-height: 1;
        }

        [data-bs-theme='dark'] .mobile-nav-header {
            background: rgba(30, 30, 45, 0.85);
            border-bottom-color: rgba(255, 255, 255, 0.05);
        }
        [data-bs-theme='dark'] .mobile-burger-btn {
            color: #f8fafc;
        }
        [data-bs-theme='dark'] .app-brand span {
            color: #f8fafc !important;
        }
      
    </style>
    @stack('styles')
</head>

<body>
<script src="{{ asset('mazer/assets/static/js/initTheme.js') }}"></script>

<div id="app">

    {{-- Desktop Sidebar Toggle Button (visible on lg+ screens only via CSS) --}}
    <button id="desktopSidebarToggle" title="Toggle Sidebar" aria-label="Toggle Sidebar">
        <i class="bi bi-layout-sidebar" style="font-size:1.1rem;"></i>
    </button>

    <!-- ================= SIDEBAR ================= -->
    <div id="sidebar">
        <div class="sidebar-wrapper active">

            <div class="sidebar-header text-center d-flex flex-column align-items-center gap-2 pb-2">
                <div class="d-flex justify-content-center align-items-center w-100 px-3">
                    <a href="{{ url('/dashboard') }}" class="d-inline-flex justify-content-center">
                        <img src="{{ asset('corevo-logo.png') }}" style="height:65px" id="sidebar-logo">
                    </a>
                </div>
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
                        $showPayrollGroup = $isMasterAdmin || $isAdmin || $isManager || $hasKpiAccess;
                        $showInventoryLogs = $isMasterAdmin || $isAdmin || $isManager || $user->hasAccess('inventory_logs');
                        $showInventoryAdmin = $isMasterAdmin || $isAdmin || $user->hasAccess('inventory');

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
                            @if($isMasterAdmin || $isAdmin || $isManager)
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

                    <!-- BUKU KAS & KEUANGAN MODULE (RBAC) -->
                    @if($isAdmin || $isMasterAdmin || $isManager || $isMarketing || $isSupervisor || $isFinanceRole)
                    <li class="menu-group expanded">
                        <div class="menu-group-header">
                            <i class="bi bi-wallet2 group-icon"></i>
                            <span>Buku Kas & Keuangan</span>
                            <i class="bi bi-chevron-right chevron"></i>
                        </div>
                        <ul class="menu-group-items">
                            @if($isAdmin || $isMasterAdmin || $isManager || $isMarketing || $isFinanceRole)
                            <li class="sidebar-item {{ $activeFinanceTransactions ? 'active' : '' }}">
                                <a href="{{ url('/finance/transactions') }}" class="sidebar-link">
                                    <i class="bi bi-pencil-square"></i>
                                    <span>Buku Kas (Ledger)</span>
                                </a>
                            </li>
                            @endif
                            
                            @if($isAdmin || $isMasterAdmin || $isManager || $isMarketing || $isFinanceRole)
                            <li class="sidebar-item {{ $activeFinanceEntities ? 'active' : '' }}">
                                <a href="{{ url('/finance/entities') }}" class="sidebar-link">
                                    <i class="bi bi-building"></i>
                                    <span>Master Entitas</span>
                                </a>
                            </li>
                            <li class="sidebar-item {{ $activeFinanceAccounts ? 'active' : '' }}">
                                <a href="{{ url('/finance/accounts') }}" class="sidebar-link">
                                    <i class="bi bi-journal-bookmark"></i>
                                    <span>Kategori Akun (CoA)</span>
                                </a>
                            </li>
                            @endif

                            <li class="sidebar-item {{ $activeFinanceReports ? 'active' : '' }}">
                                <a href="{{ url('/finance/reports') }}" class="sidebar-link">
                                    <i class="bi bi-file-earmark-spreadsheet"></i>
                                    <span>Laporan Keuangan</span>
                                </a>
                            </li>

                            @if($isAdmin || $isMasterAdmin || $isManager || $isMarketing || $isFinanceRole)
                            <li class="sidebar-item {{ $activeFinanceCharts ? 'active' : '' }}">
                                <a href="{{ url('/finance/charts') }}" class="sidebar-link">
                                    <i class="bi bi-graph-up-arrow"></i>
                                    <span>Grafik Analitik</span>
                                </a>
                            </li>
                            @endif

                            {{-- Admin: Claim Management --}}
                            @if($isAdmin || $isMasterAdmin)
                            <li class="sidebar-item {{ $activeFinanceClaims ? 'active' : '' }}">
                                <a href="{{ url('/finance/claims') }}" class="sidebar-link">
                                    <i class="bi bi-clipboard-check"></i>
                                    <span>Kelola Klaim Biaya</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    <!-- INVENTORY -->
                    <li class="menu-group expanded">
                        <div class="menu-group-header">
                            <i class="bi bi-boxes group-icon"></i>
                            <span>Inventory</span>
                            <i class="bi bi-chevron-right chevron"></i>
                        </div>
                        <ul class="menu-group-items">
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
                            @if($isMasterAdmin || false || $user->hasAccess('inventory'))
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

                            

                            
                            <hr class="mx-3 my-1 border-light opacity-25">

                            <li class="sidebar-item {{ $activeWorkLogs ? 'active' : '' }}">
                                <a href="{{ route('work-logs.index') }}" class="sidebar-link">
                                    <i class="bi bi-journal-check"></i>
                                    <span>My Activity</span>
                                </a>
                            </li>
                            
                            <li class="sidebar-item {{ $overtimes ? 'active' : '' }}">
                                <a href="{{ route('overtimes.index') }}" class="sidebar-link">
                                    <i class="bi bi-clock"></i>
                                    <span>My Overtimes</span>
                                </a>
                            </li>

                            <li class="sidebar-item {{ $activeMyFinance ? 'active' : '' }}">
                                <a href="{{ url('/finance/my-finance') }}" class="sidebar-link">
                                    <i class="bi bi-wallet-fill"></i>
                                    <span>Keuangan Saya</span>
                                </a>
                            </li>
                            <li class="sidebar-item {{ $activeFinanceClaims ? 'active' : '' }}">
                                <a href="{{ url('/finance/claims') }}" class="sidebar-link">
                                    <i class="bi bi-receipt"></i>
                                    <span>Klaim Biaya Saya</span>
                                </a>
                            </li>

                            <li class="sidebar-item {{ request()->is('knowledge-base*') ? 'active' : '' }}">
                                <a href="{{ url('/knowledge-base') }}" class="sidebar-link">
                                    <i class="bi bi-book"></i>
                                    <span>Knowledge Base</span>
                                </a>
                            </li>

                            <li class="sidebar-item">
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
                    <img src="{{ asset('corevo-logo.png') }}" style="height: 30px;" alt="Logo" onerror="this.style.display='none'">
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

    // Desktop toggle button
    document.getElementById('desktopSidebarToggle')?.addEventListener('click', function() {
        bodyEl.classList.toggle('sidebar-collapsed');
        localStorage.setItem(SIDEBAR_KEY, bodyEl.classList.contains('sidebar-collapsed') ? '1' : '0');
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
</script>

@stack('scripts')

</body>
</html>
