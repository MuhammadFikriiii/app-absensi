<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompensationRule;
use App\Models\User;
use Illuminate\Http\Request;

class CompensationController extends Controller
{
    public function edit()
    {
        $positions = User::where('role', 'karyawan')->whereNotNull('position')->distinct()->orderBy('position')->pluck('position');
        $rules = CompensationRule::all()->keyBy('position');
        return view('admin.compensations.edit', compact('positions', 'rules'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'rules' => ['required', 'array'], 'rules.*.position' => ['required', 'string', 'max:100'],
            'rules.*.daily_transport' => ['required', 'integer', 'min:0'],
            'rules.*.overtime_hourly_rate' => ['required', 'integer', 'min:0'],
            'rules.*.late_deduction_per_minute' => ['required', 'integer', 'min:0'],
            'rules.*.overtime_minimum_minutes' => ['required', 'integer', 'min:0', 'max:1440'],
        ]);
        foreach ($data['rules'] as $rule) CompensationRule::updateOrCreate(['position' => $rule['position']], $rule);
        return back()->with('success', 'Aturan uang dan lembur berhasil disimpan.');
    }
}
