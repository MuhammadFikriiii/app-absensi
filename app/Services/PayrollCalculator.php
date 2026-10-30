<?php

namespace App\Services;

use App\Models\CompensationRule;
use App\Models\User;
use App\Models\WorkSchedule;
use Carbon\Carbon;

class PayrollCalculator
{
    public function calculate(User $user, Carbon $start, Carbon $end): array
    {
        $rule = CompensationRule::where('position', $user->position)->first();
        $transportRate = $user->daily_transport_override ?? $rule?->daily_transport ?? 0;
        $overtimeRate = $user->overtime_hourly_rate_override ?? $rule?->overtime_hourly_rate ?? 0;
        $lateRate = $user->late_deduction_per_minute_override ?? $rule?->late_deduction_per_minute ?? 0;
        $overtimeMinimum = $user->overtime_minimum_minutes_override ?? $rule?->overtime_minimum_minutes ?? 180;
        $attendances = $user->attendances()->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])->get();
        $overtimeMinutes = 0;

        foreach ($attendances as $attendance) {
            if (! $attendance->clock_out_at) continue;
            $schedule = WorkSchedule::where('day_of_week', $attendance->attendance_date->isoWeekday())
                ->where(fn ($query) => $query->where('user_id', $user->id)->orWhereNull('user_id'))
                ->orderByRaw('user_id IS NULL')->first();
            if (! $schedule?->is_workday || ! $schedule->end_time) continue;
            $scheduledEnd = $attendance->attendance_date->copy()->setTimeFromTimeString($schedule->end_time);
            $minutes = max(0, (int) $scheduledEnd->diffInMinutes($attendance->clock_out_at, false));
            if ($minutes >= $overtimeMinimum) $overtimeMinutes += $minutes;
        }

        $attendanceDays = $attendances->whereNotNull('clock_in_at')->count();
        $lateMinutes = (int) $attendances->sum('late_minutes');
        $transport = $attendanceDays * $transportRate;
        $overtime = (int) round(($overtimeMinutes / 60) * $overtimeRate);
        $deduction = $lateMinutes * $lateRate;

        return [
            'attendance_days' => $attendanceDays, 'overtime_minutes' => $overtimeMinutes,
            'late_minutes' => $lateMinutes, 'transport_amount' => $transport,
            'overtime_amount' => $overtime, 'late_deduction_amount' => $deduction,
            'net_amount' => $transport + $overtime - $deduction,
            'rates' => compact('transportRate', 'overtimeRate', 'lateRate', 'overtimeMinimum'),
        ];
    }
}
