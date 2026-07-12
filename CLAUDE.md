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
Controllers are namespaced by role. `Guru/` holds shared base controllers reused by other teacher roles via thin subclasses (role-scoped reuse, not translation wrappers):

```
app/Http/Controllers/
├── Admin/          # Admin CRUD: siswa, guru, kelas, pengaturan, OTP auth
│   └── Auth/       # Admin login
├── Auth/           # Guest flows: register, forgot password, Google OAuth, OTP verify
├── Guru/           # Shared base layer reused across teacher roles
│   ├── DashboardController       # Branches internally per role
│   ├── ProfilController          # Shared profile CRUD with OTP for email/password change
│   ├── RekapAbsensiController    # Wali kelas rekap absensi
│   ├── ClassRosterController     # Wali kelas daily class view
│   ├── RiwayatPelanggaranController
│   ├── BkMonitoringController
│   ├── MonitoringController      # Kesiswaan school-wide
│   ├── PelanggaranSiswaController
│   ├── PengajuanPoinController   # Wali kelas submits point-addition requests
│   ├── PersetujuanPoinController # Kesiswaan approves/rejects them
│   ├── JenisPelanggaranController
│   ├── TataTertibController
│   └── LaporanPelanggaranController
├── WaliKelas/      # Thin subclasses → extend Guru/* counterparts
├── Bk/             # Thin subclasses + BkAbsensiRecapController (grade-scoped)
├── Kesiswaan/      # Thin subclasses
├── Siswa/          # Student dashboard, absensi, profile, TataTertibController
└── AbsensiPublikController  # Public token-based attendance check-in (no auth)
```

Admin's siswa/guru/kelas/import/pengaturan controllers used to be split into an English "base" class plus a one-line Indonesian subclass purely to get an Indonesian class name on routes (e.g. `StudentController` wrapped by `SiswaController`). Those pairs have been merged — `Admin/SiswaController`, `GuruController`, `KelasController`, `ImporSiswaController`, `ImporGuruController`, `BiodataSiswaController`, `PengaturanController` hold their logic directly now, no English base class exists. Route parameter names, route names, and URL paths are all Indonesian too (`{siswa}`, `{guru}`, `{kelas}`, `pengaturan.*`, `ekspor-excel`/`ekspor-pdf`) — the only English left is third-party/technical vocabulary (`FonnteService`, `GeofenceValidator`, `KmlParser`, `whatsapp`).

### Models
Models are Indonesian-named and map directly to Indonesian tables/columns — there is no separate English model layer or alias to worry about: `Pengguna` (table `pengguna`), `ProfilSiswa` (`profil_siswa`), `ProfilGuru` (`profil_guru`), `Kelas` (`kelas`), `Absensi` (`absensi`), `PelanggaranSiswa` (`pelanggaran_siswa`), `JenisPelanggaran` (`jenis_pelanggaran`), `TataTertib` (`tata_tertib`), `Pengaturan` (`pengaturan`), `PesanWhatsapp` (`pesan_whatsapp`), `TokenOtp` (`token_otp`).

### Photo Storage
Photos are stored in three places; the sidebar resolves them in order:
- Admin → `pengguna.foto` → `storage/photos/admin/`
- Teacher → `profil_guru.foto` → `storage/photos/teachers/`
- Student → `profil_siswa.foto` → `storage/photos/students/`
- Attendance selfie → `absensi.selfie_path` → `storage/attendance-selfies/{profile_id}/{date}-demo.jpg`

Always use `asset('storage/'.$path)` to generate URLs.

### Key Services
- `FonnteService` — WhatsApp send via Fonnte API; reads token from `Pengaturan::get('fonnte_token')`
- `OtpService` — generates 6-digit OTP, stores in `token_otp`, mails to `pending['new_email'] ?? $user->email`
- `AbsenceWarningService` — computes alpha count per student for current semester; threshold = 3
- `GeofenceValidator` — validates GPS check-in against KML-defined geofence polygon

### Image Generation
Intervention Image v3 is installed but its `create()` method does not exist; only `createImage()` exists and requires a TTF font path for text. **Use native GD** (`imagecreatetruecolor`, `imagejpeg`, `imagedestroy`) for any image generation — see `DemoSchoolSeeder` for the pattern.

### Exports
- `ArrayExport` — generic `FromArray` export, accepts headers + rows
- `RekapAbsensiExport` — per-class attendance summary
- `TemplateSiswaExport` / `TemplateGuruExport` — import template downloads
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

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `pnpm run build`, `pnpm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `pnpm run build` or ask the user to run `pnpm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

</laravel-boost-guidelines>
