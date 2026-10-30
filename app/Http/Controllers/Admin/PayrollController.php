<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollPeriod;
use App\Models\User;
use App\Services\PayrollCalculator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index(Request $request, PayrollCalculator $calculator)
    {
        $month = Carbon::createFromFormat('Y-m', $request->input('month', now()->format('Y-m')))->startOfMonth();
        $start = $month->copy()->startOfMonth(); $end = $month->copy()->endOfMonth();
        $employees = User::where('role', 'karyawan')->where('is_active', true)->orderBy('name')->get();
        $saved = PayrollPeriod::where('period_start', $start->toDateString())->where('period_end', $end->toDateString())->get()->keyBy('user_id');
        $rows = $employees->map(fn ($user) => ['user' => $user, 'calculation' => $calculator->calculate($user, $start, $end), 'payroll' => $saved->get($user->id)]);
        return view('admin.payrolls.index', compact('month', 'start', 'end', 'rows'));
    }

    public function pay(Request $request, User $user, PayrollCalculator $calculator)
    {
        abort_unless($user->role === 'karyawan', 404);
        $data = $request->validate(['month' => ['required', 'date_format:Y-m']]);
        $start = Carbon::createFromFormat('Y-m', $data['month'])->startOfMonth(); $end = $start->copy()->endOfMonth();
        $values = $calculator->calculate($user, $start, $end);
        unset($values['rates']);
        PayrollPeriod::updateOrCreate(
            ['user_id' => $user->id, 'period_start' => $start->toDateString(), 'period_end' => $end->toDateString()],
            [...$values, 'status' => 'paid', 'paid_at' => now(), 'paid_by' => $request->user()->id],
        );
        return back()->with('success', 'Pembayaran '.$user->name.' ditandai sudah dibayar.');
    }
}
