<laravel-boost-guidelines>

## Stack
- php 8.4, Laravel v13, Pest v4, Tailwind CSS v4, pnpm 11, Vite 8
- laravel/boost v2 (MCP tools), laravel/pint v1, laravel/pail v1

## Repo-specific gotchas
- **Package manager is pnpm**, not npm. `.npmrc` has `ignore-scripts=true` so postinstall scripts never run automatically.
- **Database is MySQL** (`sentrisiswav2` on 127.0.0.1:3306, root/no-password). Tests use `DB_CONNECTION=sqlite` + `DB_DATABASE=:memory:` (configured in `phpunit.xml`, not `.env`).
- **Tailwind v4**: uses `@import "tailwindcss"` and `@theme` blocks, not the old v3 `@tailwind` directives. Check `resources/css/app.css` for reference.
- **Bare scaffold**: only `routes/web.php` (single `/` route), only `app/Models/User.php`. No API routes, no custom controllers yet.
- **Vite** watches ignore `storage/framework/views/**` (see `vite.config.js`).

## Skills
Available in `.github/skills/` and `.agents/skills/`:
- `laravel-best-practices` (19 rule files) — activate for any backend PHP work
- `pest-testing` — activate for any test creation, editing, or fixing
- `tailwindcss-development` — activate for any Blade/CSS styling

Activate the relevant skill BEFORE starting work, don't wait until stuck.

## Commands
- `composer run dev` — starts server + queue:listen + vite concurrently
- `composer run setup` — full first-time setup (install, env, key:generate, migrate, build)
- `composer run test` — runs `config:clear` then `php artisan test`
- `php artisan test --compact --filter=testName` — run a single test
- `vendor/bin/pint --dirty --format agent` — format only changed PHP files (run after any PHP edit)

## Laravel Boost MCP tools (always prefer these)
- `database-query` — read-only SQL (never write raw SQL in tinker)
- `database-schema` — inspect tables before writing migrations/models
- `get-absolute-url` — resolve correct URL scheme/domain/port
- `browser-logs` — read recent browser errors
- `search-docs` — version-specific docs for installed packages (use BEFORE writing code)
- `read-log-entries` — read application log (PSR-3 / JSON format)

## Testing
- Create tests: `php artisan make:test --pest SomeTest` (no `Feature/` prefix)
- Use factories for models in tests; check for custom factory states before manual setup
- `$this->faker->word()` or `fake()->randomDigit()` — follow existing convention in the test file

### Browser testing (Pest + Playwright)
- Plugin already installed: `pestphp/pest-plugin-browser` + `playwright` npm dep
- Configure Firefox as default in `tests/Pest.php`: `pest()->browser()->inFirefox();`
- Or override per-run: `php artisan test --browser firefox`
- Or chain per-test: `visit('/')->on()->firefox()`
- Install Playwright browsers: `pnpm exec playwright install`
- Add `tests/Browser/Screenshots` to `.gitignore`

## Formatting & style
- PHP: run `vendor/bin/pint --dirty --format agent` after any PHP changes
- Enums: TitleCase keys (`FavoritePerson`, not `favorite_person`)
- Constructor property promotion for simple DI; explicit return types on all methods
- PHPDoc blocks for methods, inline comments only for complex logic

## Frontend
- If Vite manifest error: run `pnpm run build` or `pnpm run dev`
- Font is "Instrument Sans" loaded via bunny CDN (configured in `vite.config.js`)
- Dark mode support is active (Tailwind `dark:` variants)

## Artisan conventions
- Always pass `--no-interaction` to Artisan commands from agents
- Use `php artisan make:` commands for generating files (controller, model, migration, etc.)
- Read config: `php artisan config:show app.name` (dot notation)

</laravel-boost-guidelines>
