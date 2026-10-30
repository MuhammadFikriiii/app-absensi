import './bootstrap';

document.querySelectorAll('#theme-toggle, #theme-toggle-side').forEach((button) => {
    button.addEventListener('click', () => {
        const dark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', dark ? 'dark' : 'light');
    });
});

document.querySelectorAll('.attendance-form').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        // Jika kamera belum siap, biarkan listener kamera (di bawah) yang handle — jangan return dulu di sini
        // agar event bisa di-preventDefault oleh listener kamera
        if (form.dataset.requireLocation !== '1' || form.dataset.locationReady === '1') return;
        // GPS wajib tapi belum diambil — ambil dulu
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

// PWA Install — capture beforeinstallprompt sesegera mungkin
let installPrompt = null;
window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    installPrompt = event;
    // Tampilkan tombol install jika sebelumnya sudah di-render (mungkin hidden)
    const btn = document.querySelector('#install-app');
    btn?.classList.remove('hidden');
});

const installButton = document.querySelector('#install-app');
// Sembunyikan tombol jika sudah berjalan sebagai PWA standalone
if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
    installButton?.classList.add('hidden');
}

installButton?.addEventListener('click', async () => {
    if (installPrompt) {
        installPrompt.prompt();
        const choice = await installPrompt.userChoice;
        if (choice.outcome === 'accepted') installButton.classList.add('hidden');
        installPrompt = null;
        return;
    }
    // Tidak ada prompt native — tampilkan panduan manual
    const help = document.querySelector('#install-help');
    const isIos = /iphone|ipad|ipod/i.test(navigator.userAgent);
    help?.classList.remove('hidden');
    document.querySelector(isIos ? '#install-help-ios' : '#install-help-generic')?.classList.remove('hidden');
});

window.addEventListener('appinstalled', () => installButton?.classList.add('hidden'));

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
    const form = camera.closest('form');
    const dialog = camera.querySelector('[data-camera-dialog]');
    const video = camera.querySelector('[data-camera-video]');
    const preview = camera.querySelector('[data-camera-preview]');
    const errorPanel = camera.querySelector('[data-camera-error]');
    const fileInput = camera.querySelector('[data-camera-file]');
    const takeButton = camera.querySelector('[data-camera-take]');
    const retakeButton = camera.querySelector('[data-camera-retake]');
    const confirmButton = camera.querySelector('[data-camera-confirm]');
    const closeButton = camera.querySelector('[data-camera-close]');
    const pickGalleryButton = camera.querySelector('[data-camera-pick-gallery]');
    let stream;

    const stopCamera = () => {
        stream?.getTracks().forEach((track) => track.stop());
        stream = null;
    };

    // Tampilkan foto yang dipilih di preview dan siapkan untuk submit
    const useFile = (file) => {
        if (!file) return;
        // Salin ke fileInput utama (nama field "selfie")
        const transfer = new DataTransfer();
        transfer.items.add(file);
        fileInput.files = transfer.files;
        preview.src = URL.createObjectURL(file);
        video.classList.add('hidden');
        takeButton.classList.add('hidden');
        pickGalleryButton?.classList.add('hidden');
        errorPanel.classList.add('hidden');
        preview.classList.remove('hidden');
        retakeButton.classList.remove('hidden');
        confirmButton.classList.remove('hidden');
    };

    const showCameraError = () => {
        video.classList.add('hidden');
        takeButton.classList.add('hidden');
        errorPanel.classList.remove('hidden');
        // Tampilkan tombol fallback galeri
        pickGalleryButton?.classList.remove('hidden');
    };

    const openCamera = async () => {
        dialog.showModal();
        preview.classList.add('hidden');
        errorPanel.classList.add('hidden');
        video.classList.remove('hidden');
        takeButton.classList.remove('hidden');
        retakeButton.classList.add('hidden');
        confirmButton.classList.add('hidden');
        pickGalleryButton?.classList.add('hidden');
        if (!navigator.mediaDevices?.getUserMedia) {
            showCameraError();
            return;
        }
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
            video.srcObject = stream;
        } catch {
            showCameraError();
        }
    };

    takeButton.addEventListener('click', () => {
        if (!video.videoWidth) return;
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth; canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        canvas.toBlob((blob) => {
            const file = new File([blob], 'selfie-' + Date.now() + '.jpg', { type: 'image/jpeg' });
            stopCamera();
            useFile(file);
        }, 'image/jpeg', 0.85);
    });

    // Fallback: buka file picker saat kamera tidak tersedia (HTTP)
    pickGalleryButton?.addEventListener('click', () => {
        // Input sementara dengan capture untuk buka kamera HP langsung
        const picker = document.createElement('input');
        picker.type = 'file';
        picker.accept = 'image/*';
        picker.capture = 'user'; // buka kamera depan HP
        picker.addEventListener('change', () => {
            if (picker.files[0]) useFile(picker.files[0]);
        });
        picker.click();
    });

    retakeButton.addEventListener('click', openCamera);
    confirmButton.addEventListener('click', () => {
        dialog.close();
        form.dataset.selfieReady = '1';
        form.requestSubmit();
    });
    closeButton.addEventListener('click', () => {
        stopCamera();
        dialog.close();
    });
    dialog.addEventListener('close', stopCamera);
    form.addEventListener('submit', (event) => {
        if (form.dataset.selfieReady !== '1' || !fileInput.files.length) {
            event.preventDefault();
            openCamera();
        }
    });
    window.addEventListener('pagehide', stopCamera);
});


