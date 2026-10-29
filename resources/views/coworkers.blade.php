@extends('layouts.app')
@section('title', 'Status Rekan Kerja')
@section('content')
<header class="mb-7"><h1 class="page-title">Rekan kerja</h1><p class="page-subtitle">Status hari ini dan kontak WhatsApp karyawan.</p></header>
<div class="grid gap-4 sm:grid-cols-2">
@foreach($employees as $employee)
    @php
        $attendance = $attendances->get($employee->id); $leave = $leaves->get($employee->id);
        $status = $leave ? ucfirst($leave->type) : ($attendance?->clock_out_at ? 'Sudah pulang' : ($attendance?->clock_in_at ? ($attendance->state === 'istirahat' ? 'Istirahat' : 'Hadir') : 'Belum masuk'));
        $color = $leave ? 'bg-emerald-50 text-emerald-700' : ($attendance?->clock_in_at ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-500');
    @endphp
    <article class="surface flex items-center gap-4">
        <span class="grid size-12 shrink-0 place-items-center rounded-full bg-emerald-100 font-black text-emerald-700">{{ strtoupper(substr($employee->name,0,1)) }}</span>
        <div class="min-w-0 flex-1"><h2 class="truncate font-black">{{ $employee->name }}</h2><p class="truncate text-sm text-slate-500">{{ $employee->position ?: 'Karyawan' }}</p><span class="mt-2 inline-block rounded-full px-2.5 py-1 text-xs font-bold {{ $color }}">{{ $status }}</span></div>
        @if($employee->phone)@php($wa = preg_replace('/\D/','',$employee->phone))@php($wa = str_starts_with($wa,'0') ? '62'.substr($wa,1) : $wa)<a class="grid size-11 shrink-0 place-items-center rounded-full bg-emerald-600 font-black text-white" target="_blank" rel="noopener" aria-label="WhatsApp {{ $employee->name }}" href="https://wa.me/{{ $wa }}">WA</a>@endif
    </article>
@endforeach
</div>
@endsection
