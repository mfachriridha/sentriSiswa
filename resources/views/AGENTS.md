# BLADE UI KNOWLEDGE

## OVERVIEW
Blade + Tailwind v4 UI using shared layouts, partials, components, Indonesian copy, and role-aware sidebar navigation.

## STRUCTURE
```
resources/views/
├── layouts/      # app/guest shells
├── partials/     # sidebar/header/footer/head/scripts
├── components/   # reusable alert/table/modal/form widgets
├── admin/        # admin CRUD/settings screens
├── siswa/        # student dashboard/profile/attendance
├── guru/         # teacher placeholder dashboard
└── auth/         # login/register steps
```

## WHERE TO LOOK
| Task | Location | Notes |
|------|----------|-------|
| Main authenticated layout | `layouts/app.blade.php` | Fixed left sidebar, main content `ml-64`. |
| Guest auth layout | `layouts/guest.blade.php` | Login/register pages. |
| Navigation | `partials/sidebar.blade.php` | Role sections and active route classes. |
| Alpine scripts | `partials/scripts.blade.php` + `@push('scripts')` | Alpine loaded via CDN. |
| Reusable UI | `components/*.blade.php` | Alerts, modals, pagination, sort/search forms. |
| Student attendance UI | `siswa/absensi/*` | Camera/selfie and history table in progress. |

## CONVENTIONS
- Use Indonesian labels, buttons, validation messages, and section names.
- Use `route()` for links and `request()->routeIs()` for active sidebar states.
- For route groups with base + children, match both: `routeIs('siswa.profil', 'siswa.profil.*')`.
- Admin list pages use table + search/filter/sort components; preserve visual rhythm.
- Buttons use `bg-primary` / `hover:bg-primary-dark`; theme colors live in `resources/css/app.css`.
- Keep profile menu under `Akun`; for siswa, `Profil` should stay last.

## ANTI-PATTERNS
- Never nest forms; keep upload/delete forms outside edit/update forms.
- Do not add dark-mode classes; app is light-mode only for now.
- Do not hardcode URLs; use named routes.
- Do not assume Vite manifest exists in dev; if assets fail, run/build Vite.
- Do not edit compiled views under `storage/framework/views`; edit `resources/views/**` source files.

## CURRENT GOTCHAS
- Attendance selfie uses browser `getUserMedia` + Alpine; manual browser QA is required for camera behavior.
- `resources/views/student/*` and `resources/views/teacher/*` appear legacy/unused compared with Indonesian `siswa/guru` routes.
