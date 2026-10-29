<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\User;

class CoworkerController extends Controller
{
    public function __invoke()
    {
        $attendances = Attendance::whereDate('attendance_date', now())->get()->keyBy('user_id');
        $leaves = LeaveRequest::where('status', 'approved')->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())->get()->keyBy('user_id');
        $employees = User::where('role', 'karyawan')->where('is_active', true)->orderBy('name')->get();

        return view('coworkers', compact('employees', 'attendances', 'leaves'));
    }
}
