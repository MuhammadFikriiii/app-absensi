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
        <p class="mt-6 text-center text-xs text-slate-400">Aplikasi internal — data kehadiran tersimpan aman.</p>
    </main>
</body>
</html>
