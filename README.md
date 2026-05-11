# SentriSiswa

Sistem presensi siswa SMA berbasis web dengan verifikasi selfie, geofencing, dan notifikasi WhatsApp.

## Tech Stack

- **Framework:** Laravel 13
- **PHP:** 8.4
- **Frontend:** Tailwind CSS 4, Bootstrap Icons
- **Font:** Inter
- **Database:** MySQL

## Mulai

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed --seeder=DummySeeder
php artisan serve
```

## Login

Semua login melalui `/auth/login`.

| Role | Email | Password |
|---|---|---|
| Admin | `admin@sentrisiswa.sch.id` | `admin123` |
| Wali Kelas 1 | `ahmad.dahlan@sentrisiswa.sch.id` | `password123` |
| Wali Kelas 2 | `siti.aminah@sentrisiswa.sch.id` | `password123` |
| BK | `rudi.hartono@sentrisiswa.sch.id` | `password123` |
| Siswa | `andi.pratama@sentrisiswa.sch.id` | `password123` |

## Registrasi

Di `/auth/register` — multi-step: validasi NIP (guru) atau NIS+NISN (siswa) → lengkapi email+password.

**Data belum terdaftar (demo):**

| Role | Identifier | Nama |
|---|---|---|
| Guru | NIP `196610162000121001` | Heru Suyana, S.Pd |
| Guru | NIP `199211262025212077` | Ayu Dewi Lestari, S.Pd |
| Siswa | NIS `3001` / NISN `0003001` | Eka Putra |
| Siswa | NIS `3002` / NISN `0003002` | Fitriani |

NIP wajib 18 digit tanpa spasi.

---

## Status Progress

### Selesai

#### Auth & Middleware
- Unified login `/auth/login` — email + password, redirect otomatis sesuai role
- Role middleware: `admin`, `siswa`, `wali_kelas`, `bk`
- Registrasi multi-step: validasi NIP/NIS → lengkapi email+password
- `is_active` flag — guru/siswa harus registrasi dulu sebelum login

#### Admin Panel
- **Dashboard** — total siswa, total kelas, presensi hari ini
- **Manajemen Guru** — CRUD via modal + class assignment (wali kelas: 1 kelas, BK: multi kelas) + CSV import/preview + template download
- **Manajemen Siswa** — CRUD + detail modal (JIRA-style: biodata, orang tua) + CSV import/preview + template download
- **Manajemen Kelas** — CRUD + lihat/tambah/kurangi siswa per kelas + filter (prefix tingkat) + sort (No., Nama, Jumlah, Terbaru) + hapus kelas kosong
- **Tata Tertib** — upload/replace/delete PDF, preview iframe, badge status
- **Poin Pelanggaran** — form tambah pelanggaran → auto-decrement poin siswa + riwayat
- **Profil** — edit nama/email/password
- **Semua tabel** — pagination 25/50/100 + sort asc/desc + custom confirm modal + toast notifikasi

#### Siswa
- **Kehadiran** — kamera selfie + GPS geolocation + validasi polygon (ray-casting) + submit AJAX
- Dashboard, Riwayat, Poin, Biodata, Pengaturan (placeholder)

#### Wali Kelas
- **Dashboard** — daftar siswa di kelasnya + status presensi hari ini + konfirmasi + ubah status
- Loading state + toast di semua aksi

#### Database
- `users` (nip, is_active), `school_classes`, `students` (user_id, school_class_id), `student_parents`, `guardians`
- `attendances` (selfie_path, lat/lng, geofence flag, confirmed_by), `teacher_classes` (pivot)
- `violations`, `student_violations`
- 36 jenis pelanggaran (seeder)

#### UI/UX
- Tailwind CSS 4 + Inter font + navy `#001e40` palette
- Responsif mobile-first, sidebar collapsible
- Loading state di setiap tombol submit
- Custom confirm modal (bukan `confirm()` browser)
- Toast notifikasi sukses/gagal

---

### TODO

#### Prioritas Tinggi
- [ ] **WhatsApp Notification Job** — `SendAttendanceNotification` via wapisender.id API, kirim ke orang tua setelah wali kelas konfirmasi
- [ ] **Wali Kelas Reporting** — rekap mingguan/bulanan/semester/tahunan/custom date range, export CSV/PDF
- [ ] **BK Dashboard** — monitoring kelas yang di-assign, rekap presensi, filter & sort
- [ ] **Admin assign kelas ke BK** — dari halaman guru, pilih guru BK → assign multi kelas
- [ ] **Selfie preview** — di wali kelas dashboard, tampil thumbnail selfie sebelum konfirmasi
- [ ] **Riwayat Kehadiran (Siswa)** — tampil data dari `attendances`, bukan placeholder
- [ ] **Poin Saya (Siswa)** — tampil data dari `student_violations`

#### Prioritas Menengah
- [ ] **Landing page** — integrasi PDF viewer dengan real PDF dari storage
- [ ] **Presensi pulang** — siswa presensi pulang (wajib/tidak)
- [ ] **Status kehadiran auto** — otomatis "alfa" kalau tidak presensi sebelum jam tertentu
- [ ] **Filter & search** — search bar di semua tabel (nama, NIS, NIP)
- [ ] **Export** — download CSV/PDF untuk rekap presensi
- [ ] **Profile siswa** — edit data diri (bukan placeholder)

#### Prioritas Rendah
- [ ] **Integrasi API WhatsApp** — halaman integrasi: simpan API key, test koneksi
- [ ] **Notifikasi mingguan** — auto-kirim rekap poin ke orang tua
- [ ] **Log aktivitas** — audit trail untuk admin actions
- [ ] **Dashboard charts** — grafik kehadiran, poin, distribusi kelas
- [ ] **Scheduler** — cron job untuk auto-alfa, auto-notifikasi mingguan
