@props(['name' => 'selfie', 'label' => 'Selfie'])
<div data-camera-capture>
    {{-- File input tersembunyi — diisi oleh kamera ATAU picker galeri --}}
    <input class="hidden" data-camera-file type="file" name="{{ $name }}" accept="image/*" required>
    {{-- Input galeri terpisah, hanya muncul sebagai fallback saat kamera tidak bisa dibuka --}}
    <input class="hidden" data-camera-gallery-pick type="file" name="_gallery_pick" accept="image/*" capture="user">
    <dialog class="camera-dialog" data-camera-dialog aria-label="{{ $label }}">
        <div class="flex items-start justify-between gap-4">
            <div><p class="text-xs font-black uppercase tracking-wider text-emerald-600">Verifikasi kehadiran</p><h2 class="mt-1 text-xl font-black">{{ $label }}</h2></div>
            <button class="camera-dialog-close" type="button" data-camera-close aria-label="Tutup">×</button>
        </div>
        <p class="mt-2 text-sm text-slate-500">Posisikan wajah di tengah, lalu ambil foto.</p>
        <div class="camera-shell mt-4">
            <video class="camera-video" data-camera-video autoplay muted playsinline></video>
            <img class="camera-preview hidden" data-camera-preview alt="Preview selfie">
            <div class="camera-error hidden p-6 text-center text-white" data-camera-error>
                <x-icon name="camera" class="mx-auto size-10" />
                <strong class="mt-3 block">Kamera langsung tidak tersedia</strong>
                <small class="mt-2 block text-slate-300">Gunakan tombol di bawah untuk mengambil foto dari kamera atau galeri HP.</small>
            </div>
        </div>
        <div class="mt-4 grid grid-cols-2 gap-2">
            <button class="primary-button col-span-2" type="button" data-camera-take><x-icon name="camera" class="size-5" />Potret sekarang</button>
            {{-- Tombol fallback: muncul saat kamera tidak tersedia (HTTP) --}}
            <button class="secondary-button col-span-2 hidden" type="button" data-camera-pick-gallery><x-icon name="image" class="size-5" />Pilih / ambil foto dari HP</button>
            <button class="secondary-button hidden" type="button" data-camera-retake>Ambil ulang</button>
            <button class="primary-button hidden" type="button" data-camera-confirm>Gunakan foto &amp; lanjut absen</button>
        </div>
    </dialog>
</div>
