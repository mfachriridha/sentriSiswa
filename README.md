# SentriSiswa

Sistem presensi siswa SMA berbasis web dengan verifikasi selfie, geofencing, dan notifikasi WhatsApp.

## Fitur

### Siswa
- **Dashboard** — ringkasan kehadiran, poin, aksi cepat
- **Kehadiran** — selfie presensi + deteksi lokasi (geofencing polygon)
- **Riwayat Kehadiran** — rekap harian dengan status Hadir/Terlambat/Alpha
- **Poin Saya** — sisa poin pelanggaran (awal 100), riwayat pengurangan
- **Biodata** — data diri lengkap (rapor: NIS, NISN, alamat, orang tua, wali)
- **Profil** — edit akun, email, WhatsApp siswa & orang tua

### Wali Kelas
- Dashboard, Presensi Masuk/Pulang, verifikasi selfie + GPS siswa, simpan & kirim WA

### Guru Mapel
- Presensi kelas tanpa selfie, simpan tanpa notifikasi WA

### BK (Bimbingan Konseling)
- Monitoring semua kelas, rekap presensi, poin pelanggaran

### Kesiswaan (Super Admin)
- Master data guru & siswa (CRUD + upload CSV)
- Manajemen role & kelas
- Upload PDF tata tertib
- Poin pelanggaran

## Tech Stack

- **Framework:** Laravel 13
- **Frontend:** Tailwind CSS 4, Bootstrap Icons
- **Font:** Inter
- **Database:** MySQL

## Mulai

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

### Login

| Role | Email | Password |
|---|---|---|
| Admin | `admin@sentrisiswa.sch.id` | `admin123` |
| Siswa | `siswa@sentrisiswa.sch.id` | `siswa123` |

Semua login melalui `/auth/login`. Role otomatis terdeteksi dari akun.

## Status

Frontend dalam pengembangan. Seluruh halaman sudah berfungsi sebagai UI only.
