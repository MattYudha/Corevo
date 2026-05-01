<?php

namespace App\Http\Controllers;

use App\Models\OfficeLocation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OfficeLocationController extends Controller
{
    public function index()
    {
        $officeLocations = OfficeLocation::withCount('employees')
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();

        return view('office-locations.index', compact('officeLocations'));
    }

    public function create()
    {
        return view('office-locations.create');
    }

    public function store(Request $request)
    {
        OfficeLocation::create($this->validatedData($request));

        return redirect()
            ->route('office-locations.index')
            ->with('success', 'Lokasi kantor berhasil ditambahkan.');
    }

    public function edit(OfficeLocation $officeLocation)
    {
        return view('office-locations.edit', compact('officeLocation'));
    }

    public function update(Request $request, OfficeLocation $officeLocation)
    {
        $officeLocation->update($this->validatedData($request, $officeLocation->id));

        return redirect()
            ->route('office-locations.index')
            ->with('success', 'Lokasi kantor berhasil diperbarui.');
    }

    public function destroy(OfficeLocation $officeLocation)
    {
        if ($officeLocation->employees()->exists()) {
            return redirect()
                ->route('office-locations.index')
                ->with('error', 'Lokasi kantor tidak dapat dihapus karena masih dipakai oleh karyawan.');
        }

        $officeLocation->delete();

        return redirect()
            ->route('office-locations.index')
            ->with('success', 'Lokasi kantor berhasil dihapus.');
    }

    protected function validatedData(Request $request, ?int $officeLocationId = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('office_locations', 'name')->ignore($officeLocationId)],
            'location_type' => ['required', Rule::in(['head_office', 'branch', 'other'])],
            'address' => ['nullable', 'string'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius' => ['required', 'integer', 'min:10', 'max:50000'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'notes' => ['nullable', 'string'],
        ]);

        $data['allowed_ssids'] = $this->normalizeTextArray($request->input('allowed_ssids_text'));
        $data['allowed_ips'] = $this->normalizeTextArray($request->input('allowed_ips_text'));

        return $data;
    }

    protected function normalizeTextArray(?string $value): array
    {
        $lines = preg_split('/\r\n|\r|\n/', (string) $value) ?: [];
        $items = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line !== '') $items[] = $line;
        }
        return array_values(array_unique($items));
    }

//     protected function normalizeSsids(?string $value): array
//     {
//         $lines = preg_split('/
// |
// |
// /', (string) $value) ?: [];
//         $ssids = [];

//         foreach ($lines as $line) {
//             $line = trim($line);
//             if ($line !== '') {
//                 $ssids[] = $line;
//             }
//         }

//         return array_values(array_unique($ssids));
//     }
}
