# Fix Admin Create User Password

## TL;DR
> **Summary**: Fix admin-created siswa/guru failing when password is left blank by always writing a non-null password hash while keeping the account `unregistered`.
> **Deliverables**: controller fix for student + teacher create flows; focused Pest coverage; security regression that unregistered users cannot access dashboards.
> **Effort**: Quick
> **Parallel**: YES - 1 implementation wave + final review wave
> **Critical Path**: Controller fallback password fix → Pest coverage → focused verification

## Context
### Original Request
After `migrate:fresh`, admin creating a new student without filling optional password fails with `SQLSTATE[HY000]: Field 'password' doesn't have a default value` from `Admin\StudentController@store`.

### Interview Summary
- User confirmed password should not need to be filled.
- Admin-created siswa/guru must remain `status=unregistered` and complete `/daftar` before login.
- Do not make the database password column nullable/defaulted.

### Metis / Oracle Review
- Metis flagged known fallback password risk; plan must prove unregistered users cannot reach dashboards.
- Oracle verdict: GO. Fix controller writes, preserve DB constraint, add focused tests and security regression.

## Work Objectives
### Core Objective
Make admin create siswa/guru succeed when password is blank while preserving the registration activation workflow.

### Deliverables
- `Admin\StudentController@store` always writes `password` and `status='unregistered'`.
- `Admin\TeacherController@store` always writes `password` and `status='unregistered'`.
- Pest feature tests for blank-password and manual-password creation paths.
- Regression test proving unregistered account cannot access role dashboard before `/daftar`.

### Definition of Done
- `php artisan test --compact --filter=Student` passes.
- `php artisan test --compact --filter=Teacher` passes.
- `php artisan test --compact --filter=unregistered` or equivalent focused auth filter passes.
- `vendor/bin/pint --dirty --format agent` passes/fixes formatting.

### Must Have
- Blank password creates a non-null hash using fallback literal `password`, matching import behavior.
- Manual password is hashed if supplied, but status still remains `unregistered`.
- Existing profile creation stays inside the existing DB transaction.

### Must NOT Have
- Do not change migrations or make `users.password` nullable.
- Do not mark admin-created siswa/guru as `registered`.
- Do not change `/daftar` flow.
- Do not touch attendance/selfie, AGENTS, `boost.json`, or `session-ses_*.md` changes.

## Verification Strategy
> ZERO HUMAN INTERVENTION - all verification is agent-executed.
- Test decision: tests-after with Pest 4.
- QA policy: Every task has agent-executed scenarios.
- Evidence: `.omo/evidence/task-{N}-{slug}.txt`

## Execution Strategy
### Parallel Execution Waves
Wave 1: Task 1 (controller fix) and Task 2 (tests) can be done by one Laravel agent because tests need exact implementation names.
Wave 2: Task 3 verification.

### Dependency Matrix
| Task | Depends On | Blocks |
|------|------------|--------|
| 1 | none | 2, 3 |
| 2 | 1 | 3 |
| 3 | 1, 2 | final |

### Agent Dispatch Summary
Wave 1 → 2 tasks → `quick` with `laravel-best-practices`, `pest-testing`
Wave 2 → 1 task → `quick` with `pest-testing`

## TODOs

