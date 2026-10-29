<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#159447">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Absensi Kampus">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" href="/icons/app-icon.svg" type="image/svg+xml">
    <title>@yield('title', 'Absensi Kampus')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <div class="mx-auto min-h-screen max-w-7xl lg:grid lg:grid-cols-[240px_1fr]">
        <aside class="hidden border-r border-slate-200 bg-white px-5 py-7 lg:flex lg:flex-col">
            <a href="{{ route('dashboard') }}" class="mb-10 flex items-center gap-3">
                <span class="grid size-11 place-items-center rounded-2xl bg-emerald-600 text-xl font-black text-white">A</span>
                <span><strong class="block text-lg">Absensi</strong><small class="text-slate-500">Kampus</small></span>
            </a>
            <nav class="space-y-2 text-sm font-semibold">
                <a class="side-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Beranda</a>
                <a class="side-link {{ request()->routeIs('calendar') ? 'active' : '' }}" href="{{ route('calendar') }}">Kalender</a>
                <a class="side-link {{ request()->routeIs('history') ? 'active' : '' }}" href="{{ route('history') }}">Riwayat</a>
                <a class="side-link {{ request()->routeIs('profile') ? 'active' : '' }}" href="{{ route('profile') }}">Profil</a>
                @if(auth()->user()->isAdmin())
                    <p class="px-4 pb-1 pt-6 text-xs font-bold uppercase tracking-wider text-slate-400">Admin</p>
                    <a class="side-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}" href="{{ route('admin.employees.index') }}">Karyawan</a>
                    <a class="side-link {{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}" href="{{ route('admin.attendances.index') }}">Absensi harian</a>
                    <a class="side-link {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}" href="{{ route('admin.schedules.edit') }}">Jadwal kerja</a>
                @endif
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="mt-auto">@csrf
                <button class="side-link w-full text-left text-rose-600">Keluar</button>
            </form>
        </aside>

        <div class="min-w-0">
            <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/95 px-5 py-4 backdrop-blur lg:px-10">
                <div class="mx-auto flex max-w-4xl items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Absensi Kampus</p>
                        <p class="text-sm text-slate-500">{{ auth()->user()->position ?: ucfirst(auth()->user()->role) }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button id="install-app" class="hidden rounded-xl border border-emerald-200 px-3 py-2 text-xs font-bold text-emerald-700">Pasang aplikasi</button>
                        <a href="{{ route('profile') }}" class="grid size-10 place-items-center rounded-full bg-emerald-100 font-bold text-emerald-700">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</a>
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-4xl px-4 pb-28 pt-6 sm:px-6 lg:px-10 lg:pb-10 lg:pt-10">
                @if(session('success'))
                    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">{{ $errors->first() }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white/95 px-2 pb-[max(.65rem,env(safe-area-inset-bottom))] pt-2 backdrop-blur lg:hidden">
        <div class="mx-auto grid max-w-lg grid-cols-4">
            @php
                $nav = [
                    ['dashboard', 'Beranda', '⌂'],
                    ['calendar', 'Kalender', '□'],
                    ['history', 'Riwayat', '◷'],
                    ['profile', 'Profil', '○'],
                ];
            @endphp
            @foreach($nav as [$route, $label, $icon])
                <a href="{{ route($route) }}" class="bottom-link {{ request()->routeIs($route) ? 'active' : '' }}">
                    <span aria-hidden="true" class="text-xl leading-none">{{ $icon }}</span>
                    <span>{{ $label }}</span>
                </a>
            @endforeach
        </div>
    </nav>
</body>
</html>
