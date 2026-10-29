@extends('layouts.app')
@section('title', 'Jadwal Kerja')
@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-bold uppercase tracking-[0.18em] text-emerald-700">Pengaturan admin</p>
            <h1 class="page-title">Jadwal kerja</h1>
            <p class="page-subtitle">Atur jam kerja umum untuk seluruh karyawan.</p>
        </div>
        <button id="copy-monday" type="button" class="secondary-button">Salin Senin ke Selasa–Jumat</button>
    </header>
    <form method="POST" action="{{ route('admin.schedules.update') }}" class="space-y-4">
        @csrf @method('PUT')
        @foreach($days as $dayNumber => $dayName)
            @php($schedule = $schedules->get($dayNumber))
            <section class="surface grid gap-4 md:grid-cols-[1.2fr_repeat(3,1fr)] md:items-end" data-day="{{ $dayNumber }}">
                <div>
                    <input type="hidden" name="schedules[{{ $dayNumber }}][is_workday]" value="0">
                    <label class="flex min-h-12 cursor-pointer items-center gap-3 rounded-2xl bg-slate-50 px-4">
                        <input class="size-5 accent-emerald-600" type="checkbox" name="schedules[{{ $dayNumber }}][is_workday]" value="1" @checked(old("schedules.$dayNumber.is_workday", $schedule?->is_workday ?? ($dayNumber <= 5)))>
                        <span class="font-black text-slate-900">{{ $dayName }}</span>
                    </label>
                </div>
                <label><span class="field-label">Jam masuk</span><input class="field schedule-start" type="time" name="schedules[{{ $dayNumber }}][start_time]" value="{{ old("schedules.$dayNumber.start_time", $schedule?->start_time ? substr($schedule->start_time, 0, 5) : '09:00') }}" required></label>
                <label><span class="field-label">Jam pulang</span><input class="field schedule-end" type="time" name="schedules[{{ $dayNumber }}][end_time]" value="{{ old("schedules.$dayNumber.end_time", $schedule?->end_time ? substr($schedule->end_time, 0, 5) : '17:00') }}" required></label>
                <label><span class="field-label">Toleransi terlambat</span><div class="relative"><input class="field schedule-tolerance pr-16" type="number" min="0" max="180" name="schedules[{{ $dayNumber }}][late_tolerance_minutes]" value="{{ old("schedules.$dayNumber.late_tolerance_minutes", $schedule?->late_tolerance_minutes ?? 10) }}" required><span class="pointer-events-none absolute right-4 top-3.5 text-sm font-bold text-slate-400">menit</span></div></label>
            </section>
        @endforeach
        <div class="sticky bottom-24 z-20 flex justify-end md:bottom-6"><button class="primary-button w-full shadow-xl sm:w-auto" type="submit">Simpan jadwal</button></div>
    </form>
</div>
@endsection