- [x] 1. Fix admin student/teacher create password fallback

  **What to do**:
  - Edit `app/Http/Controllers/Admin/StudentController.php`.
  - Start from `$validated = $request->validated();` or keep request access only if matching existing local style; do not use `$request->all()`.
  - In `store()`, build `$data` with:
    - `name` from validated/request value
    - `email` from validated/request value
    - `role` = `student`
    - `status` = `unregistered`
    - `password` = `Hash::make($request->filled('password') ? $request->password : 'password')`
  - Edit `app/Http/Controllers/Admin/TeacherController.php` similarly with `role` = `teacher`.
  - Keep existing transaction and profile creation unchanged.

  **Must NOT do**:
  - Do not change validation to make password required.
  - Do not change database migrations.
  - Do not mark users registered.

  **Recommended Agent Profile**:
  - Category: `quick` - two small controller edits.
  - Skills: `laravel-best-practices` - controller/auth consistency.
  - Omitted: `tailwindcss-development` - no UI changes.

  **Parallelization**: Can Parallel: NO | Wave 1 | Blocks: 2, 3 | Blocked By: none

  **References**:
  - Pattern: `app/Imports/StudentImport.php:71-76` - imported students get `status=unregistered` and hashed fallback password.
  - Pattern: `app/Imports/TeacherImport.php:68-73` - imported teachers use the same approach.
  - Bug: `app/Http/Controllers/Admin/StudentController.php:83-93` - password omitted when blank.
  - Bug: `app/Http/Controllers/Admin/TeacherController.php:88-98` - same issue for teachers.
  - Schema: `database/migrations/0001_01_01_000000_create_users_table.php:19` - `password` non-null.
  - Schema: `database/migrations/2026_05_24_064119_add_status_to_users_table.php:12` - default status unregistered.

  **Acceptance Criteria**:
  - [ ] POST `/admin/students` with no password no longer throws SQL error.
  - [ ] POST `/admin/teachers` with no password no longer throws SQL error.
  - [ ] Created users have `status='unregistered'` regardless of password input.

  **QA Scenarios**:
  ```
  Scenario: Blank-password student create data path
    Tool: Bash
    Steps: Run focused Pest test added in Task 2 for admin student create without password.
    Expected: Test asserts user exists, status unregistered, password hash checks against 'password', profile exists.
    Evidence: .omo/evidence/task-1-student-password-fallback.txt

  Scenario: Blank-password teacher create data path
    Tool: Bash
    Steps: Run focused Pest test added in Task 2 for admin teacher create without password.
    Expected: Test asserts user exists, status unregistered, password hash checks against 'password', profile exists.
    Evidence: .omo/evidence/task-1-teacher-password-fallback.txt
  ```

  **Commit**: YES | Message: `fix: add fallback password for admin-created users` | Files: `app/Http/Controllers/Admin/StudentController.php`, `app/Http/Controllers/Admin/TeacherController.php`

- [x] 2. Add Pest regression tests for admin-created users

  **What to do**:
  - Create or update feature tests under `tests/Feature` using Pest.
  - Use factories to create a registered admin user.
  - Test `POST /admin/students` without `password` succeeds and creates:
    - `users.role = student`
    - `users.status = unregistered`
    - non-empty `password`
    - `Hash::check('password', $user->password) === true`
    - related `student_profiles` row with submitted NISN/NIS.
  - Test `POST /admin/teachers` without `password` succeeds with equivalent assertions.
  - Test manual password path for at least one student and one teacher or a parameterized dataset:
    - Submitted password `Secret123!` hashes correctly.
    - `status` remains `unregistered`.
  - Add security regression:
    - Create unregistered student/teacher with known password and email.
    - Attempt to access `siswa.dashboard` or `guru.dashboard` while authenticated as that user, or login then request dashboard.
    - Assert registered middleware blocks/logs out/redirects to login with error.

  **Must NOT do**:
  - Do not make tests depend on existing database rows.
  - Do not delete starter tests without approval.

  **Recommended Agent Profile**:
  - Category: `quick` - focused feature tests.
  - Skills: `pest-testing`, `laravel-best-practices` - Pest + auth/controller behavior.
  - Omitted: `tailwindcss-development` - no UI assertions.

  **Parallelization**: Can Parallel: NO | Wave 1 | Blocks: 3 | Blocked By: 1

  **References**:
  - Test bootstrap: `tests/Pest.php` - Feature tests bind Laravel `TestCase`; `RefreshDatabase` currently commented.
  - Admin seeder/factory: `database/seeders/DatabaseSeeder.php`, `database/factories/UserFactory.php` - use existing admin state if available.
  - Middleware: `app/Http/Middleware/EnsureUserIsRegistered.php:13-17` - expected block for unregistered users.
  - Routes: `routes/web.php` - `admin.students.store`, `admin.teachers.store`, `siswa.dashboard`, `guru.dashboard`.

  **Acceptance Criteria**:
  - [ ] Test covers blank student password.
  - [ ] Test covers blank teacher password.
  - [ ] Test covers manual password stays unregistered.
  - [ ] Test covers unregistered user cannot access dashboard.

  **QA Scenarios**:
  ```
  Scenario: Feature tests prove password fallback
    Tool: Bash
    Steps: php artisan test --compact --filter=AdminCreateUserPassword
    Expected: All password fallback/manual password assertions pass.
    Evidence: .omo/evidence/task-2-password-tests.txt

  Scenario: Security regression blocks unregistered dashboards
    Tool: Bash
    Steps: php artisan test --compact --filter=Unregistered
    Expected: Unregistered student/teacher dashboard access redirects/logs out through registered middleware.
    Evidence: .omo/evidence/task-2-unregistered-security.txt
  ```

  **Commit**: YES | Message: `test: cover admin user password fallback` | Files: `tests/Feature/*`

