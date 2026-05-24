## Findings

- `app/Http/Controllers/Admin/StudentController.php` and `app/Http/Controllers/Admin/TeacherController.php` now always set `password` during create using `Hash::make($request->filled('password') ? $request->password : 'password')`.
- Both create flows keep `status` fixed at `unregistered` and leave the existing DB transaction/profile creation logic unchanged.
- `php artisan route:list --path=admin` still shows the same admin routes for students and teachers.
- Added `tests/Feature/AdminCreateUserPasswordTest.php` covering student fallback password, teacher fallback password, student manual password, and `registered` middleware redirect/logout behavior.
- Verification passed with `php artisan test --compact --filter=AdminCreateUser` on `DB_CONNECTION=mysql` / `DB_DATABASE=sentrisiswav2_test`.
- 2026-05-24: Restored both enum migration files to raw MySQL `DB::statement(...)` form and expanded Pest coverage to include teacher manual-password creation plus role-specific dashboard blocking for unregistered student and teacher users.