const calendarDialog = document.querySelector('#calendar-event-dialog');
const calendarLabels = { holiday: 'Libur', leave: 'Izin / cuti', birthday: 'Ulang tahun' };
const openCalendarDialog = (day) => {
    const events = JSON.parse(day.dataset.events || '[]');
    document.querySelector('#calendar-dialog-date').textContent = day.dataset.date;
    const container = document.querySelector('#calendar-dialog-events');
    container.replaceChildren();
    events.forEach((event) => {
        const article = document.createElement('article');
        article.className = 'rounded-2xl bg-slate-50 p-4';
        const type = document.createElement('span');
        type.className = 'text-xs font-black uppercase tracking-wider text-emerald-600';
        type.textContent = calendarLabels[event.type] || 'Agenda';
        const title = document.createElement('h3');
        title.className = 'mt-1 font-black';
        title.textContent = event.title;
        article.append(type, title);
        if (event.description) {
            const description = document.createElement('p');
            description.className = 'mt-1 text-sm text-slate-500';
            description.textContent = event.description;
            article.append(description);
        }
        container.append(article);
    });
    calendarDialog?.showModal();
};
document.addEventListener('click', (event) => {
    const day = event.target.closest('[data-calendar-day]:not(:disabled)');
    if (day) openCalendarDialog(day);
});
document.querySelectorAll('[data-calendar-close]').forEach((button) => button.addEventListener('click', () => calendarDialog?.close()));
calendarDialog?.addEventListener('click', (event) => {
    if (event.target === calendarDialog) calendarDialog.close();
});

