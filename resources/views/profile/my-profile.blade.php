@extends ('layouts.dashboard')

@section ('content')
    @php
        $isAdmin = \App\Constants\Roles::isAdmin(session('role'));
    @endphp
    <style>
        /* ═══════════════════════════════════════════
       PROFILE HEADER (TETAP PAKAI WARNA FIX KARENA BACKGROUNDNYA GELAP)
    ═══════════════════════════════════════════ */
        .profile-card {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);
            color: white;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(30, 58, 138, 0.25);
            position: relative;
        }

        .profile-card-body {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.75rem 2rem;
            position: relative;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            min-width: 100px;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-avatar i {
            font-size: 3rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .profile-meta {
            flex: 1;
            min-width: 0;
        }

        .profile-emp-code {
            font-size: 0.8rem;
            font-weight: 600;
            color: #fde68a;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 0.2rem;
        }

        .profile-name {
            font-size: 1.4rem;
            font-weight: 700;
            color: #facc15;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 0.3rem;
            line-height: 1.2;
            word-break: break-word;
        }

        .profile-position {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.8);
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .profile-edit-btn {
            flex-shrink: 0;
        }

        .profile-edit-btn .btn {
            background: #facc15;
            border: none;
            color: #1e3a8a;
            font-weight: 700;
            font-size: 0.8rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .profile-edit-btn .btn:hover {
            background: #fde68a;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        /* ═══════════════════════════════════════════
       TAB NAVIGATION WRAPPER (ADAPTIVE DARK/LIGHT)
    ═══════════════════════════════════════════ */
        .profile-tabs-wrapper {
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .profile-tabs-wrapper::-webkit-scrollbar {
            display: none;
        }

        .profile-tabs-wrapper .nav {
            display: flex;
            flex-wrap: nowrap;
            border-bottom: 2px solid var(--bs-border-color); /* Pakai variable mazer */
            min-width: max-content;
        }

        .profile-tabs-wrapper .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            color: var(--bs-body-color); /* Adaptive text */
            opacity: 0.7;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            padding: 0.85rem 1.25rem;
            white-space: nowrap;
            transition: all 0.2s ease;
            background: transparent;
        }

        .profile-tabs-wrapper .nav-link:hover {
            color: var(--bs-primary);
            opacity: 1;
        }

        .profile-tabs-wrapper .nav-link.active {
            color: var(--bs-primary);
            border-bottom-color: var(--bs-primary);
            opacity: 1;
            background: transparent;
        }

        /* ═══════════════════════════════════════════
       INFO TABLE (ADAPTIVE DARK/LIGHT)
    ═══════════════════════════════════════════ */
        .info-table {
            width: 100%;
        }

        .info-table th {
            width: 220px;
            font-weight: 600;
            color: var(--bs-heading-color); /* Adaptive heading */
            opacity: 0.8;
            border: none;
            padding: 0.7rem 0;
            vertical-align: top;
        }

        .info-table td {
            border: none;
            padding: 0.7rem 0;
            color: var(--bs-body-color); /* Adaptive text */
            font-weight: 500;
        }

        .info-table tr {
            border-bottom: 1px solid var(--bs-border-color); /* Adaptive border */
        }

        .info-table tr:last-child {
            border-bottom: none;
        }

        /* ═══════════════════════════════════════════
       MOBILE RESPONSIVE  ≤ 768px
    ═══════════════════════════════════════════ */
        @media (max-width: 768px) {
            .profile-card-body {
                flex-direction: column;
                align-items: center;
                text-align: center;
                padding: 1.5rem 1rem 1.25rem;
                gap: 0.9rem;
            }

            .profile-avatar {
                width: 80px;
                height: 80px;
                min-width: 80px;
            }

            .profile-avatar i {
                font-size: 2.5rem;
            }
            .profile-meta {
                width: 100%;
            }
            .profile-name {
                font-size: 1.15rem;
            }
            .profile-edit-btn {
                width: 100%;
            }

            .profile-edit-btn .btn {
                width: 100%;
                padding: 0.6rem 1rem;
                font-size: 0.85rem;
                justify-content: center;
                display: flex;
                align-items: center;
                gap: 0.4rem;
            }

            .tab-content.tablet-pad {
                padding: 1rem !important;
            }

            .info-table tr {
                display: flex;
                flex-direction: column;
                padding: 0.6rem 0;
                border-bottom: 1px solid var(--bs-border-color);
            }

            .info-table tr:last-child {
                border-bottom: none;
            }

            .info-table th {
                width: 100%;
                font-size: 0.7rem;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                padding: 0 0 0.15rem;
            }

            .info-table td {
                width: 100%;
                font-size: 0.92rem;
                padding: 0;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .profile-card-body {
                padding: 1.5rem;
                gap: 1.25rem;
            }
            .profile-name {
                font-size: 1.25rem;
            }
            .info-table th {
                width: 180px;
            }
        }
    </style>
    <div class="page-heading">
        {{-- ══════════════════ Profile Header Card ══════════════════ --}}
        <div class="profile-card shadow-sm">
            <div class="profile-card-body">
                {{-- Avatar --}}
                <div class="profile-avatar">
                    @if ($employee->profile_photo)
                        <img src="{{ asset('storage/' . $employee->profile_photo) }}" alt="Profile Photo" />
                    @else
                        <i class="bi bi-person-fill"></i>
                    @endif
                </div>

                {{-- Employee Info --}}
                <div class="profile-meta">
                    <div class="profile-emp-code">{{ $employee->emp_code ?? 'N/A' }}</div>
                    <div class="profile-name">{{ $employee->fullname }}</div>
                    <div class="profile-position">
                        {{ $employee->department->name ?? 'N/A' }} &bull; {{ $employee->role->title ?? 'N/A' }}
                    </div>
                </div>

                {{-- Edit Button --}}
                <div class="profile-edit-btn">
                    <a href="{{ route('employees.edit', $employee->id) }}" class="btn">
                        <i class="bi bi-pencil-square"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>

        {{-- ══════════════════ Tabs Section ══════════════════ --}}
        <section class="section">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    {{-- Tab Navigation wrapped in scrollable div --}}
                    <div class="profile-tabs-wrapper">
                        <ul class="nav" id="profileTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link active"
                                    id="working-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#working"
                                    type="button"
                                    role="tab"
                                >
                                    Working Information
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link"
                                    id="personal-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#personal"
                                    type="button"
                                    role="tab"
                                >
                                    Personal Information
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link"
                                    id="education-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#education"
                                    type="button"
                                    role="tab"
                                >
                                    Education
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link"
                                    id="family-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#family"
                                    type="button"
                                    role="tab"
                                >
                                    Family Relation
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link"
                                    id="career-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#career"
                                    type="button"
                                    role="tab"
                                >
                                    Career History
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link"
                                    id="bank-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#bank"
                                    type="button"
                                    role="tab"
                                >
                                    Bank Account
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link"
                                    id="training-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#training"
                                    type="button"
                                    role="tab"
                                >
                                    Training History
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link"
                                    id="documents-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#documents"
                                    type="button"
                                    role="tab"
                                >
                                    Documents
                                </button>
                            </li>
                        </ul>
                    </div>

                    {{-- Tab Content --}}
                    <div class="tab-content tablet-pad p-4" id="profileTabsContent">
                        {{-- Working Information --}}
                        <div class="tab-pane fade show active" id="working" role="tabpanel">
                            <table class="table info-table mb-0">
                                <tbody>
                                    <tr>
                                        <th>Join Date</th>
                                        <td>
                                            {{
                                                $employee->hire_date
                                                    ? $employee->hire_date->format('d/m/Y')
                                                    : '-'
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Resign Date</th>
                                        <td>
                                            {{
                                                $employee->resign_date
                                                    ? \Carbon\Carbon::parse($employee->resign_date)->format('d/m/Y')
                                                    : '-'
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Permanent Date</th>
                                        <td>
                                            {{
                                                $employee->permanent_date
                                                    ? $employee->permanent_date->format('d/m/Y')
                                                    : '-'
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Contract Expiry</th>
                                        <td>
                                            {{
                                                $employee->contract_expiry
                                                    ? $employee->contract_expiry->format('d/m/Y')
                                                    : '-'
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Work Location</th>
                                        <td>{{ $employee->department->name ?? 'Head Office' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Homebase</th>
                                        <td>Head Office</td>
                                    </tr>
                                    @if ($isAdmin)
                                        <tr>
                                            <th>JG / PG</th>
                                            <td>
                                                @php
                                                    $latestPos = $employee->employeePositions->sortByDesc('start_date')->first();
                                                @endphp
                                                {{ $latestPos->pay_grade_id ?? '-' }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>Employment Status</th>
                                        <td>{{ ucfirst($employee->employee_status ?? 'Permanent') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Personal Information --}}
                        <div class="tab-pane fade" id="personal" role="tabpanel">
                            <table class="table info-table mb-0">
                                <tbody>
                                    <tr>
                                        <th>NIK</th>
                                        <td>{{ $employee->nik ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Full Name</th>
                                        <td>{{ $employee->fullname }}</td>
                                    </tr>
                                    <tr>
                                        <th>Place / Date of Birth</th>
                                        <td>
                                            {{ $employee->place_of_birth ?? '-' }} / {{ $employee->birth_date ? $employee->birth_date->format('d M Y') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Gender</th>
                                        <td>{{ ucfirst($employee->gender ?? '-') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Religion</th>
                                        <td>{{ $employee->religion ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Marital Status</th>
                                        <td>{{ $employee->marital_status ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $employee->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone Number</th>
                                        <td>{{ $employee->phone_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td>{{ $employee->address ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Education --}}
                        <div class="tab-pane fade" id="education" role="tabpanel">
                            <table class="table info-table mb-0">
                                <tbody>
                                    <tr>
                                        <th>Latest Education Level</th>
                                        <td>{{ $employee->educationLevel->level ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Family Relation --}}
                        <div class="tab-pane fade" id="family" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Relation</th>
                                            <th>NIK</th>
                                            <th>Date of Birth</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($employee->families as $family)
                                            <tr>
                                                <td>{{ $family->fullname }}</td>
                                                <td>{{ $family->relation }}</td>
                                                <td>{{ $family->nik }}</td>
                                                <td>
                                                    {{
                                                        $family->date_of_birth
                                                            ? \Carbon\Carbon::parse($family->date_of_birth)->format('d/m/Y')
                                                            : '-'
                                                    }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">
                                                    No family data available.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Career History --}}
                        <div class="tab-pane fade" id="career" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Old Position</th>
                                            <th>New Position</th>
                                            <th>Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($employee->mutations as $mutation)
                                            <tr>
                                                <td>{{ $mutation->mutation_date->format('d/m/Y') }}</td>
                                                <td>{{ ucfirst($mutation->type) }}</td>
                                                <td>
                                                    {{ $mutation->oldDepartment->name ?? '-' }} – {{ $mutation->oldRole->title ?? '-' }}
                                                </td>
                                                <td>
                                                    {{ $mutation->newDepartment->name ?? '-' }} – {{ $mutation->newRole->title ?? '-' }}
                                                </td>
                                                <td>{{ $mutation->reason }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    No career history available.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Bank Account --}}
                        <div class="tab-pane fade" id="bank" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Bank Name</th>
                                            <th>Account No</th>
                                            <th>Account Holder</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($employee->bankAccounts as $bank)
                                            <tr>
                                                <td>{{ $bank->bank_name }}</td>
                                                <td>{{ $bank->account_no }}</td>
                                                <td>{{ $bank->account_holder }}</td>
                                                <td>
                                                    @if ($bank->is_primary)
                                                        <span class="badge bg-primary">Primary</span>
                                                    @else
                                                        <span class="badge bg-secondary">Secondary</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">
                                                    No bank account information available.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Training History --}}
                        <div class="tab-pane fade" id="training" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Training Name</th>
                                            <th>Provider</th>
                                            <th>Category</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                <i class="bi bi-journal-check fs-2 d-block mb-2"></i>
                                                Training history data is not yet available for this employee.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Documents --}}
                        <div class="tab-pane fade" id="documents" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Document Type</th>
                                            <th>ID Number</th>
                                            <th>Description</th>
                                            <th>File</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($employee->documentIdentities as $doc)
                                            <tr>
                                                <td>{{ $doc->identityType->name ?? 'N/A' }}</td>
                                                <td>{{ $doc->identity_number }}</td>
                                                <td>{{ $doc->description ?? '-' }}</td>
                                                <td>
                                                    @if ($doc->file_name)
                                                        <a
                                                            href="{{ asset('storage/' . $doc->file_name) }}"
                                                            target="_blank"
                                                            class="btn btn-sm btn-outline-primary"
                                                        >
                                                            <i class="bi bi-download"></i> View
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">
                                                    No documents available.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{-- /tab-content --}}
                </div>
            </div>
        </section>
    </div>
@endsection