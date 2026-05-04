<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\Employee;
use App\Models\OfficeLocation;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PresencesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Presence::with(['employee.officeLocation', 'officeLocation']);
            
            if (!in_array(session('role'), ['HR Administrator', \App\Constants\Roles::MASTER_ADMIN])) {
                $query->where('employee_id', session('employee_id'));
            }
            
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btns = '<div class="btn-group btn-group-sm" role="group">';

                    $btns .= '<a href="'.route('presences.show', $row->id).'" class="btn btn-outline-info" title="View Details"><i class="bi bi-eye"></i></a>';
                    
                    if (in_array(session('role'), ['HR Administrator', \App\Constants\Roles::MASTER_ADMIN])) {
                        $btns .= '<a href="'.route('presences.edit', $row->id).'" class="btn btn-outline-warning"><i class="bi bi-pencil"></i></a>';
                        $csrf = csrf_token();
                        $btns .= '
                            <form action="'.route('presences.destroy', $row->id).'" method="POST" class="d-inline">
                                <input type="hidden" name="_token" value="'.$csrf.'">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm(\'Delete this record?\')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        ';
                    }
                    
                    $btns .= '</div>';
                    return $btns;
                })
                ->addColumn('status_badge', function($row){
                    $class = match($row->status) {
                        'present' => 'bg-success',
                        'absent' => 'bg-danger',
                        'leave' => 'bg-info',
                        default => 'bg-secondary'
                    };
                    $badge = '<span class="badge '.$class.'">'.ucfirst($row->status).'</span>';
                    
                    if ($row->status === 'present' && $row->is_late) {
                        $badge .= ' <span class="badge bg-warning text-dark">Late</span>';
                    }
                    
                    return $badge;
                })
                ->addColumn('work_type_badge', function($row){
                    $class = match($row->work_type) {
                        'WFO' => 'bg-primary',
                        'WFH' => 'bg-secondary',
                        'WFA' => 'bg-dark',
                        default => 'bg-light text-dark'
                    };
                    return '<span class="badge '.$class.'">'.($row->work_type ?? 'WFO').'</span>';
                })
                ->addColumn('office_location_name', function($row){
                    if ($row->officeLocation) {
                        return e($row->officeLocation->name);
                    }
                    if (($row->work_type ?? 'WFO') === 'WFO' && $row->employee?->officeLocation) {
                        return e($row->employee->officeLocation->name);
                    }
                    return '-';
                })
                ->editColumn('date', function($row){
                    return $row->date ? Carbon::parse($row->date)->format('d M Y') : '-';
                })
                ->editColumn('check_in', function($row){
                    return $row->check_in ? Carbon::parse($row->check_in)->format('H:i:s') : '-';
                })
                ->editColumn('check_out', function($row){
                    if ($row->check_out) {
                        return Carbon::parse($row->check_out)->format('H:i:s');
                    }
                    if (session('employee_id') == $row->employee_id && 
                        Carbon::parse($row->date)->isToday() && $row->check_in && !$row->check_out) {
                        return '<a href="'.route('presences.checkout').'" class="btn btn-sm btn-success">Check Out</a>';
                    }
                    return '-';
                })
                ->rawColumns(['action', 'status_badge', 'work_type_badge', 'check_out'])
                ->make(true);
        }
        
        return view('presences.index');
    }

    // Show the form to create a new attendance record
    public function create()
    {
        $employees = Employee::all();
        $currentEmployee = Auth::user()?->employee;

        if ($currentEmployee) {
            $currentEmployee->loadMissing('officeLocation');
        }

        $wfoOfficeLocations = $this->getSelectableWfoOfficeLocations();
        $selectedWfoOfficeLocation = $this->resolveDefaultWfoOfficeLocation($currentEmployee, $wfoOfficeLocations);

        return view('presences.create', compact('employees', 'wfoOfficeLocations', 'selectedWfoOfficeLocation'));
    }

    // Store a newly created attendance record
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if (session('role')  == 'HR Administrator') {

            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|exists:employees,id',
                'check_in' => 'required|date',
                'check_out' => 'nullable|date|after_or_equal:check_in',
                'status' => 'required|in:present,absent,leave'
            ]);

            if ($validator->fails()) {
                return redirect()->route('presences.index')->withErrors($validator)->withInput();
            }

            Presence::create([
                'employee_id' => $request->employee_id,
                'check_in' => Carbon::parse($request->check_in)->format('Y-m-d H:i:s'),
                'check_out' => $request->filled('check_out') ? Carbon::parse($request->check_out)->format('Y-m-d H:i:s') : null,
                'date' => Carbon::parse($request->check_in)->format('Y-m-d'),
                'status' => $request->status,
                'work_type' => 'WFO'
            ]);

        } else {

            // Regular employee mode, handle WFO/WFH/WFA
            $workType = $request->work_type ?? 'WFO';
            $fingerprint = $request->fingerprint;
            // Handle is_mobile as string "0" or "1" from form
            $isMobile = $request->is_mobile == '1' || $request->is_mobile === 1 || $request->is_mobile === true;
            $ssid = $request->ssid ?? '';
            
            \Log::info('Presence store request', [
                'work_type' => $workType,
                'has_fingerprint' => !empty($fingerprint),
                'is_mobile' => $isMobile,
                'has_latitude' => $request->has('latitude'),
                'has_longitude' => $request->has('longitude'),
                'has_ssid' => $request->has('ssid'),
            ]);

            $employeeId = session('employee_id');
            $employee = $employeeId ? Employee::with('officeLocation')->find($employeeId) : null;
            $officeLocationConfig = $this->resolveOfficeLocationForEmployee($employee);
            $selectedWfoOfficeLocationId = null;
            
            // 1. Device Fingerprinting Logic (required for all work types)
            if (empty($fingerprint)) {
                return redirect()->back()->with('error', 'Failed to verify your device identity. Please refresh the page and try again.');
            }
            
            try {
                if ($isMobile) {
                    if (!$user->browser_fingerprint_mobile) {
                        $user->update(['browser_fingerprint_mobile' => $fingerprint]);
                    } elseif ($user->browser_fingerprint_mobile !== $fingerprint) {
                        $this->logSuspicious($user->id, 'wrong_fingerprint', "Mobile fingerprint mismatch. Got: $fingerprint");
                        return redirect()->back()->with('error', 'Unregistered mobile device. Please use your original device.');
                    }
                } else {
                    if (!$user->browser_fingerprint_desktop) {
                        $user->update(['browser_fingerprint_desktop' => $fingerprint]);
                    } elseif ($user->browser_fingerprint_desktop !== $fingerprint) {
                        $this->logSuspicious($user->id, 'wrong_fingerprint', "Desktop fingerprint mismatch. Got: $fingerprint");
                        return redirect()->back()->with('error', 'Unregistered browser. Please use your primary browser.');
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Error updating fingerprint: ' . $e->getMessage());
                return redirect()->back()->with('error', 'An error occurred while verifying the device. Please try again.');
            }

            // For WFO, validate selected office, GPS, and WiFi
            if ($workType === 'WFO') {
                $validator = Validator::make($request->all(), [
                    'office_location_id' => 'required|integer|exists:office_locations,id',
                    'latitude' => 'required|numeric',
                    'longitude' => 'required|numeric',
                    'accuracy' => 'required|numeric'
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $selectedWfoOfficeLocationId = (int) $request->office_location_id;
                $officeLocationConfig = $this->resolveOfficeLocationForSelection($selectedWfoOfficeLocationId);

                if (!$officeLocationConfig) {
                    return redirect()->back()->withInput()->with('error', 'Invalid or inactive WFO office location.');
                }

                // check gps accuracy to help detect fake gps
                // spoofed gps may return 1m or unrealistically perfect values
                // real gps usually fluctuates between 10-65m
                if ($request->accuracy <= 5) {
                    $this->logSuspicious($user->id, 'fake_gps', "Unnatural accuracy detected (too perfect): {$request->accuracy}m");
                    return redirect()->back()->with('error', 'Your location is detected using a third-party application (Fake GPS). Please disable it and try again.');
                }

                // verify user location on the server using radius-based geofencing
                $officeLat = $officeLocationConfig['latitude'];
                $officeLon = $officeLocationConfig['longitude'];
                $radius = $officeLocationConfig['radius'];
                $distance = $this->calculateDistance($request->latitude, $request->longitude, $officeLat, $officeLon);
                
                if ($distance > $radius) {
                    $this->logSuspicious($user->id, 'out_of_radius', "Attendance attempt from $distance meters away from {$officeLocationConfig['name']}.");
                    return redirect()->back()->with('error', "You are outside the radius of {$officeLocationConfig['name']} (Your distance: " . round($distance) . " meters).");
                }

                // validate network security using ip address instead of manual ssid checks
                // significantly harder to bypass than gps spoofing
                $allowedIPs = $officeLocationConfig['allowed_ips'] ?? [];
                $clientIp = request()->ip();

                // run validation only when the office has an ip configured
                if (!empty($allowedIPs)) {
                    if (!in_array($clientIp, $allowedIPs)) {
                        $this->logSuspicious($user->id, 'invalid_ip', "Attendance attempt from an unknown network: $clientIp");
                        return redirect()->back()->with('error', "The system rejected your WFO attendance because your device's IP ($clientIp) is not connected to the official office WiFi network.");
                    }
                }
            }

            // Validate employee_id exists in session
            if (!$employeeId) {
                return redirect()->back()->with('error', 'Invalid session. Please log in again.');
            }

            // Check if already checked in today
            $today = Carbon::today();
            $existingPresence = Presence::where('employee_id', $employeeId)
                ->whereDate('date', $today)
                ->whereNotNull('check_in')
                ->first();

            if ($existingPresence) {
                if ($existingPresence->check_out) {
                    return redirect()->back()->with('error', 'You have already checked in and checked out today.');
                } else {
                    return redirect()->back()->with('error', 'You have already checked in today. Please check out first.');
                }
            }

            // Check for late check-in
            $checkInTime = Carbon::now();
            $workStartTimeStr = Setting::getValue('work_start_time', '08:00');
            $workStartTime = Carbon::parse(date('Y-m-d') . ' ' . $workStartTimeStr);
            $lateThreshold = (int) Setting::getValue('late_threshold_minutes', 15);
            $isLate = $this->isLateCheckIn($presence ?? new Presence(['status' => 'present', 'work_type' => $workType, 'check_in' => $checkInTime]));

            try {
                // Store GPS data for all work types (WFO, WFH, WFA)
                $latitude = $request->has('latitude') && !empty($request->latitude) ? $request->latitude : null;
                $longitude = $request->has('longitude') && !empty($request->longitude) ? $request->longitude : null;
                
                $photoPath = null;
                if ($request->filled('photo_data')) {
                    $image_parts = explode(";base64,", $request->photo_data);
                    if (count($image_parts) == 2) {
                        $image_type_aux = explode("image/", $image_parts[0]);
                        $image_type = $image_type_aux[1];
                        $image_base64 = base64_decode($image_parts[1]);
                        
                        $fileName = 'presence_' . $employeeId . '_' . time() . '.' . $image_type;
                        $folderPath = 'presence_photos/' . date('Y-m'); 
                        
                        \Illuminate\Support\Facades\Storage::disk('public')->put($folderPath . '/' . $fileName, $image_base64);
                        $photoPath = $folderPath . '/' . $fileName;
                    }
                }

                $dummyPresence = new Presence([
                    'status' => 'present', 
                    'work_type' => $workType, 
                    'check_in' => $checkInTime->format('Y-m-d H:i:s'),
                    'date' => $checkInTime->format('Y-m-d')
                ]);
                $isLate = $this->isLateCheckIn($dummyPresence);

                $presenceData = [
                    'employee_id' => $employeeId,
                    'office_location_id' => $workType === 'WFO' ? ($selectedWfoOfficeLocationId ?: ($officeLocationConfig['id'] ?? null)) : null,
                    'check_in' => $checkInTime->format('Y-m-d H:i:s'),
                    'date' => $checkInTime->format('Y-m-d'),
                    'status' => 'present',
                    'work_type' => $workType,
                    'is_late' => $isLate,
                    'photo_path' => $photoPath, 
                ];
                
                // Add GPS data if it exists
                if ($latitude !== null) $presenceData['latitude'] = $latitude;
                if ($longitude !== null) $presenceData['longitude'] = $longitude;
                
                $presence = Presence::create($presenceData);
                
                \Log::info('Presence created successfully', [
                    'presence_id' => $presence->id,
                    'employee_id' => $employeeId,
                    'work_type' => $workType
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                \Log::error('Database error creating presence: ' . $e->getMessage(), [
                    'employee_id' => $employeeId,
                    'work_type' => $workType,
                    'sql_error' => $e->getSql(),
                    'bindings' => $e->getBindings()
                ]);
                return redirect()->back()->with('error', 'Failed to save attendance data to the database. Please try again or contact the administrator.');
            } catch (\Exception $e) {
                \Log::error('Error creating presence: ' . $e->getMessage(), [
                    'employee_id' => $employeeId,
                    'work_type' => $workType,
                    'error' => $e->getTraceAsString()
                ]);
                return redirect()->back()->with('error', 'Failed to save attendance data: ' . $e->getMessage());
            }

            // Show warning if late
            if ($isLate) {
                $lateMinutes = $checkInTime->diffInMinutes($workStartTime->copy()->addMinutes($lateThreshold));
                $targetRoute = in_array(session('role'), ['HR Administrator', \App\Constants\Roles::MASTER_ADMIN]) ? 'presences.index' : 'dashboard';
                return redirect()->route($targetRoute)->with('warning', "Attendance recorded successfully. You are {$lateMinutes} minutes late.");
            }
            
        }

        $targetRoute = in_array(session('role'), ['HR Administrator', \App\Constants\Roles::MASTER_ADMIN]) ? 'presences.index' : 'dashboard';
        return redirect()->route($targetRoute)->with('success', 'Attendance recorded successfully.');
    }

    private function getSelectableWfoOfficeLocations(): array
    {
        return OfficeLocation::active()
            ->orderBy('name')
            ->get()
            ->map(fn (OfficeLocation $officeLocation) => $this->mapOfficeLocationToConfig($officeLocation))
            ->values()
            ->all();
    }

    private function resolveDefaultWfoOfficeLocation(?Employee $employee, array $officeLocations): array
    {
        $assignedOfficeLocationId = $employee?->office_location_id;

        if ($assignedOfficeLocationId) {
            foreach ($officeLocations as $officeLocation) {
                if ((int) $officeLocation['id'] === (int) $assignedOfficeLocationId) {
                    return $officeLocation;
                }
            }
        }

        return $officeLocations[0] ?? $this->defaultOfficeLocationConfig();
    }

    private function resolveOfficeLocationForSelection(?int $officeLocationId): ?array
    {
        if (!$officeLocationId) {
            return null;
        }

        $officeLocation = OfficeLocation::active()->find($officeLocationId);

        return $officeLocation ? $this->mapOfficeLocationToConfig($officeLocation) : null;
    }

    private function resolveOfficeLocationForPresence(?Presence $presence, ?Employee $employee = null): array
    {
        $officeLocation = $presence?->officeLocation;

        if (!$officeLocation) {
            $officeLocation = $employee?->officeLocation;
        }

        return $officeLocation
            ? $this->mapOfficeLocationToConfig($officeLocation)
            : $this->defaultOfficeLocationConfig();
    }

    private function resolveOfficeLocationForEmployee(?Employee $employee): array
    {
        return $employee?->officeLocation
            ? $this->mapOfficeLocationToConfig($employee->officeLocation)
            : $this->defaultOfficeLocationConfig();
    }

    private function defaultOfficeLocationConfig(): array
    {
        return [
            'id' => null,
            'name' => 'Head Office',
            'latitude' => (float) config('presence.office_latitude', -6.200000),
            'longitude' => (float) config('presence.office_longitude', 106.816666),
            'radius' => (int) config('presence.location_radius', 1000),
            'allowed_ssids' => [], 
            'allowed_ips' => [], 
            'address' => null,
        ];
    }

    private function mapOfficeLocationToConfig(OfficeLocation $officeLocation): array
    {
        $defaultConfig = $this->defaultOfficeLocationConfig();

        return [
            'id' => $officeLocation->id,
            'name' => $officeLocation->name,
            'latitude' => $officeLocation->latitude !== null ? (float) $officeLocation->latitude : $defaultConfig['latitude'],
            'longitude' => $officeLocation->longitude !== null ? (float) $officeLocation->longitude : $defaultConfig['longitude'],
            'radius' => !empty($officeLocation->radius) ? (int) $officeLocation->radius : $defaultConfig['radius'],
            'allowed_ssids' => !empty($officeLocation->allowed_ssids)
                ? array_values(array_filter($officeLocation->allowed_ssids))
                : $defaultConfig['allowed_ssids'],
            'allowed_ips' => !empty($officeLocation->allowed_ips)
                ? array_values(array_filter($officeLocation->allowed_ips))
                : $defaultConfig['allowed_ips'],
            'address' => $officeLocation->address,
        ];
    }

    private function buildInvalidSsidMessage(string $officeName, array $allowedSSIDs): string
    {
        if (empty($allowedSSIDs)) {
            return 'You must be connected to the registered office WiFi for WFO attendance.';
        }

        $ssidList = implode(', ', array_map(fn ($ssid) => '"' . $ssid . '"', $allowedSSIDs));

        return "You must be connected to the {$officeName} WiFi ({$ssidList}) for WFO attendance.";
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // in meters
        
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
            
        return $angle * $earthRadius;
    }

    private function logSuspicious($userId, $type, $details)
    {
        \App\Models\SuspiciousActivity::create([
            'user_id' => $userId,
            'activity_type' => $type,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    // Show the form for editing an attendance record
    public function edit(Presence $presence)
    {
        $employees = Employee::all();
        return view('presences.edit', compact('presence', 'employees'));
    }

    // Update the specified attendance record
    public function update(Request $request, Presence $presence)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'check_in' => 'required|date',
            'check_out' => 'nullable|date|after_or_equal:check_in',
            'status' => 'required|in:present,absent,leave',
        ]);

        $presence->update($request->all());

        return redirect()->route('presences.index')->with('success', 'Attendance data updated successfully.');
    }

    // Delete an attendance record
    public function destroy(Presence $presence)
    {
        if ($presence->photo_path && Storage::disk('public')->exists($presence->photo_path)) {
            Storage::disk('public')->delete($presence->photo_path);
        }

        $presence->delete();
        
        return redirect()->route('presences.index')->with('success', 'Attendance data deleted successfully');
    }

    // Check-out functionality - Show form
    public function checkout()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $employeeId = session('employee_id');
        $employee = $employeeId ? Employee::with('officeLocation')->find($employeeId) : null;
        
        // Find today's presence record with check-in but no check-out
        $today = Carbon::today();
        $presence = Presence::with('officeLocation')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->first();

        if (!$presence) {
            return redirect()->route('presences.index')->with('error', 'No check-in record found for today. Please check in first.');
        }

        $workType = $presence->work_type ?? 'WFO';
        // Only resolve office location config for WFO; WFH/WFA don't need it
        $officeLocationConfig = $workType === 'WFO'
            ? $this->resolveOfficeLocationForPresence($presence, $employee)
            : $this->defaultOfficeLocationConfig();
        $checkInTime = Carbon::parse($presence->check_in)->format('H:i:s');

        return view('presences.checkout', compact('presence', 'checkInTime', 'officeLocationConfig'));
    }

    // Check-out functionality - Process checkout
    public function processCheckout(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $employeeId = session('employee_id');
        $employee = $employeeId ? Employee::with('officeLocation')->find($employeeId) : null;
        
        // Find today's presence record with check-in but no check-out
        $today = Carbon::today();
        $presence = Presence::with('officeLocation')
            ->where('employee_id', $employeeId)
            ->whereDate('date', $today)
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->first();

        if (!$presence) {
            return redirect()->route('presences.index')->with('error', 'No check-in record found for today. Please check in first.');
        }

        $workType = $presence->work_type ?? 'WFO';
        // Only resolve office location config for WFO; WFH/WFA don't need geofencing
        $officeLocationConfig = $workType === 'WFO'
            ? $this->resolveOfficeLocationForPresence($presence, $employee)
            : $this->defaultOfficeLocationConfig();

        // Validate check-out cannot be before check-in
        $checkInTime = Carbon::parse($presence->check_in);
        $checkOutTime = Carbon::now();

        if ($checkOutTime->lt($checkInTime)) {
            return redirect()->route('presences.checkout')->with('error', 'Check-out time cannot be before check-in time.');
        }

        // For WFO, calculate GPS distance but DO NOT block the checkout if out of bounds
        if ($presence->work_type === 'WFO' && $request->has('latitude')) {
            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'accuracy' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Calculate distance
            $officeLat = $officeLocationConfig['latitude'];
            $officeLon = $officeLocationConfig['longitude'];
            $radius = $officeLocationConfig['radius'];
            $distance = $this->calculateDistance($request->latitude, $request->longitude, $officeLat, $officeLon);
            
            if ($distance > $radius) {
                // Log the suspicious activity but allow the checkout to proceed
                $this->logSuspicious($user->id, 'out_of_radius_checkout', "User checked out $distance meters from {$officeLocationConfig['name']} (Limit: {$radius}m).");
            }
        }

        // Proceed to update check-out time and explicitly save to the new check_out coordinate columns
        $presence->update([
            'check_out' => $checkOutTime->format('Y-m-d H:i:s'),
            'check_out_latitude' => $request->latitude ?? null,
            'check_out_longitude' => $request->longitude ?? null,
        ]);

        $targetRoute = in_array(session('role'), ['HR Administrator', \App\Constants\Roles::MASTER_ADMIN]) ? 'presences.index' : 'dashboard';
        return redirect()->route($targetRoute)->with('success', 'Check-out recorded successfully.');
    }

    public function show(Presence $presence)
    {
        // Ensure the user is authorized to view this record
        if (!in_array(session('role'), ['HR Administrator', \App\Constants\Roles::MASTER_ADMIN]) && session('employee_id') != $presence->employee_id) {
            abort(403, 'Unauthorized access to this presence record.');
        }

        $presence->load(['employee.department', 'employee.role', 'officeLocation']);
        $officeConfig = $this->resolveOfficeLocationForPresence($presence, $presence->employee);
        
        // 1. Check late status using the controller's built-in function
        $isLate = $presence->is_late;

        // 2. Check-In Geofence Calculations
        $checkInDistance = 0;
        $isCheckInOutOfRadius = false;
        if ($presence->work_type === 'WFO' && $presence->latitude && $presence->longitude) {
            $checkInDistance = $this->calculateDistance($presence->latitude, $presence->longitude, $officeConfig['latitude'], $officeConfig['longitude']);
            if ($checkInDistance > $officeConfig['radius']) {
                $isCheckInOutOfRadius = true;
            }
        }

        // 3. Check-Out Geofence Calculations
        $checkOutDistance = 0;
        $isCheckOutOutOfRadius = false;
        if ($presence->work_type === 'WFO' && $presence->check_out_latitude && $presence->check_out_longitude) {
            $checkOutDistance = $this->calculateDistance($presence->check_out_latitude, $presence->check_out_longitude, $officeConfig['latitude'], $officeConfig['longitude']);
            if ($checkOutDistance > $officeConfig['radius']) {
                $isCheckOutOutOfRadius = true;
            }
        }

        return view('presences.show', compact(
            'presence', 'officeConfig', 
            'checkInDistance', 'isCheckInOutOfRadius', 
            'checkOutDistance', 'isCheckOutOutOfRadius',
            'isLate' // <-- Ensure this variable is sent to the view
        ));
    }

    // Calendar view
    public function calendar(Request $request)
    {
        $employeeId = session('employee_id');
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        
        $year = (int) $request->get('year', $currentYear);
        $month = (int) $request->get('month', $currentMonth);
        
        // Validate year and month
        if ($year < 2000 || $year > 2100) {
            $year = $currentYear;
        }
        if ($month < 1 || $month > 12) {
            $month = $currentMonth;
        }
        
        // Handle month overflow/underflow
        if ($month < 1) {
            $month = 12;
            $year--;
        } elseif ($month > 12) {
            $month = 1;
            $year++;
        }

        $query = Presence::with('employee')
            ->whereYear('date', $year)
            ->whereMonth('date', $month);

        if (!in_array(session('role'), ['HR Administrator', \App\Constants\Roles::MASTER_ADMIN]) && $employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $presences = $query->get()->map(function ($presence) {
            try {
                $date = $presence->date instanceof \DateTime 
                    ? $presence->date->format('Y-m-d') 
                    : Carbon::parse($presence->date)->format('Y-m-d');
                
                $checkIn = null;
                if ($presence->check_in) {
                    try {
                        $checkIn = Carbon::parse($presence->check_in)->format('H:i');
                    } catch (\Exception $e) {
                        // If check_in is already in time format, try to parse it differently
                        $checkIn = $presence->check_in;
                    }
                }
                
                $checkOut = null;
                if ($presence->check_out) {
                    try {
                        $checkOut = Carbon::parse($presence->check_out)->format('H:i');
                    } catch (\Exception $e) {
                        $checkOut = $presence->check_out;
                    }
                }
                
                return [
                    'id' => $presence->id,
                    'employee' => $presence->employee->fullname ?? 'Unknown',
                    'date' => $date,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'status' => $presence->status,
                    'work_type' => $presence->work_type ?? 'WFO',
                    'is_late' => $presence->is_late, 
                ];
            } catch (\Exception $e) {
                \Log::error('Error processing presence in calendar: ' . $e->getMessage(), [
                    'presence_id' => $presence->id ?? null,
                    'error' => $e->getTraceAsString()
                ]);
                return null;
            }
        })->filter();

        return view('presences.calendar', compact('presences', 'year', 'month'));
    }

    // Statistics/Reports
    public function statistics(Request $request)
    {
        $employeeId = session('employee_id');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $employees = [];
        $selectedEmployeeId = $request->get('employee_id');

        $query = Presence::with('employee')
            ->whereBetween('date', [$startDate, $endDate]);

        if (in_array(session('role'), ['HR Administrator', \App\Constants\Roles::MASTER_ADMIN])) {
            $employees = Employee::orderBy('fullname')->get();
            if ($selectedEmployeeId) {
                $query->where('employee_id', $selectedEmployeeId);
            }
        } else {
            $query->where('employee_id', $employeeId);
        }

        $presences = $query->get();

        // Calculate statistics
        $stats = [
            'total_days' => $presences->count(),
            'present' => $presences->where('status', 'present')->count(),
            'absent' => $presences->where('status', 'absent')->count(),
            'leave' => $presences->where('status', 'leave')->count(),
            // 'late_checkins' => $presences->filter(function ($presence) {
            //     return $this->isLateCheckIn($presence);
            // })->count(), --> old checking
            'late_checkins' => $presences->where('is_late', true)->count(),
            'average_hours' => $presences->filter(function ($presence) {
                return $presence->check_in && $presence->check_out;
            })->map(function ($presence) {
                return Carbon::parse($presence->check_in)->diffInHours(Carbon::parse($presence->check_out));
            })->avg(),
            'work_type_breakdown' => (function() use ($presences) {
                $grouped = $presences->groupBy(function ($p) {
                    return strtoupper($p->work_type ?? 'WFO');
                });
                return [
                    'WFO' => $grouped->get('WFO', collect())->count(),
                    'WFH' => $grouped->get('WFH', collect())->count(),
                    'WFA' => $grouped->get('WFA', collect())->count(),
                ];
            })(),
        ];

        return view('presences.statistics', compact('stats', 'startDate', 'endDate', 'employees', 'selectedEmployeeId'));
    }

    // Helper method to check if check-in is late
    private function isLateCheckIn($presence)
    {
        try {
            if (!$presence->check_in || $presence->status !== 'present') {
                return false;
            }

            $workType = strtolower($presence->work_type ?? 'wfo');
            
            // Get toggle status according to work type (wfo, wfh, wfa)
            $isLateEnabled = \App\Models\Setting::getValue('enable_late_' . $workType, '0');
            
            // If disabled from master presence, it is automatically never late
            if ($isLateEnabled == '0') {
                return false;
            }

            $checkInTime = Carbon::parse($presence->check_in);
            $dateStr = $presence->date instanceof \DateTime 
                ? $presence->date->format('Y-m-d') 
                : (string) $presence->date;
            
            $workStartTimeStr = \App\Models\Setting::getValue('work_start_time', '08:00');
            $workStartTime = Carbon::parse($dateStr . ' ' . $workStartTimeStr);
            
            // Get specific minute tolerance according to the work type
            $lateThreshold = (int) \App\Models\Setting::getValue('late_threshold_' . $workType, 15);

            return $checkInTime->gt($workStartTime->copy()->addMinutes($lateThreshold));
        } catch (\Exception $e) {
            \Log::warning('Error checking late status: ' . $e->getMessage());
            return false;
        }
    }

    public function export(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $query = Presence::with('employee')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->orderBy('employee_id', 'asc');

        $presences = $query->get();

        $filename = 'Presences_' . $startDate . '_to_' . $endDate . '.csv';
        $handle = fopen('php://memory', 'r+');

        // Header
        fputcsv($handle, [
            'Employee Name',
            'Date',
            'Check In',
            'Check Out',
            'Work Type',
            'Status',
            'Is Late'
        ]);

        // Data
        foreach ($presences as $presence) {
            fputcsv($handle, [
                $presence->employee->fullname ?? 'Unknown',
                Carbon::parse($presence->date)->format('Y-m-d'),
                $presence->check_in ? Carbon::parse($presence->check_in)->format('H:i:s') : '-',
                $presence->check_out ? Carbon::parse($presence->check_out)->format('H:i:s') : '-',
                $presence->work_type ?? 'WFO',
                ucfirst($presence->status),
                $presence->is_late ? 'Yes' : 'No' 
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}