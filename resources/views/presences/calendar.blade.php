@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Presence Calendar</h3>
                <p class="text-subtitle text-muted">View presence records in calendar format.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('presences.index') }}">Presences</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Calendar</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
    <section class="section">
        <div class="card">
            <div class="card-body">
                <!-- Month Navigation -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('presences.calendar', ['year' => $year, 'month' => $month - 1]) }}" 
                       class="btn btn-outline-primary">
                        <i class="bi bi-chevron-left"></i> Previous Month
                    </a>
                    <h4 class="mb-0">
                        @php
                            try {
                                $displayDate = \Carbon\Carbon::create($year, $month, 1);
                                echo $displayDate->format('F Y');
                            } catch (\Exception $e) {
                                echo \Carbon\Carbon::now()->format('F Y');
                            }
                        @endphp
                    </h4>
                    <a href="{{ route('presences.calendar', ['year' => $year, 'month' => $month + 1]) }}" 
                       class="btn btn-outline-primary">
                        Next Month <i class="bi bi-chevron-right"></i>
                    </a>
                </div>

                <!-- Calendar Grid -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sun</th>
                                <th>Mon</th>
                                <th>Tue</th>
                                <th>Wed</th>
                                <th>Thu</th>
                                <th>Fri</th>
                                <th>Sat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                try {
                                    $firstDay = \Carbon\Carbon::create($year, $month, 1);
                                    $lastDay = $firstDay->copy()->endOfMonth();
                                    $startDate = $firstDay->copy()->startOfWeek();
                                    $endDate = $lastDay->copy()->endOfWeek();
                                    $currentDate = $startDate->copy();
                                    $presencesByDate = $presences->keyBy('date');
                                } catch (\Exception $e) {
                                    // Fallback to current month if date creation fails
                                    $firstDay = \Carbon\Carbon::now()->startOfMonth();
                                    $lastDay = $firstDay->copy()->endOfMonth();
                                    $startDate = $firstDay->copy()->startOfWeek();
                                    $endDate = $lastDay->copy()->endOfWeek();
                                    $currentDate = $startDate->copy();
                                    $presencesByDate = collect([]);
                                }
                            @endphp
                            @while($currentDate->lte($endDate))
                                <tr>
                                    @for($i = 0; $i < 7; $i++)
                                        @php
                                            $dateStr = $currentDate->format('Y-m-d');
                                            $presence = $presencesByDate->get($dateStr);
                                            $isCurrentMonth = $currentDate->month == $month;
                                            $isToday = $currentDate->isToday();
                                        @endphp
                                        <td class="calendar-day {{ !$isCurrentMonth ? 'text-muted' : '' }} {{ $isToday ? 'bg-light' : '' }}" 
                                            style="height: 100px; vertical-align: top; position: relative;">
                                            <div class="fw-bold {{ $isToday ? 'text-primary' : '' }}">
                                                {{ $currentDate->day }}
                                            </div>
                                            @if($presence && $isCurrentMonth)
                                                <div class="small mt-1">
                                                    @if($presence['check_in'])
                                                        <div class="badge bg-success">In: {{ $presence['check_in'] }}</div>
                                                    @endif
                                                    @if($presence['check_out'])
                                                        <div class="badge bg-info mt-1">Out: {{ $presence['check_out'] }}</div>
                                                    @endif
                                                    @if($presence['is_late'])
                                                        <div class="badge bg-warning mt-1">Late</div>
                                                    @endif
                                                    <div class="badge bg-secondary mt-1">{{ $presence['work_type'] }}</div>
                                                </div>
                                            @endif
                                        </td>
                                        @php $currentDate->addDay(); @endphp
                                    @endfor
                                </tr>
                            @endwhile
                        </tbody>
                    </table>
                </div>

                <!-- Legend -->
                <div class="mt-3">
                    <h6>Legend:</h6>
                    <span class="badge bg-success">Check-in</span>
                    <span class="badge bg-info">Check-out</span>
                    <span class="badge bg-warning">Late</span>
                    <span class="badge bg-secondary">Work Type</span>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection