<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecapController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $period = $request->input('period', 'daily');
        $anchor = Carbon::parse($request->input('date', now()->toDateString()));
        [$start, $end] = match ($period) {
            'weekly' => [$anchor->copy()->startOfWeek(), $anchor->copy()->endOfWeek()],
            'monthly' => [$anchor->copy()->startOfMonth(), $anchor->copy()->endOfMonth()],
            default => [$anchor->copy()->startOfDay(), $anchor->copy()->endOfDay()],
        };
        $attendances = Attendance::with('user')
            ->whereDate('attendance_date', '>=', $start->toDateString())
            ->whereDate('attendance_date', '<=', $end->toDateString())
            ->orderBy('attendance_date')
            ->get();
        $workMinutes = $attendances->sum(function ($item) {
            if (! $item->clock_in_at || ! $item->clock_out_at) {
                return 0;
            }
            $minutes = $item->clock_in_at->diffInMinutes($item->clock_out_at);
            if ($item->break_started_at && $item->break_ended_at) {
                $minutes -= $item->break_started_at->diffInMinutes($item->break_ended_at);
            }

            return max(0, (int) $minutes);
        });
        $stats = ['hadir' => $attendances->whereNotNull('clock_in_at')->count(), 'terlambat' => $attendances->where('late_minutes', '>', 0)->count(), 'belum_lengkap' => $attendances->whereNotNull('clock_in_at')->whereNull('clock_out_at')->count(), 'jam_kerja' => round($workMinutes / 60, 1)];

        return view('admin.recaps.index', compact('period', 'anchor', 'start', 'end', 'attendances', 'stats'));
    }
}