- [x] 3. Run verification and formatting

  **What to do**:
  - Run focused tests:
    - `php artisan test --compact --filter=Student`
    - `php artisan test --compact --filter=Teacher`
    - `php artisan test --compact --filter=Unregistered`
    - or a narrower exact test class/filter if tests are named differently.
  - Run `vendor/bin/pint --dirty --format agent`.
  - Capture output to `.omo/evidence/` files if the executor uses evidence logging.

  **Must NOT do**:
  - Do not run `migrate:fresh` unless needed by tests.
  - Do not stage unrelated files (`AGENTS.md`, attendance/selfie worktree, `boost.json`, `session-ses_*.md`).

  **Recommended Agent Profile**:
  - Category: `quick` - command verification.
  - Skills: `pest-testing` - test command interpretation.
  - Omitted: `laravel-best-practices` - implementation already done.

  **Parallelization**: Can Parallel: NO | Wave 2 | Blocks: final | Blocked By: 1, 2

  **References**:
  - Commands: `AGENTS.md` root command list.
  - Pint rule: existing Laravel Boost guidelines require Pint after PHP edits.

  **Acceptance Criteria**:
  - [ ] Focused tests pass.
  - [ ] Pint passes/fixes dirty PHP files.
  - [ ] `git status --short` reviewed to ensure no unrelated files are included in the fix commit(s).

  **QA Scenarios**:
  ```
  Scenario: Focused automated verification
    Tool: Bash
    Steps: Run focused Pest filters and Pint.
    Expected: Commands exit 0 or Pint reports fixed files only.
    Evidence: .omo/evidence/task-3-verification.txt

  Scenario: Worktree hygiene
    Tool: Bash
    Steps: git status --short
    Expected: Only intended controller/test files are staged for this fix; unrelated AGENTS/attendance/session files remain unstaged unless user separately approves.
    Evidence: .omo/evidence/task-3-git-status.txt
  ```

  **Commit**: YES | Message: `fix: allow admin create users without password` | Files: controller + test files only

## Final Verification Wave
> 4 review agents run in PARALLEL. ALL must APPROVE. Present consolidated results to user and get explicit "okay" before completing.
> **Do NOT auto-proceed after verification. Wait for user's explicit approval before marking work complete.**
- [ ] F1. Plan Compliance Audit — oracle
- [ ] F2. Code Quality Review — unspecified-high
- [ ] F3. Real Manual QA — unspecified-high
- [ ] F4. Scope Fidelity Check — deep

## Commit Strategy
- Prefer one focused commit if implementation + tests are small and inseparable.
- Do not include unrelated current worktree files: `AGENTS.md`, `app/AGENTS.md`, `resources/views/AGENTS.md`, `database/AGENTS.md`, attendance/selfie changes, `boost.json`, or `session-ses_*.md`.

## Success Criteria
- Admin can create siswa/guru with password field omitted.
- Created siswa/guru remain `unregistered` and must complete `/daftar`.
- Known fallback password does not grant dashboard access before registration.
- Tests and Pint pass.
