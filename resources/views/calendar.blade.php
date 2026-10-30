@extends('layouts.app')
@section('title', 'Kalender')
@section('content')
<header class="mb-7"><h1 class="page-title">Kalender</h1><p class="page-subtitle">Geser kalender atau gunakan tombol panah untuk berpindah bulan.</p></header>

<div class="mb-5 flex flex-wrap gap-4 text-xs font-semibold text-slate-600">
    <span><i class="legend bg-rose-500"></i>Libur</span>
    <span><i class="legend bg-emerald-500"></i>Izin/cuti</span>
    <span><i class="legend bg-blue-500"></i>Ulang tahun</span>
    <span><i class="legend bg-violet-500"></i>Agenda</span>
</div>

<section class="surface" data-calendar-swipe data-previous-url="{{ route('calendar',['month'=>$previousMonth]) }}" data-next-url="{{ route('calendar',['month'=>$nextMonth]) }}">
    <div class="mb-5 flex items-center justify-between gap-3">
        <a class="calendar-arrow" href="{{ route('calendar',['month'=>$previousMonth]) }}" aria-label="Bulan sebelumnya">←</a>
        <div class="text-center"><h2 class="text-xl font-black sm:text-2xl">{{ $month->translatedFormat('F Y') }}</h2><a class="text-xs font-bold text-emerald-600" href="{{ route('calendar',['month'=>now()->format('Y-m')]) }}">Kembali ke bulan ini</a></div>
        <a class="calendar-arrow" href="{{ route('calendar',['month'=>$nextMonth]) }}" aria-label="Bulan berikutnya">→</a>
    </div>

    <div class="calendar-grid mb-2 text-center text-[10px] font-black uppercase tracking-wide text-slate-500 sm:text-xs">
        @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $dayName)<div class="py-2">{{ $dayName }}</div>@endforeach
    </div>
    <div class="calendar-grid overflow-hidden rounded-2xl border border-slate-200">
        @foreach($days as $day)
            @php
                $dayItems = $itemsByDate->get($day->toDateString(), collect());
                $types = $dayItems->pluck('type')->unique();
                $primaryType = $types->contains('holiday') ? 'holiday' : ($types->contains('leave') ? 'leave' : ($types->contains('birthday') ? 'birthday' : ($types->isNotEmpty() ? 'agenda' : null)));
            @endphp
            <div class="calendar-day {{ !$day->isSameMonth($month) ? 'outside' : '' }} {{ $primaryType ? 'has-event event-'.$primaryType : '' }}">
                <span class="calendar-number {{ $day->isToday() ? 'today' : '' }}">{{ $day->day }}</span>
                @if($dayItems->isNotEmpty())
                    <div class="mt-auto flex flex-wrap gap-1 sm:mt-2">
                        @foreach($types as $type)<i class="calendar-dot dot-{{ in_array($type,['holiday','leave','birthday']) ? $type : 'agenda' }}"></i>@endforeach
                    </div>
                    <span class="mt-1 hidden truncate text-[10px] font-bold sm:block">{{ $dayItems->first()->title }}</span>
                @endif
            </div>
        @endforeach
    </div>
    <p class="mt-3 text-center text-xs text-slate-500 sm:hidden">Geser ke kiri atau kanan untuk mengganti bulan.</p>
</section>

<section class="mt-5 space-y-3">
    <h2 class="section-title">Agenda {{ $month->translatedFormat('F Y') }}</h2>
    @forelse($items as $item)
        @php $color = $item->type === 'holiday' ? 'bg-rose-500' : ($item->type === 'birthday' ? 'bg-blue-500' : ($item->type === 'leave' ? 'bg-emerald-500' : 'bg-violet-500')); @endphp
        <article class="surface flex items-center gap-4 p-4"><span class="grid size-12 shrink-0 place-items-center rounded-2xl text-center font-black text-white {{ $color }}">{{ $item->event_date->format('d') }}</span><div class="min-w-0"><h3 class="font-bold">{{ $item->title }}</h3><p class="text-sm text-slate-500">{{ $item->event_date->translatedFormat('l, d F') }}{{ $item->description ? ' · '.$item->description : '' }}</p></div></article>
    @empty
        <div class="empty-state">Tidak ada agenda pada bulan ini.</div>
    @endforelse
</section>
@endsection
