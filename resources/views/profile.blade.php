@extends('layouts.app')
@section('title', 'Profil')
@section('content')
<header class="mb-7"><h1 class="page-title">Profil saya</h1><p class="page-subtitle">Perbarui data kontak dan tanggal lahir.</p></header>
<section class="surface">
    <div class="mb-7 flex items-center gap-4"><span class="grid size-16 place-items-center rounded-full bg-emerald-100 text-2xl font-black text-emerald-700">{{ strtoupper(substr($user->name, 0, 1)) }}</span><div><h2 class="text-xl font-black">{{ $user->name }}</h2><p class="text-sm text-slate-500">{{ $user->position ?: ucfirst($user->role) }}</p></div></div>
    <form method="POST" action="{{ route('profile.update') }}" class="grid gap-5 sm:grid-cols-2">@csrf @method('PUT')
        <label class="block sm:col-span-2"><span class="field-label">Nama lengkap</span><input class="field" name="name" value="{{ old('name', $user->name) }}" required></label>
        <label class="block"><span class="field-label">Nomor WhatsApp</span><input class="field" name="phone" value="{{ old('phone', $user->phone) }}" inputmode="tel"></label>
        <label class="block"><span class="field-label">Tanggal lahir</span><input class="field" type="date" name="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}"></label>
        <label class="block"><span class="field-label">Email</span><input class="field bg-slate-100" value="{{ $user->email }}" disabled></label>
        <label class="block"><span class="field-label">Jabatan</span><input class="field bg-slate-100" value="{{ $user->position ?: 'Belum diatur' }}" disabled></label>
        <button class="primary-button sm:col-span-2 sm:w-fit" type="submit">Simpan profil</button>
    </form>
</section>
<form method="POST" action="{{ route('logout') }}" class="mt-5 lg:hidden">@csrf<button class="danger-button w-full">Keluar dari aplikasi</button></form>
@endsection
