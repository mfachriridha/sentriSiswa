# APP LAYER KNOWLEDGE

## OVERVIEW
Laravel domain layer: role-aware controllers, form requests, imports, middleware, and Eloquent models for admin/guru/siswa workflows.

## WHERE TO LOOK
| Task | Location | Notes |
|------|----------|-------|
| Role logic | `Models/User.php` | Central helpers: admin/teacher/student/subroles. |
| Profile data | `Models/StudentProfile.php`, `Models/TeacherProfile.php` | Student/teacher details split from `users`. |
| Admin CRUD | `Http/Controllers/Admin/*Controller.php` | Classes, students, teachers, settings, imports. |
| Student area | `Http/Controllers/Siswa/*Controller.php` | Profile and attendance pages. |
| Registration | `Http/Controllers/Auth/RegisterController.php` | 2-step identity verification before account completion. |
| Validation | `Http/Requests/**` | Prefer request classes for CRUD/auth forms. |
| Excel imports | `Imports/StudentImport.php`, `Imports/TeacherImport.php` | Chunked imports with identity-based upserts. |

## CONVENTIONS
- New controllers should return `View`/`RedirectResponse` explicitly.
- Use `Auth::user()` only in role-scoped controllers; rely on middleware aliases from `bootstrap/app.php`.
- Keep profile data in profile tables, not `users`, except auth fields (`name`, `email`, `password`, `role`, `status`).
- Imported users start `unregistered`; registration updates credentials/status.
- `Setting::get()` caches values for one day; use `Setting::set()` so cache is cleared.
- For class student counts, use the `students()` relation, never the `student_count` column.

## ANTI-PATTERNS
- Do not create broad services/repositories unless reused; current app uses direct Laravel controllers + models.
- Do not infer new user IDs after bulk insert in new imports without checking existing import behavior; current imports use offset math.
- Do not add `StudentViolation` code paths until violation tables/models are added.
- Do not bypass request validation for admin/student forms.

## CURRENT GOTCHAS
- `Attendance` includes `selfie_path` in the working tree; confirm migration status before editing attendance storage.
- `StudentProfile::points` is a temporary hardcoded accessor returning `100`.
