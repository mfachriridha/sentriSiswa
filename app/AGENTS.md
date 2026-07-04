# APP LAYER KNOWLEDGE

## OVERVIEW
Laravel domain layer: role-aware controllers, form requests, imports, middleware, and Eloquent models for admin/guru/siswa workflows. Everything is Indonesian-named (models, controllers, FormRequests, routes) except third-party/technical vocabulary (`FonnteService`, `GeofenceValidator`, `KmlParser`).

## WHERE TO LOOK
| Task | Location | Notes |
|------|----------|-------|
| Role logic | `Models/Pengguna.php` | Central helpers: `isAdmin()`/`isGuru()`/`isSiswa()`/sub-roles. |
| Profile data | `Models/ProfilSiswa.php`, `Models/ProfilGuru.php` | Student/teacher details split from `pengguna`. |
| Admin CRUD | `Http/Controllers/Admin/*Controller.php` | Kelas, siswa, guru, pengaturan, imports. |
| Student area | `Http/Controllers/Siswa/*Controller.php` | Profile and attendance pages. |
| Registration | `Http/Controllers/Auth/RegisterController.php` | 2-step identity verification before account completion. |
| Validation | `Http/Requests/**` | Prefer request classes for CRUD/auth forms. |
| Excel imports | `Imports/ImporSiswa.php`, `Imports/ImporGuru.php` | Chunked imports with identity-based upserts. |

## CONVENTIONS
- New controllers should return `View`/`RedirectResponse` explicitly.
- Use `Auth::user()` only in role-scoped controllers; rely on middleware aliases from `bootstrap/app.php`.
- Keep profile data in profile tables, not `pengguna`, except auth fields (`nama`, `email`, `password`, `peran`, `status`).
- Imported users start `unregistered`; registration updates credentials/status.
- `Pengaturan::get()` caches values for one day; use `Pengaturan::set()` so cache is cleared.
- For class student counts, use the `siswa()` relation, never a cached count column.

## ANTI-PATTERNS
- Do not create broad services/repositories unless reused; current app uses direct Laravel controllers + models.
- Do not infer new user IDs after bulk insert in new imports without checking existing import behavior; current imports use offset math.
- Do not bypass request validation for admin/student forms.
- Do not reintroduce an English "base class + Indonesian wrapper" pair for a new admin/domain controller — put the logic directly in the Indonesian-named class.

## CURRENT GOTCHAS
- `Absensi` includes `selfie_path`; confirm migration status before editing attendance storage.
- `ProfilSiswa::getPoinAttribute()` computes real points from approved violations/point-additions, clamped to `[0, 100]` — not a hardcoded value.
