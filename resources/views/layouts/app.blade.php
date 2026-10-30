<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#111315">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Absensi Kampus">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" href="/icons/app-icon.svg" type="image/svg+xml">
    <script>if(localStorage.getItem('theme')!=='light')document.documentElement.classList.add('dark')</script>
    <title>@yield('title', 'Absensi Kampus')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased transition-colors">
    @php
        $mainNav = [['dashboard','Beranda','home'],['calendar','Kalender','calendar'],['coworkers','Rekan','users'],['history','Riwayat','history'],['corrections.index','Koreksi','edit'],['leaves.index','Izin & Cuti','briefcase'],['payrolls.index','Uang & lembur','wallet'],['profile','Profil','user']];
        $adminNav = [['admin.employees.index','Karyawan','users'],['admin.attendances.index','Absensi harian','check'],['admin.schedules.edit','Jadwal kerja','calendar'],['admin.attendance-settings.edit','Validasi absensi','settings'],['admin.leaves.index','Persetujuan izin','briefcase'],['admin.corrections.index','Persetujuan koreksi','edit'],['admin.recaps.index','Rekap absensi','chart'],['admin.compensations.edit','Aturan uang','wallet'],['admin.payrolls.index','Pembayaran','money'],['admin.audit-logs.index','Audit log','audit']];
    @endphp
    <div class="mx-auto min-h-screen max-w-[1480px] lg:grid lg:grid-cols-[250px_1fr]">
        <aside class="app-sidebar hidden lg:flex lg:h-screen lg:flex-col lg:overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="brand-lockup"><span class="brand-mark"><x-icon name="calendar" class="size-6" /></span><strong>Absensi Kampus</strong></a>
            <nav class="space-y-1.5">
                @foreach($mainNav as [$route,$label,$icon])<a class="side-link {{ request()->routeIs($route) ? 'active' : '' }}" href="{{ route($route) }}"><x-icon :name="$icon" /><span>{{ $label }}</span></a>@endforeach
                @if(auth()->user()->isAdmin())<p class="nav-section">Admin</p>@foreach($adminNav as [$route,$label,$icon])<a class="side-link {{ request()->routeIs(str_replace('.index','.*',str_replace('.edit','.*',$route))) ? 'active' : '' }}" href="{{ route($route) }}"><x-icon :name="$icon" /><span>{{ $label }}</span></a>@endforeach @endif
            </nav>
            <div class="mt-auto space-y-3 pt-8">
                <button id="theme-toggle-side" type="button" class="sidebar-utility"><x-icon name="sun" /><span>Mode tampilan</span><strong class="ml-auto dark:hidden">Terang</strong><strong class="ml-auto hidden dark:inline">Gelap</strong></button>
                <a href="{{ route('profile') }}" class="sidebar-profile"><span>{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span><div><strong>{{ explode(' ',auth()->user()->name)[0] }}</strong><small>{{ auth()->user()->position ?: ucfirst(auth()->user()->role) }}</small></div><x-icon name="chevron" class="ml-auto size-4" /></a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="side-link w-full text-rose-400"><x-icon name="logout" /><span>Keluar</span></button></form>
            </div>
        </aside>

        <div class="min-w-0">
            <header class="app-header sticky top-0 z-30 px-4 py-3 sm:px-6 lg:px-8">
                <div class="mx-auto flex max-w-6xl items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Absensi Kampus</p>
                        <p class="text-sm text-slate-500">{{ auth()->user()->position ?: ucfirst(auth()->user()->role) }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button id="theme-toggle" type="button" class="icon-button" aria-label="Ganti tema"><x-icon name="moon" class="dark:hidden" /><x-icon name="sun" class="hidden dark:block" /></button>
                        <a href="{{ route('profile') }}" class="profile-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</a>
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-6xl px-4 pb-28 pt-6 sm:px-6 lg:px-8 lg:pb-12 lg:pt-8">
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

    <nav class="mobile-nav fixed inset-x-0 bottom-0 z-40 px-2 pb-[max(.65rem,env(safe-area-inset-bottom))] pt-2 lg:hidden">
        <div class="mx-auto grid max-w-lg grid-cols-5">
            @php
                $nav = [
                    ['dashboard', 'Beranda', 'home'],
                    ['calendar', 'Kalender', 'calendar'],
                    ['coworkers', 'Rekan', 'users'],
                    ['history', 'Riwayat', 'history'],
                    ['profile', 'Profil', 'user'],
                ];
            @endphp
            @foreach($nav as [$route, $label, $icon])
                <a href="{{ route($route) }}" class="bottom-link {{ request()->routeIs($route) ? 'active' : '' }}">
                    <x-icon :name="$icon" class="size-5" />
                    <span>{{ $label }}</span>
                </a>
            @endforeach
        </div>
    </nav>
</body>
</html>
