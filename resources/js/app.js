import './bootstrap';

document.querySelectorAll('#theme-toggle, #theme-toggle-side').forEach((button) => {
    button.addEventListener('click', () => {
        const dark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', dark ? 'dark' : 'light');
    });
});

document.querySelectorAll('.attendance-form').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        if (form.dataset.requireLocation !== '1' || form.dataset.locationReady === '1') return;
        event.preventDefault();
        const button = form.querySelector('button[type="submit"]');
        const original = button.textContent;
        button.disabled = true; button.textContent = 'Mengambil lokasi…';
        if (!navigator.geolocation) { alert('Perangkat tidak mendukung GPS.'); button.disabled = false; button.textContent = original; return; }
        navigator.geolocation.getCurrentPosition((position) => {
            form.querySelector('[name="latitude"]').value = position.coords.latitude;
            form.querySelector('[name="longitude"]').value = position.coords.longitude;
            form.dataset.locationReady = '1';
            form.requestSubmit();
        }, () => {
            alert('Lokasi tidak dapat diakses. Izinkan akses lokasi lalu coba kembali.');
            button.disabled = false; button.textContent = original;
        }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
    });
});

if ('serviceWorker' in navigator) window.addEventListener('load', () => navigator.serviceWorker.register('/service-worker.js'));

let installPrompt;
const installButton = document.querySelector('#install-app');
window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault(); installPrompt = event; installButton?.classList.remove('hidden');
});
installButton?.addEventListener('click', async () => {
    if (!installPrompt) return;
    installPrompt.prompt(); await installPrompt.userChoice; installPrompt = null; installButton.classList.add('hidden');
});

document.querySelector('#copy-monday')?.addEventListener('click', () => {
    const monday = document.querySelector('[data-day="1"]');
    if (!monday) return;
    for (let day = 2; day <= 5; day += 1) {
        const target = document.querySelector(`[data-day="${day}"]`);
        target.querySelector('input[type="checkbox"]').checked = monday.querySelector('input[type="checkbox"]').checked;
        target.querySelector('.schedule-start').value = monday.querySelector('.schedule-start').value;
        target.querySelector('.schedule-end').value = monday.querySelector('.schedule-end').value;
        target.querySelector('.schedule-tolerance').value = monday.querySelector('.schedule-tolerance').value;
    }
});

document.querySelector('#set-office-location')?.addEventListener('click', () => {
    const button = document.querySelector('#set-office-location');
    const status = document.querySelector('#office-location-status');
    if (!navigator.geolocation) return alert('Perangkat ini tidak mendukung GPS.');
    button.disabled = true;
    button.textContent = 'Mengambil lokasi…';
    navigator.geolocation.getCurrentPosition(({ coords }) => {
        document.querySelector('#office-latitude').value = coords.latitude.toFixed(7);
        document.querySelector('#office-longitude').value = coords.longitude.toFixed(7);
        status.textContent = 'Titik berhasil diambil · akurasi sekitar ' + Math.round(coords.accuracy) + ' meter.';
        status.classList.remove('hidden');
        button.disabled = false;
        button.textContent = 'Perbarui titik lokasi';
    }, () => {
        button.disabled = false;
        button.textContent = 'Ambil titik lokasi';
        alert('Lokasi tidak dapat diakses. Izinkan akses lokasi pada browser lalu coba kembali.');
    }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
});

document.querySelectorAll('[data-camera-capture]').forEach((camera) => {
    const video = camera.querySelector('[data-camera-video]');
    const preview = camera.querySelector('[data-camera-preview]');
    const placeholder = camera.querySelector('[data-camera-placeholder]');
    const fileInput = camera.querySelector('[data-camera-file]');
    const startButton = camera.querySelector('[data-camera-start]');
    const takeButton = camera.querySelector('[data-camera-take]');
    const retakeButton = camera.querySelector('[data-camera-retake]');
    const fallback = camera.querySelector('[data-camera-fallback]');
    const fallbackInput = fallback.querySelector('input');
    let stream;

    const stopCamera = () => {
        stream?.getTracks().forEach((track) => track.stop());
        stream = null;
    };
    const openCamera = async () => {
        if (!navigator.mediaDevices?.getUserMedia) {
            fallback.classList.remove('hidden');
            startButton.classList.add('hidden');
            return alert('Kamera langsung membutuhkan HTTPS atau localhost. Gunakan pilihan kamera cadangan di bawah.');
        }
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
            video.srcObject = stream;
            placeholder.classList.add('hidden'); preview.classList.add('hidden'); video.classList.remove('hidden');
            startButton.classList.add('hidden'); retakeButton.classList.add('hidden'); takeButton.classList.remove('hidden');
        } catch {
            fallback.classList.remove('hidden');
            startButton.classList.add('hidden');
            alert('Kamera tidak dapat dibuka. Izinkan akses kamera atau gunakan pilihan cadangan.');
        }
    };
    startButton.addEventListener('click', openCamera);
    takeButton.addEventListener('click', () => {
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth; canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        canvas.toBlob((blob) => {
            const file = new File([blob], 'selfie-' + Date.now() + '.jpg', { type: 'image/jpeg' });
            const transfer = new DataTransfer(); transfer.items.add(file); fileInput.files = transfer.files;
            preview.src = URL.createObjectURL(blob);
            stopCamera(); video.classList.add('hidden'); takeButton.classList.add('hidden');
            preview.classList.remove('hidden'); retakeButton.classList.remove('hidden');
        }, 'image/jpeg', 0.85);
    });
    retakeButton.addEventListener('click', openCamera);
    fallbackInput.addEventListener('change', () => {
        if (!fallbackInput.files[0]) return;
        const transfer = new DataTransfer(); transfer.items.add(fallbackInput.files[0]); fileInput.files = transfer.files;
        preview.src = URL.createObjectURL(fallbackInput.files[0]);
        placeholder.classList.add('hidden'); preview.classList.remove('hidden');
    });
    camera.closest('form')?.addEventListener('submit', (event) => {
        if (!fileInput.files.length) {
            event.preventDefault();
            alert('Ambil selfie terlebih dahulu sebelum melanjutkan absensi.');
        }
    });
    window.addEventListener('pagehide', stopCamera);
});

document.querySelectorAll('[data-calendar-swipe]').forEach((calendar) => {
    let startX = 0;
    let startY = 0;
    calendar.addEventListener('touchstart', (event) => {
        startX = event.touches[0].clientX;
        startY = event.touches[0].clientY;
    }, { passive: true });
    calendar.addEventListener('touchend', (event) => {
        const deltaX = event.changedTouches[0].clientX - startX;
        const deltaY = event.changedTouches[0].clientY - startY;
        if (Math.abs(deltaX) < 70 || Math.abs(deltaX) < Math.abs(deltaY) * 1.5) return;
        window.location.href = deltaX < 0 ? calendar.dataset.nextUrl : calendar.dataset.previousUrl;
    }, { passive: true });
});
