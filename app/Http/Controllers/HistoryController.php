<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function __invoke(Request $request)
    {
        $attendances = \App\Models\Attendance::where('user_id', $request->user()->id)
            ->latest('attendance_date')
            ->paginate(12);

        return view('history', compact('attendances'));
    }
}
