@extends('layouts.app')
@section('title', 'Izin dan Cuti')
@section('content')
<header class="mb-7 flex items-end justify-between gap-4"><div><h1 class="page-title">Izin & cuti</h1><p class="page-subtitle">Pantau seluruh pengajuan Anda.</p></div><a class="primary-button" href="{{ route('leaves.create') }}">Buat pengajuan</a></header>
<div class="space-y-4">
@forelse($requests as $item)
    @php $badge = $item->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($item->status === 'rejected' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700'); @endphp
    <article class="surface"><div class="flex items-start justify-between gap-3"><div><h2 class="font-black">{{ ucfirst($item->type) }}</h2><p class="text-sm text-slate-500">{{ $item->start_date->translatedFormat('d M Y') }} – {{ $item->end_date->translatedFormat('d M Y') }}</p></div><span class="rounded-full px-3 py-1 text-xs font-bold {{ $badge }}">{{ $item->status === 'pending' ? 'Menunggu' : ($item->status === 'approved' ? 'Disetujui' : 'Ditolak') }}</span></div><p class="mt-4 text-sm">{{ $item->reason }}</p>@if($item->admin_note)<p class="mt-3 rounded-2xl bg-slate-50 p-3 text-sm"><strong>Catatan admin:</strong> {{ $item->admin_note }}</p>@endif</article>
@empty <div class="empty-state"><h2 class="font-black">Belum ada pengajuan</h2><p class="mt-2 text-sm text-slate-500">Gunakan tombol di atas untuk membuat pengajuan pertama.</p></div>
@endforelse
</div>
@endsection
