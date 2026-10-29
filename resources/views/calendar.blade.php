@extends('layouts.app')
@section('title', 'Kalender')
@section('content')
<header class="mb-7"><h1 class="page-title">Kalender</h1><p class="page-subtitle">Hari libur, izin, dan ulang tahun karyawan.</p></header>
<form class="mb-5 flex gap-3" method="GET"><input class="field max-w-56" type="month" name="month" value="{{ $month->format('Y-m') }}"><button class="secondary-button px-5" type="submit">Tampilkan</button></form>
<div class="mb-5 flex flex-wrap gap-4 text-xs font-semibold text-slate-600"><span><i class="legend bg-rose-500"></i>Libur</span><span><i class="legend bg-emerald-500"></i>Izin</span><span><i class="legend bg-blue-500"></i>Ulang tahun</span></div>
<section class="surface">
    <h2 class="section-title">{{ $month->translatedFormat('F Y') }}</h2>
    <div class="mt-5 space-y-3">
        @forelse($items as $item)
            @php $color = $item->type === 'holiday' ? 'bg-rose-500' : ($item->type === 'birthday' ? 'bg-blue-500' : 'bg-emerald-500'); @endphp
            <article class="flex items-center gap-4 rounded-2xl bg-slate-50 p-4"><span class="grid size-12 shrink-0 place-items-center rounded-2xl text-center font-black text-white {{ $color }}">{{ $item->event_date->format('d') }}</span><div><h3 class="font-bold">{{ $item->title }}</h3><p class="text-sm text-slate-500">{{ $item->event_date->translatedFormat('l, d F') }}{{ $item->description ? ' · '.$item->description : '' }}</p></div></article>
        @empty
            <p class="py-8 text-center text-sm text-slate-500">Tidak ada agenda pada bulan ini.</p>
        @endforelse
    </div>
</section>
@endsection
