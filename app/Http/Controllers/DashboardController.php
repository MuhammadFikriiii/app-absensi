<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\CalendarEvent;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', $today->toDateString())
            ->first() ?? new Attendance([
                'user_id' => $user->id,
                'attendance_date' => $today->toDateString(),
            ]);
        $schedule = WorkSchedule::where('day_of_week', $today->isoWeekday())
            ->where(fn ($query) => $query->where('user_id', $user->id)->orWhereNull('user_id'))
            ->orderByRaw('user_id IS NULL')
            ->first();

        $monthQuery = Attendance::where('user_id', $user->id)
            ->whereBetween('attendance_date', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()]);

        $stats = [
            'hadir' => (clone $monthQuery)->whereNotNull('clock_in_at')->count(),
            'terlambat' => (clone $monthQuery)->where('late_minutes', '>', 0)->count(),
            'izin' => (clone $monthQuery)->whereIn('status', ['izin', 'sakit', 'cuti'])->count(),
        ];

        $nextEvent = CalendarEvent::whereDate('event_date', '>=', $today)->orderBy('event_date')->first();

        return view('dashboard', compact('user', 'today', 'attendance', 'schedule', 'stats', 'nextEvent'));
    }
}
