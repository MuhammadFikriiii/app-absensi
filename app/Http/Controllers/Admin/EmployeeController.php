<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::where('role', 'karyawan')->orderBy('name')->paginate(20);

        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function edit(User $employee)
    {
        abort_unless($employee->role === 'karyawan', 404);
        return view('admin.employees.edit', compact('employee'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:24'],
            'position' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([...$data, 'role' => 'karyawan', 'is_active' => true]);

        return redirect()->route('admin.employees.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function update(Request $request, User $employee)
    {
        abort_unless($employee->role === 'karyawan', 404);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:24'], 'position' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'], 'is_active' => ['nullable', 'boolean'],
            'daily_transport_override' => ['nullable', 'integer', 'min:0'],
            'overtime_hourly_rate_override' => ['nullable', 'integer', 'min:0'],
            'late_deduction_per_minute_override' => ['nullable', 'integer', 'min:0'],
            'overtime_minimum_minutes_override' => ['nullable', 'integer', 'min:0', 'max:1440'],
        ]);
        $employee->update([...$data, 'is_active' => (bool) ($data['is_active'] ?? false)]);
        return back()->with('success', 'Data dan aturan khusus karyawan berhasil disimpan.');
    }
}
