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

        $items = $events->concat($birthdays)->sortBy('event_date')->values();
        $itemsByDate = $items->groupBy(fn ($item) => $item->event_date->toDateString());
        $gridStart = $month->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $month->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);
        $days = collect();
        for ($date = $gridStart->copy(); $date->lte($gridEnd); $date->addDay()) {
            $days->push($date->copy());
        }
        $previousMonth = $month->copy()->subMonth()->format('Y-m');
        $nextMonth = $month->copy()->addMonth()->format('Y-m');

        if ($request->wantsJson()) {
            return response()->json([
                'month' => $month->format('Y-m'),
                'monthLabel' => $month->translatedFormat('F Y'),
                'previousUrl' => route('calendar', ['month' => $previousMonth]),
                'nextUrl' => route('calendar', ['month' => $nextMonth]),
                'currentUrl' => route('calendar', ['month' => $month->format('Y-m')]),
                'days' => $days->map(function ($day) use ($month, $itemsByDate) {
                    $events = $itemsByDate->get($day->toDateString(), collect());
                    $types = $events->pluck('type')->unique()->values();
                    $primaryType = $types->contains('holiday') ? 'holiday' : ($types->contains('leave') ? 'leave' : ($types->contains('birthday') ? 'birthday' : ($types->isNotEmpty() ? 'agenda' : null)));
                    return [
                        'date' => $day->toDateString(), 'dateLabel' => $day->translatedFormat('l, d F Y'),
                        'day' => $day->day, 'inMonth' => $day->isSameMonth($month), 'today' => $day->isToday(),
                        'primaryType' => $primaryType, 'types' => $types,
                        'events' => $events->map(fn ($item) => ['title' => $item->title, 'type' => $item->type, 'description' => $item->description])->values(),
                    ];
                })->values(),
                'items' => $items->map(fn ($item) => [
                    'title' => $item->title, 'type' => $item->type, 'description' => $item->description,
                    'day' => $item->event_date->format('d'), 'dateLabel' => $item->event_date->translatedFormat('l, d F'),
                ])->values(),
            ]);
        }

        return view('calendar', compact('month', 'items', 'itemsByDate', 'days', 'previousMonth', 'nextMonth'));
    }
}
