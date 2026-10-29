@extends('layouts.app')
@section('title', 'Absensi Harian')
@section('content')
<div class="space-y-6">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div><p class="text-sm font-bold uppercase tracking-[0.18em] text-emerald-700">Pantauan admin</p><h1 class="page-title">Absensi harian</h1><p class="page-subtitle">Lihat status masuk, istirahat, dan pulang seluruh karyawan.</p></div>
        <form method="GET" class="flex gap-2"><input class="field" type="date" name="date" value="{{ $date->toDateString() }}"><button class="secondary-button px-5">Lihat</button></form>
    </header>
    <div class="space-y-3">
        @forelse($employees as $employee)
            @php($attendance = $attendances->get($employee->id))
            <article class="surface flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div><h2 class="font-black text-slate-950">{{ $employee->name }}</h2><p class="text-sm text-slate-500">{{ $employee->position ?: 'Karyawan' }}</p></div>
                <div class="grid grid-cols-3 gap-4 text-sm sm:text-right">
                    <div><span class="block text-xs text-slate-400">Masuk</span><strong>{{ $attendance?->clock_in_at?->format('H:i') ?? '—' }}</strong></div>
                    <div><span class="block text-xs text-slate-400">Pulang</span><strong>{{ $attendance?->clock_out_at?->format('H:i') ?? '—' }}</strong></div>
                    <div><span class="block text-xs text-slate-400">Status</span><strong class="{{ $attendance?->clock_in_at ? 'text-emerald-700' : 'text-slate-500' }}">{{ $attendance?->clock_in_at ? ucfirst($attendance->state) : 'Belum masuk' }}</strong></div>
                </div>
            </article>
        @empty
            <div class="empty-state"><h2 class="font-black">Belum ada karyawan aktif</h2></div>
        @endforelse
    </div>
</div>
@endsection
