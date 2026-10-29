import './bootstrap';

document.querySelector('#theme-toggle')?.addEventListener('click', () => {
    const dark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', dark ? 'dark' : 'light');
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
