@extends('layouts.app')
@section('title', 'Tambah Karyawan')
@section('content')
<header class="mb-7"><a class="text-sm font-bold text-emerald-700" href="{{ route('admin.employees.index') }}">← Kembali</a><h1 class="page-title mt-3">Tambah karyawan</h1><p class="page-subtitle">Buat akun baru agar karyawan dapat mulai menggunakan aplikasi.</p></header>
<form method="POST" action="{{ route('admin.employees.store') }}" class="surface grid gap-5 sm:grid-cols-2">@csrf
    <label class="block sm:col-span-2"><span class="field-label">Nama lengkap</span><input class="field" name="name" value="{{ old('name') }}" required></label>
    <label class="block"><span class="field-label">Email</span><input class="field" type="email" name="email" value="{{ old('email') }}" required></label>
    <label class="block"><span class="field-label">Kata sandi awal</span><input class="field" type="password" name="password" required></label>
    <label class="block"><span class="field-label">Nomor WhatsApp</span><input class="field" name="phone" value="{{ old('phone') }}"></label>
    <label class="block"><span class="field-label">Jabatan</span><input class="field" name="position" value="{{ old('position') }}"></label>
    <label class="block"><span class="field-label">Tanggal lahir</span><input class="field" type="date" name="birth_date" value="{{ old('birth_date') }}"></label>
    <button class="primary-button sm:col-span-2 sm:w-fit" type="submit">Simpan karyawan</button>
</form>
@endsection
