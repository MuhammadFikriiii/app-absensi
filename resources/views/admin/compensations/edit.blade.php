@extends('layouts.app')
@section('title','Aturan Uang & Lembur')
@section('content')
<header class="mb-7"><p class="text-sm font-bold uppercase tracking-[.18em] text-emerald-600">Phase 4</p><h1 class="page-title">Aturan uang & lembur</h1><p class="page-subtitle">Nilai standar per jabatan. Nilai khusus per orang dapat diatur dari halaman karyawan.</p></header>
<form method="POST" action="{{ route('admin.compensations.update') }}">@csrf @method('PUT')
    <div class="space-y-4">
        @forelse($positions as $index => $position)
        @php($rule=$rules->get($position))
        <section class="surface">
            <input type="hidden" name="rules[{{ $index }}][position]" value="{{ $position }}">
            <h2 class="section-title">{{ $position }}</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <label><span class="field-label">Transport per hadir (Rp)</span><input class="field" type="number" min="0" name="rules[{{ $index }}][daily_transport]" value="{{ old("rules.$index.daily_transport",$rule?->daily_transport??0) }}" required></label>
                <label><span class="field-label">Lembur per jam (Rp)</span><input class="field" type="number" min="0" name="rules[{{ $index }}][overtime_hourly_rate]" value="{{ old("rules.$index.overtime_hourly_rate",$rule?->overtime_hourly_rate??0) }}" required></label>
                <label><span class="field-label">Potongan telat/menit (Rp)</span><input class="field" type="number" min="0" name="rules[{{ $index }}][late_deduction_per_minute]" value="{{ old("rules.$index.late_deduction_per_minute",$rule?->late_deduction_per_minute??0) }}" required></label>
                <label><span class="field-label">Minimal lembur (menit)</span><input class="field" type="number" min="0" max="1440" name="rules[{{ $index }}][overtime_minimum_minutes]" value="{{ old("rules.$index.overtime_minimum_minutes",$rule?->overtime_minimum_minutes??180) }}" required></label>
            </div>
        </section>
        @empty
        <div class="empty-state">Belum ada jabatan pada data karyawan.</div>
        @endforelse
    </div>
    @if($positions->isNotEmpty())<button class="primary-button mt-5">Simpan semua aturan</button>@endif
</form>
@endsection
