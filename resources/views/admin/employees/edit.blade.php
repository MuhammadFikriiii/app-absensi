@extends('layouts.app')
@section('title','Edit Karyawan')
@section('content')
<header class="mb-7"><a class="text-sm font-bold text-emerald-600" href="{{ route('admin.employees.index') }}">← Kembali</a><h1 class="page-title mt-3">Edit {{ $employee->name }}</h1><p class="page-subtitle">Kosongkan aturan khusus untuk memakai aturan jabatan.</p></header>
<form method="POST" action="{{ route('admin.employees.update',$employee) }}" class="space-y-5">@csrf @method('PUT')
    <section class="surface grid gap-5 sm:grid-cols-2">
        <label class="sm:col-span-2"><span class="field-label">Nama</span><input class="field" name="name" value="{{ old('name',$employee->name) }}" required></label>
        <label><span class="field-label">WhatsApp</span><input class="field" name="phone" value="{{ old('phone',$employee->phone) }}"></label>
        <label><span class="field-label">Jabatan</span><input class="field" name="position" value="{{ old('position',$employee->position) }}"></label>
        <label><span class="field-label">Tanggal lahir</span><input class="field" type="date" name="birth_date" value="{{ old('birth_date',$employee->birth_date?->toDateString()) }}"></label>
        <label class="flex items-center gap-3 pt-7"><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$employee->is_active))><span class="font-bold">Akun aktif</span></label>
    </section>
    <section class="surface"><h2 class="section-title">Aturan khusus karyawan</h2><div class="mt-4 grid gap-4 sm:grid-cols-2">
        <label><span class="field-label">Transport per hadir (Rp)</span><input class="field" type="number" min="0" name="daily_transport_override" value="{{ old('daily_transport_override',$employee->daily_transport_override) }}" placeholder="Ikuti jabatan"></label>
        <label><span class="field-label">Lembur per jam (Rp)</span><input class="field" type="number" min="0" name="overtime_hourly_rate_override" value="{{ old('overtime_hourly_rate_override',$employee->overtime_hourly_rate_override) }}" placeholder="Ikuti jabatan"></label>
        <label><span class="field-label">Potongan telat/menit (Rp)</span><input class="field" type="number" min="0" name="late_deduction_per_minute_override" value="{{ old('late_deduction_per_minute_override',$employee->late_deduction_per_minute_override) }}" placeholder="Ikuti jabatan"></label>
        <label><span class="field-label">Minimal lembur (menit)</span><input class="field" type="number" min="0" name="overtime_minimum_minutes_override" value="{{ old('overtime_minimum_minutes_override',$employee->overtime_minimum_minutes_override) }}" placeholder="Ikuti jabatan"></label>
    </div></section>
    <button class="primary-button">Simpan perubahan</button>
</form>
@endsection
