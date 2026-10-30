@extends('layouts.app')
@section('title', 'Riwayat Absensi')
@section('content')
<header class="mb-7"><h1 class="page-title">Riwayat absensi</h1><p class="page-subtitle">Catatan masuk, istirahat, dan pulang Anda.</p></header>
<div class="space-y-3">
    @forelse($attendances as $attendance)
        <article class="surface flex flex-wrap items-center justify-between gap-4">
            <div><p class="font-black">{{ $attendance->attendance_date->translatedFormat('l, d F Y') }}</p><p class="mt-1 text-sm text-slate-500">{{ ucfirst($attendance->status) }} @if($attendance->late_minutes) · Terlambat {{ $attendance->late_minutes }} menit @endif</p></div>
            <div class="flex items-center gap-4"><div class="grid grid-cols-2 gap-x-6 text-right text-sm"><div><span class="block text-slate-400">Masuk</span><strong>{{ $attendance->clock_in_at?->format('H:i') ?? '—' }}</strong></div><div><span class="block text-slate-400">Keluar</span><strong>{{ $attendance->clock_out_at?->format('H:i') ?? '—' }}</strong></div></div><a class="rounded-xl border border-emerald-200 px-3 py-2 text-xs font-bold text-emerald-700" href="{{ route('corrections.create',$attendance) }}">Ajukan koreksi</a></div>
        </article>
    @empty
        <div class="empty-state"><h2 class="font-bold">Belum ada riwayat</h2><p class="mt-1 text-sm text-slate-500">Riwayat akan muncul setelah Anda melakukan absensi.</p></div>
    @endforelse
</div>
<div class="mt-5 text-right"><a class="text-sm font-bold text-emerald-700" href="{{ route('corrections.index') }}">Lihat status pengajuan koreksi →</a></div>
<div class="mt-6">{{ $attendances->links() }}</div>
@endsection
