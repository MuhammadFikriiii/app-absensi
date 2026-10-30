@props(['name' => 'selfie', 'label' => 'Selfie'])
<div class="camera-capture" data-camera-capture>
    <span class="field-label">{{ $label }}</span>
    <div class="camera-shell">
        <div class="camera-placeholder" data-camera-placeholder>
            <x-icon name="user" class="size-10" />
            <strong>Foto wajah langsung di aplikasi</strong>
            <small>Pastikan wajah terlihat jelas dan pencahayaan cukup.</small>
        </div>
        <video class="camera-video hidden" data-camera-video autoplay muted playsinline></video>
        <img class="camera-preview hidden" data-camera-preview alt="Preview selfie">
    </div>
    <input class="hidden" data-camera-file type="file" name="{{ $name }}" accept="image/*" required>
    <div class="mt-3 grid grid-cols-2 gap-2">
        <button class="primary-button col-span-2" type="button" data-camera-start><x-icon name="camera" class="size-5" />Buka kamera</button>
        <button class="primary-button hidden" type="button" data-camera-take>Ambil foto</button>
        <button class="secondary-button hidden" type="button" data-camera-retake>Ambil ulang</button>
    </div>
    <label class="mt-3 hidden" data-camera-fallback><span class="field-label">Kamera dalam aplikasi tidak tersedia</span><input class="field" type="file" accept="image/*" capture="user"><small class="mt-2 block text-slate-500">Gunakan pilihan ini hanya jika browser tidak mendukung kamera langsung.</small></label>
</div>
