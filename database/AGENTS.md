# DATABASE KNOWLEDGE

## OVERVIEW
MySQL schema for role-based school management: `users` auth, profile tables, class assignments, settings, biodata, and attendance.

## WHERE TO LOOK
| Task | Location | Notes |
|------|----------|-------|
| Auth/users | `0001_01_01_000000_create_users_table.php` + role/status migrations | Email nullable; roles added later. |
| Teacher profile | `create_teacher_profiles_table.php` + grade/student_affairs migrations | Subroles live here. |
| Class data | `create_classes_table.php` | `student_count` is stale/legacy. |
| Student profile | `create_student_profiles_table.php` + photo/gender fix migrations | Gender removed from profile; biodata has gender. |
| Student biodata | `create_student_biodata_table.php` | Table name is singular `student_biodata`. |
| Settings | `create_settings_table.php` | Key/value config cached by model. |
| Attendance | `create_attendances_table.php` + selfie migration | Unique per student/date. |
| Seeds | `seeders/DatabaseSeeder.php`, `DemoStudentBiodataSeeder.php` | Admin seeded registered. |

## CONVENTIONS
- Use migrations for schema changes; run with `php artisan migrate --no-interaction`.
- Prefer foreign keys with cascade delete for profile-owned detail tables.
- Student attendance statuses: `hadir`, `terlambat`, `izin`, `sakit`, `alpha`.
- Imported students/teachers are represented as `users` + profile rows.
- Admin seed user: `admin@sentrisiswa.test`, status `registered`.

## ANTI-PATTERNS
- Do not rely on resetting auto-increment IDs after deletes; MySQL will not reset automatically.
- Do not write code against `classes.student_count`; calculate via `student_profiles.class_id`.
- Do not add violation FK/schema fragments until full violation feature is built.
- Do not assume SQLite behavior; active engine is MySQL.

## CURRENT GOTCHAS
- `attendances.selfie_path` exists in DB/current worktree; check migration applied state before modifying attendance pages.
- `student_biodata` singular table requires explicit model `$table`.
