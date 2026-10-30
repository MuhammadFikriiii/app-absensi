@extends('layouts.app')
@section('title', 'Beranda — Absensi Kampus')
@section('content')
@php
    $state = $attendance->state;
    $stateText = match($state) {
        'bekerja' => 'Sedang bekerja',
        'istirahat' => 'Sedang istirahat',
        'selesai' => 'Absensi selesai',
        default => 'Belum absen masuk',
    };
    $greeting = now()->hour < 11 ? 'Selamat pagi' : (now()->hour < 15 ? 'Selamat siang' : 'Selamat sore');
    $quickActions = [
        ['coworkers','Rekan kerja','Lihat status tim','users'],
        ['leaves.index','Izin & cuti','Buat pengajuan','briefcase'],
        ['calendar','Kalender','Agenda kantor','calendar'],
        ['history','Riwayat','Catatan absensi','history'],
    ];
@endphp

<section class="mb-6 flex items-start justify-between gap-4">
    <div><p class="mb-1 text-sm font-semibold text-emerald-600">{{ now()->translatedFormat('l, d F Y') }}</p><h1 class="page-title">{{ $greeting }}, {{ explode(' ', $user->name)[0] }}</h1></div>
    <span class="hidden items-center gap-2 rounded-full border border-emerald-600/30 px-3 py-2 text-xs font-bold text-emerald-600 sm:flex"><span class="size-2 rounded-full bg-emerald-500"></span>Online</span>
</section>

<section class="dashboard-status">
    <div class="grid gap-5 lg:grid-cols-[1fr_auto] lg:items-center">
        <div class="flex items-start gap-4">
            <span class="dashboard-icon"><x-icon name="clock" /></span>
            <div>
                <p class="text-sm font-bold text-slate-500">Jadwal hari ini</p>
                <p class="mt-1 text-2xl font-black tracking-tight">{{ $schedule?->is_workday ? substr($schedule->start_time, 0, 5).' – '.substr($schedule->end_time, 0, 5) : 'Hari libur' }}</p>
                <p class="mt-2 text-sm text-slate-500">
                    {{ $stateText }}
                    @if($attendance->clock_in_at)
                        · Masuk {{ $attendance->clock_in_at->format('H:i') }}
                    @elseif($schedule?->is_workday)
                        · Toleransi {{ $schedule->tolerance_minutes }} menit
                    @endif
                </p>
            </div>
        </div>

        <div class="min-w-[220px]">
            @if(!$attendance->clock_in_at)
                <form method="POST" enctype="multipart/form-data" action="{{ route('attendance.clock-in') }}" class="attendance-form" data-require-location="{{ $validationSettings->gps_enabled ? '1' : '0' }}">
                    @csrf
                    <input type="hidden" name="latitude"><input type="hidden" name="longitude">
                    @if($validationSettings->selfie_enabled)<div class="mb-4"><x-camera-capture label="Selfie masuk" /></div>@endif
                    <button type="submit" class="primary-button w-full"><x-icon name="login" class="size-5" />Absen Masuk</button>
                </form>
            @elseif(!$attendance->clock_out_at)
                <form method="POST" enctype="multipart/form-data" action="{{ route('attendance.clock-out') }}" class="attendance-form" data-require-location="{{ $validationSettings->gps_enabled ? '1' : '0' }}">
                    @csrf<input type="hidden" name="latitude"><input type="hidden" name="longitude">
                    @if($validationSettings->selfie_enabled)<div class="mb-4"><x-camera-capture label="Selfie keluar" /></div>@endif
                    <button type="submit" class="danger-button w-full">Absen Keluar</button>
                </form>
                @if(!$attendance->break_started_at)
                    <form class="mt-2" method="POST" action="{{ route('attendance.break.start') }}">@csrf<button class="secondary-button w-full">Mulai Istirahat</button></form>
                @elseif(!$attendance->break_ended_at)
                    <form class="mt-2" method="POST" action="{{ route('attendance.break.end') }}">@csrf<button class="secondary-button w-full">Selesai Istirahat</button></form>
                @endif
            @else
                <div class="rounded-2xl bg-emerald-50 p-4 text-sm font-bold text-emerald-700">Selesai · Pulang {{ $attendance->clock_out_at->format('H:i') }}</div>
            @endif
        </div>
    </div>
</section>

@if($validationSettings->gps_enabled || $validationSettings->network_enabled || $validationSettings->selfie_enabled)
<div class="mt-3 flex flex-wrap gap-2 text-xs font-bold">
    @if($validationSettings->gps_enabled)<span class="rounded-full bg-slate-100 px-3 py-2 text-slate-600">GPS · {{ $validationSettings->office_radius_meters }} m</span>@endif
    @if($validationSettings->network_enabled)<span class="rounded-full bg-slate-100 px-3 py-2 text-slate-600">WiFi kampus</span>@endif
    @if($validationSettings->selfie_enabled)<span class="rounded-full bg-slate-100 px-3 py-2 text-slate-600">Selfie wajib</span>@endif
</div>
@endif

<section class="mt-8">
    <h2 class="section-title">Menu cepat</h2>
    <div class="mt-4 grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach($quickActions as [$route,$label,$description,$icon])
            <a class="dashboard-tile" href="{{ route($route) }}"><span class="dashboard-icon"><x-icon :name="$icon" /></span><span class="min-w-0"><strong class="block text-sm">{{ $label }}</strong><small class="mt-0.5 block truncate text-slate-500">{{ $description }}</small></span></a>
        @endforeach
    </div>
</section>

<div class="mt-8 grid gap-5 lg:grid-cols-2">
    <section class="surface">
        <h2 class="section-title">Ringkasan bulan ini</h2>
        <div class="mt-5 grid grid-cols-3 divide-x divide-slate-200 text-center">
            <div><strong class="block text-3xl text-emerald-600">{{ $stats['hadir'] }}</strong><span class="text-xs text-slate-500 sm:text-sm">Hadir</span></div>
            <div><strong class="block text-3xl text-amber-500">{{ $stats['terlambat'] }}</strong><span class="text-xs text-slate-500 sm:text-sm">Terlambat</span></div>
            <div><strong class="block text-3xl text-blue-500">{{ $stats['izin'] }}</strong><span class="text-xs text-slate-500 sm:text-sm">Izin</span></div>
        </div>
    </section>
    <section class="surface">
        <div class="flex items-center justify-between gap-3"><h2 class="section-title">Agenda terdekat</h2><a href="{{ route('calendar') }}" class="text-sm font-bold text-emerald-600">Lihat semua</a></div>
        @if($nextEvent)
            <div class="mt-4 rounded-2xl bg-slate-50 p-4"><p class="font-bold">{{ $nextEvent->title }}</p><p class="mt-1 text-sm text-slate-500">{{ $nextEvent->event_date->translatedFormat('l, d F Y') }}</p></div>
        @else
            <p class="mt-4 text-sm text-slate-500">Belum ada agenda mendatang.</p>
        @endif
    </section>
</div>
@endsection
