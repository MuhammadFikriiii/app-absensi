<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceCorrection;
use App\Models\AuditLog;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceCorrectionController extends Controller
{
    public function index()
    {
        return view('admin.corrections.index', ['corrections' => AttendanceCorrection::with(['user', 'attendance'])->latest()->get()]);
    }

    public function update(Request $request, AttendanceCorrection $attendanceCorrection)
    {
        $data = $request->validate(['status' => ['required', 'in:approved,rejected'], 'admin_note' => ['nullable', 'string', 'max:500']]);
        abort_if($attendanceCorrection->status !== 'pending', 422, 'Pengajuan ini sudah ditinjau.');
        DB::transaction(function () use ($request, $attendanceCorrection, $data) {
            $correction = AttendanceCorrection::lockForUpdate()->findOrFail($attendanceCorrection->id);
            $attendance = $correction->attendance()->lockForUpdate()->firstOrFail();
            $old = collect(['clock_in_at', 'break_started_at', 'break_ended_at', 'clock_out_at', 'late_minutes'])->mapWithKeys(fn ($field) => [$field => $attendance->{$field} instanceof Carbon ? $attendance->{$field}->toDateTimeString() : $attendance->{$field}])->all();
            if ($data['status'] === 'approved') {
                $attendance->fill([
                    'clock_in_at' => $correction->requested_clock_in_at, 'break_started_at' => $correction->requested_break_started_at,
                    'break_ended_at' => $correction->requested_break_ended_at, 'clock_out_at' => $correction->requested_clock_out_at,
                ]);
                $attendance->late_minutes = $this->lateMinutes($attendance);
                $attendance->save();
            }
            $correction->update([...$data, 'reviewed_by' => $request->user()->id, 'reviewed_at' => now()]);
            AuditLog::record('correction_'.$data['status'], $correction, $data['status'] === 'approved' ? 'Admin menyetujui koreksi absensi.' : 'Admin menolak koreksi absensi.', $old, $data['status'] === 'approved' ? $attendance->only(array_keys($old)) : null);
        });

        return back()->with('success', 'Pengajuan koreksi berhasil ditinjau.');
    }

    private function lateMinutes($attendance): int
    {
        if (! $attendance->clock_in_at) {
            return 0;
        }
        $schedule = WorkSchedule::where('day_of_week', $attendance->attendance_date->isoWeekday())->where(fn ($q) => $q->where('user_id', $attendance->user_id)->orWhereNull('user_id'))->orderByRaw('user_id IS NULL')->first();
        if (! $schedule?->is_workday || ! $schedule->start_time) {
            return 0;
        }
        $start = Carbon::parse($attendance->attendance_date->toDateString().' '.$schedule->start_time)->addMinutes($schedule->tolerance_minutes);

        return $attendance->clock_in_at->greaterThan($start) ? (int) $start->diffInMinutes($attendance->clock_in_at) : 0;
    }
}
