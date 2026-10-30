<?php

namespace App\Http\Controllers;

use App\Services\PayrollCalculator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function __invoke(Request $request, PayrollCalculator $calculator)
    {
        $month = Carbon::createFromFormat('Y-m', $request->input('month', now()->format('Y-m')))->startOfMonth();
        $start = $month->copy()->startOfMonth(); $end = $month->copy()->endOfMonth();
        $calculation = $calculator->calculate($request->user(), $start, $end);
        $history = $request->user()->payrollPeriods()->latest('period_start')->take(12)->get();
        return view('payrolls.index', compact('month', 'start', 'end', 'calculation', 'history'));
    }
}
