<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#159447">
    <link rel="manifest" href="/manifest.webmanifest">
    <title>Masuk — Absensi Kampus</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grid min-h-screen place-items-center bg-slate-50 px-5 py-10">
    <main class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-9">
        <div class="mb-8 flex items-center gap-3">
            <span class="grid size-12 place-items-center rounded-2xl bg-emerald-600 text-xl font-black text-white">A</span>
            <div><h1 class="text-xl font-black text-slate-950">Absensi Kampus</h1><p class="text-sm text-slate-500">Masuk untuk memulai aktivitas</p></div>
        </div>
        @if($errors->any())<div class="mb-5 rounded-xl bg-rose-50 p-3 text-sm font-semibold text-rose-700">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('login.store') }}" class="space-y-5">@csrf
            <label class="block"><span class="field-label">Email</span><input class="field" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus></label>
            <label class="block"><span class="field-label">Kata sandi</span><input class="field" type="password" name="password" autocomplete="current-password" required></label>
            <label class="flex items-center gap-3 text-sm text-slate-600"><input class="size-4 accent-emerald-600" type="checkbox" name="remember" value="1"> Ingat saya</label>
            <button class="primary-button w-full" type="submit">Masuk</button>
        </form>
        <div class="my-6 flex items-center gap-3 text-xs font-bold uppercase tracking-wider text-slate-400"><span class="h-px flex-1 bg-slate-200"></span>atau<span class="h-px flex-1 bg-slate-200"></span></div>
        <button id="install-app" class="secondary-button w-full" type="button"><x-icon name="download" class="size-5" />Pasang aplikasi di HP</button>
        <div id="install-help" class="mt-3 hidden rounded-2xl bg-emerald-50 p-4 text-sm text-slate-700">
            <strong class="block text-emerald-700">Cara memasang aplikasi</strong>
            <p id="install-help-ios" class="mt-1 hidden">Di Safari, tekan tombol <b>Bagikan</b>, lalu pilih <b>Tambahkan ke Layar Utama</b>.</p>
            <p id="install-help-generic" class="mt-1 hidden">Buka menu browser, lalu pilih <b>Instal aplikasi</b> atau <b>Tambahkan ke layar utama</b>.</p>
        </div>
        <p class="mt-6 text-center text-xs text-slate-400">Aplikasi internal — data kehadiran tersimpan aman.</p>
    </main>
</body>
</html>
