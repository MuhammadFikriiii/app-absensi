@extends('layouts.app')
@section('title', 'Ajukan Koreksi Absensi')
@section('content')
<div class="mx-auto max-w-2xl">
<header class="mb-7"><a class="text-sm font-bold text-emerald-700" href="{{ route('history') }}">← Kembali ke riwayat</a><h1 class="page-title mt-3">Ajukan koreksi</h1><p class="page-subtitle">{{ $attendance->attendance_date->translatedFormat('l, d F Y') }} · isi jam yang seharusnya.</p></header>
<form class="surface space-y-5" method="POST" enctype="multipart/form-data" action="{{ route('corrections.store',$attendance) }}">@csrf
<div class="grid gap-4 sm:grid-cols-2"><label><span class="field-label">Jam masuk</span><input class="field" type="time" name="clock_in" value="{{ old('clock_in',$attendance->clock_in_at?->format('H:i')) }}"></label><label><span class="field-label">Jam keluar</span><input class="field" type="time" name="clock_out" value="{{ old('clock_out',$attendance->clock_out_at?->format('H:i')) }}"></label><label><span class="field-label">Mulai istirahat</span><input class="field" type="time" name="break_started" value="{{ old('break_started',$attendance->break_started_at?->format('H:i')) }}"></label><label><span class="field-label">Selesai istirahat</span><input class="field" type="time" name="break_ended" value="{{ old('break_ended',$attendance->break_ended_at?->format('H:i')) }}"></label></div>
<label><span class="field-label">Alasan koreksi</span><textarea class="field min-h-32" name="reason" required placeholder="Contoh: lupa absen keluar, seharusnya pukul 17.05">{{ old('reason') }}</textarea></label>
<label><span class="field-label">Bukti pendukung (opsional)</span><input class="field" type="file" name="attachment" accept="image/*,.pdf"><span class="mt-2 block text-xs text-slate-500">JPG, PNG, atau PDF maksimal 3 MB.</span></label>
<button class="primary-button w-full">Kirim pengajuan</button></form></div>
@endsection