const calendarPanel = document.querySelector('#calendar-panel');
const createCalendarDay = (day) => {
    const button = document.createElement('button');
    button.type = 'button';
    button.disabled = day.events.length === 0;
    button.className = ['calendar-day', 'text-left', !day.inMonth ? 'outside' : '', day.primaryType ? 'has-event event-' + day.primaryType : ''].filter(Boolean).join(' ');
    button.dataset.calendarDay = '';
    button.dataset.date = day.dateLabel;
    button.dataset.events = JSON.stringify(day.events);
    button.setAttribute('aria-label', day.events.length ? 'Lihat agenda ' + day.dateLabel : day.dateLabel);
    const number = document.createElement('span');
    number.className = 'calendar-number' + (day.today ? ' today' : '');
    number.textContent = day.day;
    button.append(number);
    if (day.events.length) {
        const dots = document.createElement('div');
        dots.className = 'mt-auto flex flex-wrap gap-1 sm:mt-2';
        day.types.forEach((typeName) => {
            const dot = document.createElement('i');
            dot.className = 'calendar-dot dot-' + (['holiday', 'leave', 'birthday'].includes(typeName) ? typeName : 'agenda');
            dots.append(dot);
        });
        const title = document.createElement('span');
        title.className = 'mt-1 hidden truncate text-[10px] font-bold sm:block';
        title.textContent = day.events[0].title;
        button.append(dots, title);
    }
    return button;
};
const createAgendaItem = (item) => {
    const article = document.createElement('article');
    article.className = 'surface flex items-center gap-4 p-4';
    const date = document.createElement('span');
    const color = item.type === 'holiday' ? 'bg-rose-500' : (item.type === 'birthday' ? 'bg-blue-500' : (item.type === 'leave' ? 'bg-emerald-500' : 'bg-violet-500'));
    date.className = 'grid size-12 shrink-0 place-items-center rounded-2xl text-center font-black text-white ' + color;
    date.textContent = item.day;
    const content = document.createElement('div');
    content.className = 'min-w-0';
    const title = document.createElement('h3');
    title.className = 'font-bold';
    title.textContent = item.title;
    const description = document.createElement('p');
    description.className = 'text-sm text-slate-500';
    description.textContent = item.dateLabel + (item.description ? ' · ' + item.description : '');
    content.append(title, description);
    article.append(date, content);
    return article;
};
const renderCalendar = (data, direction) => {
    document.querySelector('#calendar-month-title').textContent = data.monthLabel;
    document.querySelector('#calendar-agenda-title').textContent = 'Agenda ' + data.monthLabel;
    const previous = document.querySelector('#calendar-previous');
    const next = document.querySelector('#calendar-next');
    previous.href = data.previousUrl; next.href = data.nextUrl;
    calendarPanel.dataset.previousUrl = data.previousUrl;
    calendarPanel.dataset.nextUrl = data.nextUrl;
    const grid = document.querySelector('#calendar-days');
    grid.replaceChildren(...data.days.map(createCalendarDay));
    const agenda = document.querySelector('#calendar-agenda-list');
    if (data.items.length) agenda.replaceChildren(...data.items.map(createAgendaItem));
    else {
        const empty = document.createElement('div');
        empty.className = 'empty-state';
        empty.textContent = 'Tidak ada agenda pada bulan ini.';
        agenda.replaceChildren(empty);
    }
    calendarPanel.classList.remove('calendar-slide-left', 'calendar-slide-right');
    void calendarPanel.offsetWidth;
    calendarPanel.classList.add(direction === 'previous' ? 'calendar-slide-right' : 'calendar-slide-left');
};
const loadCalendar = async (url, direction, push = true) => {
    if (!calendarPanel || calendarPanel.dataset.loading === '1') return;
    calendarPanel.dataset.loading = '1';
    calendarPanel.classList.add('calendar-loading');
    try {
        const response = await fetch(url, { headers: { Accept: 'application/json' } });
        if (!response.ok) throw new Error('Calendar request failed');
        const data = await response.json();
        renderCalendar(data, direction);
        if (push) history.pushState({ calendar: true }, '', data.currentUrl);
    } catch {
        window.location.href = url;
    } finally {
        calendarPanel.dataset.loading = '0';
        calendarPanel.classList.remove('calendar-loading');
    }
};
document.querySelectorAll('[data-calendar-nav]').forEach((link) => link.addEventListener('click', (event) => {
    event.preventDefault();
    loadCalendar(link.href, link.dataset.calendarNav);
}));
if (calendarPanel) {
    let startX = 0;
    let startY = 0;
    calendarPanel.addEventListener('touchstart', (event) => {
        startX = event.touches[0].clientX; startY = event.touches[0].clientY;
    }, { passive: true });
    calendarPanel.addEventListener('touchend', (event) => {
        const deltaX = event.changedTouches[0].clientX - startX;
        const deltaY = event.changedTouches[0].clientY - startY;
        if (Math.abs(deltaX) < 70 || Math.abs(deltaX) < Math.abs(deltaY) * 1.5) return;
        loadCalendar(deltaX < 0 ? calendarPanel.dataset.nextUrl : calendarPanel.dataset.previousUrl, deltaX < 0 ? 'next' : 'previous');
    }, { passive: true });
    window.addEventListener('popstate', () => loadCalendar(window.location.href, 'previous', false));
}
