<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function __invoke(Request $request)
    {
        $monthValue = $request->input('month', now()->format('Y-m'));
        $month = Carbon::createFromFormat('Y-m', $monthValue)->startOfMonth();
        $events = CalendarEvent::with('user')
            ->whereBetween('event_date', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
            ->orderBy('event_date')
            ->get();

        $birthdays = User::where('is_active', true)->whereNotNull('birth_date')->get()
            ->filter(fn ($user) => $user->birth_date->month === $month->month)
            ->map(fn ($user) => (object) [
                'title' => 'Ulang tahun '.$user->name,
                'event_date' => $month->copy()->day(min($user->birth_date->day, $month->daysInMonth)),
                'type' => 'birthday',
                'description' => $user->position,
            ]);

        $items = $events->concat($birthdays)->sortBy('event_date');

        return view('calendar', compact('month', 'items'));
    }
}
