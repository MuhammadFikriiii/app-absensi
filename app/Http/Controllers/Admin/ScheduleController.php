<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function edit()
    {
        $schedules = WorkSchedule::whereNull('user_id')->get()->keyBy('day_of_week');

        $days = [
            1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
            5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu',
        ];

        return view('admin.schedules.edit', compact('schedules', 'days'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'schedules' => ['required', 'array'],
            'schedules.*.is_workday' => ['nullable', 'boolean'],
            'schedules.*.start_time' => ['nullable', 'date_format:H:i'],
            'schedules.*.end_time' => ['nullable', 'date_format:H:i'],
            'schedules.*.tolerance_minutes' => ['required', 'integer', 'min:0', 'max:180'],
        ]);

        foreach ($data['schedules'] as $day => $values) {
            WorkSchedule::updateOrCreate(
                ['user_id' => null, 'day_of_week' => (int) $day],
                [
                    'is_workday' => (bool) ($values['is_workday'] ?? false),
                    'start_time' => $values['start_time'] ?: null,
                    'end_time' => $values['end_time'] ?: null,
                    'tolerance_minutes' => $values['tolerance_minutes'],
                ],
            );
        }

        return back()->with('success', 'Jadwal kerja berhasil disimpan.');
    }
}
