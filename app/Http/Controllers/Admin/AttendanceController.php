<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __invoke(Request $request)
    {
        $date = Carbon::parse($request->input('date', now()->toDateString()));
        $attendances = Attendance::with('user')
            ->whereDate('attendance_date', $date)
            ->get()
            ->keyBy('user_id');

        $employees = User::where('role', 'karyawan')->where('is_active', true)->orderBy('name')->get();

        return view('admin.attendances.index', compact('date', 'attendances', 'employees'));
    }
}
