import './bootstrap';

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
