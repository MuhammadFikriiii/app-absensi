<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AttendanceCorrectionController extends Controller
{
    public function index(Request $request)
    {
        return view('corrections.index', ['corrections' => $request->user()->attendanceCorrections()->with('attendance')->latest()->get()]);
    }

    public function create(Request $request, Attendance $attendance)
    {
        abort_unless($attendance->user_id === $request->user()->id, 403);
        abort_if($attendance->corrections()->where('status', 'pending')->exists(), 422, 'Absensi ini masih memiliki pengajuan yang menunggu.');

        return view('corrections.create', compact('attendance'));
    }

    public function store(Request $request, Attendance $attendance)
    {
        abort_unless($attendance->user_id === $request->user()->id, 403);
        if ($attendance->corrections()->where('status', 'pending')->exists()) {
            throw ValidationException::withMessages(['attendance' => 'Absensi ini masih memiliki pengajuan yang menunggu.']);
        }
        $data = $request->validate([
            'clock_in' => ['required', 'date_format:H:i'], 'break_started' => ['nullable', 'date_format:H:i', 'required_with:break_ended'],
            'break_ended' => ['nullable', 'date_format:H:i', 'required_with:break_started'], 'clock_out' => ['required', 'date_format:H:i'],
            'reason' => ['required', 'string', 'max:1000'], 'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:3072'],
        ]);
        $date = $attendance->attendance_date->toDateString();
        $values = [
            'requested_clock_in_at' => $this->dateTime($date, $data['clock_in'] ?? null),
            'requested_break_started_at' => $this->dateTime($date, $data['break_started'] ?? null),
            'requested_break_ended_at' => $this->dateTime($date, $data['break_ended'] ?? null),
            'requested_clock_out_at' => $this->dateTime($date, $data['clock_out'] ?? null),
        ];
        if ($values['requested_clock_in_at'] && $values['requested_clock_out_at'] && $values['requested_clock_out_at']->lte($values['requested_clock_in_at'])) {
            throw ValidationException::withMessages(['clock_out' => 'Jam keluar harus setelah jam masuk.']);
        }
        if ($values['requested_break_started_at'] && $values['requested_break_ended_at'] && $values['requested_break_ended_at']->lte($values['requested_break_started_at'])) {
            throw ValidationException::withMessages(['break_ended' => 'Selesai istirahat harus setelah mulai istirahat.']);
        }
        if ($request->hasFile('attachment')) {
            $values['attachment'] = $request->file('attachment')->store('correction-attachments', 'public');
        }
        $correction = $attendance->corrections()->create([...$values, 'user_id' => $request->user()->id, 'reason' => $data['reason'], 'original_values' => $this->snapshot($attendance)]);
        AuditLog::record('correction_submitted', $correction, 'Karyawan mengajukan koreksi absensi.', null, $correction->only(array_keys($values)));

        return redirect()->route('corrections.index')->with('success', 'Pengajuan koreksi berhasil dikirim.');
    }

    private function dateTime(string $date, ?string $time): ?Carbon
    {
        return $time ? Carbon::parse($date.' '.$time) : null;
    }

    private function snapshot(Attendance $attendance): array
    {
        return collect(['clock_in_at', 'break_started_at', 'break_ended_at', 'clock_out_at'])->mapWithKeys(fn ($field) => [$field => $attendance->{$field}?->toDateTimeString()])->all();
    }
}
