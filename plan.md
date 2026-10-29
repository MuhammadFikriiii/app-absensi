# Rencana Aplikasi Absensi

## Tujuan

Aplikasi absensi internal untuk 30–40 karyawan dengan dua peran: `admin` dan `karyawan`. Aplikasi dibangun sebagai PWA responsif agar dapat dipasang langsung dari browser di Android dan iPhone tanpa Play Store atau App Store.

## Teknologi

- [x] Laravel 12
- [x] Blade + Tailwind CSS
- [x] SQLite untuk pengembangan lokal
- [ ] MySQL untuk produksi
- [x] PWA (Web App Manifest + Service Worker)
- [x] Zona waktu `Asia/Makassar`

## Phase 1 — Fondasi dan Absensi Dasar

Status: **Selesai untuk baseline lokal**

### Karyawan

- [x] Login dan logout.
- [x] Dashboard responsif yang menampilkan jadwal dan status hari ini.
- [x] Absen masuk satu kali per hari.
- [x] Absen keluar hanya setelah absen masuk.
- [x] Tombol mulai/selesai istirahat terpisah dari absen masuk/keluar.
- [x] Kalender sederhana: libur merah, izin hijau, ulang tahun biru.
- [x] Riwayat absensi pada halaman khusus, tidak diduplikasi di beranda.
- [x] Profil dengan nomor WhatsApp, jabatan, dan tanggal lahir.
- [x] PWA yang dapat dipasang pada Android dan iPhone.

### Admin minimum yang dibutuhkan Phase 1

- [x] Login dengan role admin.
- [x] Daftar dan pembuatan karyawan.
- [x] Pengaturan jadwal kerja mingguan.
- [x] Menyalin jadwal satu hari ke beberapa hari lain.
- [x] Daftar absensi harian.

### Kriteria selesai

- [x] Alur login → absen masuk → istirahat → selesai istirahat → absen keluar bekerja.
- [x] Data tersimpan di SQLite.
- [x] Validasi mencegah aksi ganda atau urutan yang salah.
- [x] Tampilan nyaman pada ponsel dan desktop.
- [x] Test fitur inti lulus.

## Phase 2 — Validasi Kehadiran dan Kolaborasi

Status: **Selesai untuk baseline lokal**

- [x] GPS/radius kantor yang dapat diaktifkan atau dinonaktifkan admin.
- [x] Validasi jaringan kampus berdasarkan IP atau CIDR.
- [x] Selfie masuk dan keluar melalui kamera perangkat.
- [x] Status karyawan lain dan tombol WhatsApp.
- [x] Pengajuan izin/cuti/sakit beserta persetujuan admin.
- [x] Pengajuan yang disetujui tampil pada kalender.
- [x] Tema terang dan gelap yang tersimpan pada perangkat.

## Phase 3 — Koreksi dan Rekap

- [ ] Pengajuan perubahan absensi.
- [ ] Persetujuan admin.
- [ ] Audit log.
- [ ] Rekap harian, mingguan, dan bulanan.

## Phase 4 — Lembur dan Perhitungan Uang

- [ ] Lembur otomatis berdasarkan ambang admin.
- [ ] Uang transport per jabatan dan override per karyawan.
- [ ] Potongan keterlambatan.
- [ ] Periode pembayaran dan tombol sudah dibayar.
- [ ] Migrasi produksi ke MySQL, backup, dan hardening.

## Catatan Instalasi PWA

- Android: buka aplikasi di Chrome lalu pilih **Install app** atau **Tambahkan ke layar utama**.
- iPhone/iPad: buka aplikasi di Safari, pilih **Share**, lalu **Add to Home Screen**.
- Produksi wajib menggunakan HTTPS agar pemasangan PWA, kamera, dan GPS dapat berfungsi.
