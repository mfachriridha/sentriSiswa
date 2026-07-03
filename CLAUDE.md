# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Full dev stack (server + queue + vite, all at once)
composer dev

# Build frontend assets
pnpm run build
pnpm run dev

# Run all tests
composer test

# Run a single test file
php artisan test tests/Feature/OtpProfileChangeTest.php

# Run a single Pest test by name
php artisan test --filter="test name here"

# Lint / format PHP
./vendor/bin/pint

# Fresh seed (wipes DB, seeds demo data with fake photos)
php artisan migrate:fresh --seed

# Clear compiled views / config
php artisan view:clear
php artisan config:clear
```

Always use `pnpm` (not `npm`) for JS packages.

## Architecture

### Stack
- Laravel 13.8 / PHP 8.3+, MPA (no API — all routes return HTML/redirects)
- Tailwind CSS v4, DaisyUI v5, Alpine.js (loaded via CDN in `partials/scripts.blade.php`)
- Vite, Pest (tests), DomPDF (PDF export), Maatwebsite Excel (XLSX), Intervention Image v3 (raw GD only — see note below)
- WhatsApp via **Fonnte API** (`app/Services/FonnteService.php`), jobs queued via `SendWhatsAppNotification`
- Google OAuth via Laravel Socialite

### Roles & Middleware Aliases
Five roles, each with a dedicated middleware alias registered in `bootstrap/app.php`:

| Alias | Middleware | Role |
|-------|-----------|------|
| `admin` | `EnsureUserIsAdmin` | School administrator |
| `guru` | `EnsureUserIsGuru` | All teacher roles combined |
| `wali-kelas` | `EnsureUserIsHomeroom` | Homeroom teacher |
| `bk` | `EnsureUserIsCounselor` | Counselor (BK) |
| `kesiswaan` | `EnsureUserIsStudentAffairs` | Student affairs |
| `siswa` | `EnsureUserIsSiswa` | Student |

`isGuru()` returns true for all three teacher sub-roles. `EnsureUserIsGuru` is applied at the parent group level; sub-role guards (`wali-kelas`, `bk`, `kesiswaan`) are applied inside nested groups in `routes/web.php`.

### Controller Layout
Controllers are namespaced by role. Most teacher controllers are thin wrappers around `Guru/` base classes:

```
app/Http/Controllers/
├── Admin/          # Admin CRUD: students, teachers, classes, settings, OTP auth
│   └── Auth/       # Admin login
├── Auth/           # Guest flows: register, forgot password, Google OAuth, OTP verify
├── Guru/           # Abstract base layer for all teacher roles
│   ├── DashboardController       # Branches internally per role
│   ├── ProfilController          # Shared profile CRUD with OTP for email/password change
│   ├── AttendanceRecapController # Wali kelas rekap absensi
│   ├── ClassRosterController     # Wali kelas daily class view
│   ├── ViolationHistoryController
│   ├── BkMonitoringController
│   ├── MonitoringController      # Kesiswaan school-wide
│   ├── StudentViolationController
│   ├── PengajuanPoinController   # Wali kelas submits point-addition requests
│   ├── PersetujuanPoinController # Kesiswaan approves/rejects them
│   ├── ViolationTypeController
│   ├── SchoolRuleController
│   └── ViolationReportController
├── WaliKelas/      # Thin subclasses → extend Guru/* counterparts
├── Bk/             # Thin subclasses + BkAbsensiRecapController (grade-scoped)
├── Kesiswaan/      # Thin subclasses
├── Siswa/          # Student dashboard, absensi, profile, school rules
└── AbsensiPublikController  # Public token-based attendance check-in (no auth)
```

### Model Alias Pattern
Indonesian-named models are thin aliases pointing to the same DB table as their English parent. Never add logic to the alias — put it in the parent:

```
Guru → TeacherProfile (table: teacher_profiles)
Siswa → StudentProfile (table: student_profiles)
Kelas → SchoolClass (table: classes)
Absensi → Attendance (table: attendances)
PelanggaranSiswa → StudentViolation (table: student_violations)
JenisPelanggaran → ViolationType (table: violation_types)
TataTertib → SchoolRule (table: school_rules)
Pengaturan → Setting (table: settings)
```

### Photo Storage
Photos are stored in three places; the sidebar resolves them in order:
- Admin → `users.photo` → `storage/photos/admin/`
- Teacher → `teacher_profiles.photo` → `storage/photos/teachers/`
- Student → `student_profiles.photo` → `storage/photos/students/`
- Attendance selfie → `attendances.selfie_path` → `storage/attendance-selfies/{profile_id}/{date}-demo.jpg`

Always use `asset('storage/'.$path)` to generate URLs.

### Key Services
- `FonnteService` — WhatsApp send via Fonnte API; reads token from `Setting::get('fonnte_token')`
- `OtpService` — generates 6-digit OTP, stores in `email_otp_tokens`, mails to `pending['new_email'] ?? $user->email`
- `AbsenceWarningService` — computes alpha count per student for current semester; threshold = 3
- `GeofenceValidator` — validates GPS check-in against KML-defined geofence polygon

### Image Generation
Intervention Image v3 is installed but its `create()` method does not exist; only `createImage()` exists and requires a TTF font path for text. **Use native GD** (`imagecreatetruecolor`, `imagejpeg`, `imagedestroy`) for any image generation — see `DemoSchoolSeeder` for the pattern.

### Exports
- `ArrayExport` — generic `FromArray` export, accepts headers + rows
- `AttendanceRecapExport` — per-class attendance summary
- `StudentTemplateExport` / `TeacherTemplateExport` — import template downloads
- PDF exports use `barryvdh/laravel-dompdf` via `PDF::loadView()`

### Blade Conventions
- App layout: `layouts/app.blade.php` — fixed left sidebar at `w-64`, main content `ml-64`
- Guest layout: `layouts/guest.blade.php`
- Alpine.js state: defined in `x-data` on container elements; scripts go in `@push('scripts')`
- All copy in **Indonesian** (labels, buttons, messages, section names)
- Route links use `route()`, active states use `request()->routeIs()`
- Primary color token: `bg-primary` / `hover:bg-primary-dark` (defined in `resources/css/app.css`)
- `resources/views/student/` and `resources/views/teacher/` are legacy — prefer `siswa/` and `guru/`

### Violation Workflow
`PelanggaranSiswa.status` FSM: `pending → approved | rejected` (the `pending`/`rejected` states are legacy — only old records can hold them now).
- Kesiswaan creates violations directly with `status = approved` (no submission/approval queue on this side anymore)
- Points: `ProfilSiswa::getPoinAttribute()` = 100 − SUM(approved `pengurangan_poin`) + SUM(approved `PengajuanPoin.jumlah_poin`), clamped to `[0, 100]`

### Point Addition Workflow (Pengajuan Poin)
`PengajuanPoin.status` FSM: `pending → approved | rejected`
- Wali Kelas submits a request for a student in their own homeroom class (`kelasWali->siswa`), with a reason only — no point amount
- Kesiswaan sets `jumlah_poin` (1-100) when approving; rejecting requires `alasan_penolakan`
- This replaced the old "BK submits violation" flow, which has been removed entirely

### TokenAksesAbsensi
Public attendance check-in uses a token stored in `token_akses_absensi`. Token is valid for the day it was created (end-of-day expiry). `TokenAksesAbsensi::buatAtauPerbarui()` upserts the record.
