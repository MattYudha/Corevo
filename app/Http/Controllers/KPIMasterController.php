<?php

namespace App\Http\Controllers;

use App\Models\KPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Constants\Roles;

class KPIMasterController extends Controller
{
    public function index()
    {
        $kpis = KPI::with('roles')->orderBy('category')->get();
        return view('kpi-masters.index', compact('kpis'));
    }

    public function edit(KPI $kpi_master)
    {
        $roles = \App\Models\Role::orderBy('title')->get();
        // eager load current roles to check them easily
        $kpi_master->load('roles');
        return view('kpi-masters.edit', compact('kpi_master', 'roles'));
    }

    public function update(Request $request, KPI $kpi_master)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'target_value' => 'required|numeric|min:0',
            'weight' => 'required|numeric|min:0|max:100',
            'unit' => 'required|string|max:50',
        ]);

        $kpi_master->update([
            'name' => $request->name,
            'target_value' => $request->target_value,
            'weight' => $request->weight,
            'unit' => $request->unit,
        ]);

        $roleIds = $request->input('roles', []);
        $syncData = [];
        
        foreach ($roleIds as $roleId) {
            $syncData[$roleId] = [
                'target_value' => $request->target_value,
                'weight' => $request->weight,
            ];
        }

        // Sync the roles and their pivot data
        $kpi_master->roles()->sync($syncData);

        return redirect()->route('kpi-masters.index')->with('success', 'Master KPI berhasil diperbarui.');
    }
}
