@extends('layouts.app')
@section('title', 'Karyawan')
@section('content')
<header class="mb-7 flex flex-wrap items-end justify-between gap-4"><div><h1 class="page-title">Daftar karyawan</h1><p class="page-subtitle">Akun yang dapat menggunakan absensi.</p></div><a class="primary-button" href="{{ route('admin.employees.create') }}">Tambah karyawan</a></header>
<section class="surface overflow-x-auto">
    <table class="w-full min-w-[620px] text-left text-sm"><thead class="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-400"><tr><th class="pb-3">Nama</th><th class="pb-3">Jabatan</th><th class="pb-3">WhatsApp</th><th class="pb-3">Status</th></tr></thead><tbody class="divide-y divide-slate-100">
        @foreach($employees as $employee)<tr><td class="py-4"><a class="font-bold hover:text-emerald-600" href="{{ route('admin.employees.edit',$employee) }}">{{ $employee->name }}</a><span class="block text-slate-500">{{ $employee->email }}</span></td><td>{{ $employee->position ?: '—' }}</td><td>@if($employee->phone)<a class="font-semibold text-emerald-700" target="_blank" href="https://wa.me/{{ preg_replace('/\D/', '', $employee->phone) }}">{{ $employee->phone }}</a>@else — @endif</td><td><span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $employee->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}</span></td></tr>@endforeach
    </tbody></table>
</section>
<div class="mt-6">{{ $employees->links() }}</div>
@endsection
