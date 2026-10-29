<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    public function clockIn(Request $request)
    {
        $user = $request->user();
        $now = Carbon::now();

        DB::transaction(function () use ($user, $now) {
            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('attendance_date', $now->toDateString())
                ->first() ?? new Attendance([
                    'user_id' => $user->id,
                    'attendance_date' => $now->toDateString(),
                ]);

            if ($attendance->clock_in_at) {
                throw ValidationException::withMessages(['attendance' => 'Anda sudah absen masuk hari ini.']);
            }

            $schedule = WorkSchedule::where('day_of_week', $now->isoWeekday())
                ->where(fn ($query) => $query->where('user_id', $user->id)->orWhereNull('user_id'))
                ->orderByRaw('user_id IS NULL')
                ->first();

            $lateMinutes = 0;
            if ($schedule?->is_workday && $schedule->start_time) {
                $start = Carbon::parse($now->toDateString().' '.$schedule->start_time)
                    ->addMinutes($schedule->tolerance_minutes);
                $lateMinutes = $now->greaterThan($start) ? (int) $start->diffInMinutes($now) : 0;
            }

            $attendance->fill([
                'clock_in_at' => $now,
                'status' => 'hadir',
                'late_minutes' => $lateMinutes,
            ])->save();
        });

        return back()->with('success', 'Absen masuk berhasil dicatat.');
    }

    public function clockOut(Request $request)
    {
        $attendance = $this->todayAttendance($request);
        if (! $attendance->clock_in_at) return $this->error('Silakan absen masuk terlebih dahulu.');
        if ($attendance->clock_out_at) return $this->error('Anda sudah absen keluar hari ini.');
        if ($attendance->break_started_at && ! $attendance->break_ended_at) {
            return $this->error('Selesaikan istirahat sebelum absen keluar.');
        }

        $attendance->update(['clock_out_at' => now()]);

        return back()->with('success', 'Absen keluar berhasil dicatat.');
    }

    public function startBreak(Request $request)
    {
        $attendance = $this->todayAttendance($request);
        if (! $attendance->clock_in_at || $attendance->clock_out_at) {
            return $this->error('Istirahat hanya tersedia saat jam kerja aktif.');
        }
        if ($attendance->break_started_at) return $this->error('Istirahat hari ini sudah pernah dimulai.');

        $attendance->update(['break_started_at' => now()]);

        return back()->with('success', 'Waktu istirahat dimulai.');
    }

    public function endBreak(Request $request)
    {
        $attendance = $this->todayAttendance($request);
        if (! $attendance->break_started_at || $attendance->break_ended_at) {
            return $this->error('Tidak ada istirahat aktif yang dapat diselesaikan.');
        }

        $attendance->update(['break_ended_at' => now()]);

        return back()->with('success', 'Istirahat selesai. Selamat melanjutkan pekerjaan.');
    }

    private function todayAttendance(Request $request): Attendance
    {
        return Attendance::where('user_id', $request->user()->id)
            ->whereDate('attendance_date', now()->toDateString())
            ->first() ?? new Attendance([
                'user_id' => $request->user()->id,
                'attendance_date' => now()->toDateString(),
            ]);
    }

    private function error(string $message)
    {
        return back()->withErrors(['attendance' => $message]);
    }
}
