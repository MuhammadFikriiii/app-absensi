@extends('layouts.app')
@section('title', 'Buat Pengajuan')
@section('content')
<div class="mx-auto max-w-2xl"><header class="mb-7"><a class="text-sm font-bold text-emerald-700" href="{{ route('leaves.index') }}">← Kembali</a><h1 class="page-title mt-3">Buat pengajuan</h1><p class="page-subtitle">Isi data izin, sakit, atau cuti dengan singkat dan jelas.</p></header>
<form class="surface space-y-5" method="POST" enctype="multipart/form-data" action="{{ route('leaves.store') }}">@csrf
<label><span class="field-label">Jenis</span><select class="field" name="type" required><option value="izin">Izin</option><option value="sakit">Sakit</option><option value="cuti">Cuti</option></select></label>
<div class="grid gap-4 sm:grid-cols-2"><label><span class="field-label">Mulai</span><input class="field" type="date" name="start_date" value="{{ old('start_date') }}" required></label><label><span class="field-label">Selesai</span><input class="field" type="date" name="end_date" value="{{ old('end_date') }}" required></label></div>
<label><span class="field-label">Alasan</span><textarea class="field min-h-32" name="reason" required>{{ old('reason') }}</textarea></label>
<label><span class="field-label">Lampiran (opsional)</span><input class="field" type="file" name="attachment" accept="image/*,.pdf"><span class="mt-2 block text-xs text-slate-500">JPG, PNG, atau PDF maksimal 3 MB.</span></label>
<button class="primary-button w-full">Kirim pengajuan</button></form></div>
@endsection
