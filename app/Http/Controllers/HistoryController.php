<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function __invoke(Request $request)
    {
        $attendances = Attendance::where('user_id', $request->user()->id)
            ->latest('attendance_date')
            ->paginate(12);

        return view('history', compact('attendances'));
    }
}
