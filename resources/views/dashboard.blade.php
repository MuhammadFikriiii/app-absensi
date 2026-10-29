@extends('layouts.app')
@section('title', 'Beranda — Absensi Kampus')
@section('content')
@php
    $state = $attendance->state;
    $stateText = match($state) {
        'bekerja' => 'Sedang bekerja',
        'istirahat' => 'Sedang istirahat',
        'selesai' => 'Absensi hari ini selesai',
        default => 'Belum absen masuk',
    };
    $greeting = now()->hour < 11 ? 'Selamat pagi' : (now()->hour < 15 ? 'Selamat siang' : 'Selamat sore');
@endphp
<section class="mb-7 flex items-start justify-between gap-4">
    <div><h1 class="text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">{{ $greeting }}, {{ explode(' ', $user->name)[0] }}</h1><p class="mt-1 text-slate-500">{{ now()->translatedFormat('l, d F Y') }}</p></div>
    <span class="hidden rounded-full bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 sm:inline">● Online</span>
</section>

<div class="grid gap-4 md:grid-cols-2">
    <section class="surface bg-emerald-50/70">
        <p class="text-sm font-bold text-slate-500">Jadwal hari ini</p>
        <p class="mt-2 text-3xl font-black tracking-tight text-slate-950">{{ $schedule?->is_workday ? substr($schedule->start_time, 0, 5).' – '.substr($schedule->end_time, 0, 5) : 'Hari libur' }}</p>
        @if($schedule?->is_workday)<p class="mt-2 text-sm text-slate-500">Toleransi keterlambatan {{ $schedule->tolerance_minutes }} menit</p>@endif
    </section>
    <section class="surface {{ $state === 'selesai' ? 'bg-emerald-50' : ($state === 'istirahat' ? 'bg-amber-50' : 'bg-rose-50') }}">
        <p class="text-sm font-bold text-slate-500">Status hari ini</p>
        <p class="mt-2 text-xl font-black {{ $state === 'selesai' ? 'text-emerald-700' : ($state === 'istirahat' ? 'text-amber-700' : 'text-rose-700') }}">{{ $stateText }}</p>
        @if($attendance->clock_in_at)<p class="mt-2 text-sm text-slate-500">Masuk pukul {{ $attendance->clock_in_at->format('H:i') }}</p>@endif
    </section>
</div>

<section class="mt-5 grid gap-3 sm:grid-cols-2">
    @if(!$attendance->clock_in_at)
        <form method="POST" action="{{ route('attendance.clock-in') }}" class="sm:col-span-2">@csrf<button class="primary-button w-full text-lg">Absen Masuk</button></form>
    @elseif(!$attendance->clock_out_at)
        <form method="POST" action="{{ route('attendance.clock-out') }}">@csrf<button class="danger-button w-full">Absen Keluar</button></form>
        @if(!$attendance->break_started_at)
            <form method="POST" action="{{ route('attendance.break.start') }}">@csrf<button class="secondary-button w-full">Mulai Istirahat</button></form>
        @elseif(!$attendance->break_ended_at)
            <form method="POST" action="{{ route('attendance.break.end') }}">@csrf<button class="secondary-button w-full">Selesai Istirahat</button></form>
        @else
            <button disabled class="secondary-button w-full opacity-50">Istirahat selesai</button>
        @endif
    @else
        <div class="surface sm:col-span-2"><p class="font-bold text-emerald-700">Terima kasih, absensi hari ini sudah lengkap.</p><p class="mt-1 text-sm text-slate-500">Pulang pukul {{ $attendance->clock_out_at->format('H:i') }}</p></div>
    @endif
</section>

<section class="surface mt-6">
    <h2 class="section-title">Ringkasan bulan ini</h2>
    <div class="mt-5 grid grid-cols-3 divide-x divide-slate-200 text-center">
        <div><strong class="block text-3xl text-emerald-700">{{ $stats['hadir'] }}</strong><span class="text-sm text-slate-500">Hadir</span></div>
        <div><strong class="block text-3xl text-amber-600">{{ $stats['terlambat'] }}</strong><span class="text-sm text-slate-500">Terlambat</span></div>
        <div><strong class="block text-3xl text-blue-600">{{ $stats['izin'] }}</strong><span class="text-sm text-slate-500">Izin</span></div>
    </div>
</section>

<section class="surface mt-5">
    <div class="flex items-center justify-between"><h2 class="section-title">Agenda terdekat</h2><a href="{{ route('calendar') }}" class="text-sm font-bold text-emerald-700">Lihat kalender</a></div>
    @if($nextEvent)
        <div class="mt-4 rounded-2xl bg-slate-50 p-4"><p class="font-bold">{{ $nextEvent->title }}</p><p class="mt-1 text-sm text-slate-500">{{ $nextEvent->event_date->translatedFormat('l, d F Y') }}</p></div>
    @else
        <p class="mt-4 text-sm text-slate-500">Belum ada agenda mendatang.</p>
    @endif
</section>
@endsection
