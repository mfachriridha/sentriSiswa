# Draft: Fix Admin Create User Password

## Requirements (confirmed)
- User ran `migrate:fresh`, then creating a new student from `/admin/students/create` fails.
- Error: `users.password` has no default value during insert from `Admin\StudentController@store`.
- Need investigation and plan before fixing.

## Research Findings
- `app/Http/Controllers/Admin/StudentController.php:83-93`: only sets `password` if optional password field is filled.
- `app/Http/Requests/Student/StoreStudentRequest.php:19`: `password` is nullable.
- `database/migrations/0001_01_01_000000_create_users_table.php:19`: `password` is required and has no DB default.
- `database/migrations/2026_05_24_064119_add_status_to_users_table.php:12`: `users.status` defaults to `unregistered`.
- `app/Imports/StudentImport.php:71-76`: imported students get hashed default password `password` and `status=unregistered`.
- `app/Http/Controllers/Admin/TeacherController.php:88-98`: same optional-password insert pattern exists for teachers.
- `app/Imports/TeacherImport.php:68-73`: imported teachers also get hashed default password `password` and `status=unregistered`.

## Technical Decisions
- Admin-created teacher/student with empty password must mimic import behavior: set hashed default password `password` and keep `status=unregistered`.
- If admin provides password manually, still keep `status=unregistered`; user must complete `/daftar` before login.
- Do not make `users.password` nullable/default at database level; keep DB constraint and ensure app always writes a hash.

## Open Questions
- None.

## Scope Boundaries
- INCLUDE: admin student create fix, likely admin teacher create fix, regression coverage/QA.
- EXCLUDE: changing `users.password` DB nullable/default, changing import flow, changing registration UX unless explicitly requested.
