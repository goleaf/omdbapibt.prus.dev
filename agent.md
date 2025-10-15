# Agent Handbook for omdbapibt.prus.dev

This document captures the house rules for contributors working inside this repository. Treat it as the single source of truth whenever you touch code, write documentation, or interact with project tooling.

---

## 1. Project Synopsis

- **Application type:** Laravel 12 monolith with a Livewire 3 frontend that powers an OMDb/TMDb-backed media catalog and subscription platform.
- **Primary runtime:** PHP 8.3.x (match `.tool-versions` or Docker image), Node 20+ for Vite/Tailwind.
- **Key packages:** `laravel/cashier`, `laravel/horizon`, `livewire/livewire`, `livewire/flux`, `spatie/laravel-translatable`, `barryvdh/laravel-debugbar`, `ezyang/htmlpurifier`.
- **Build tooling:** Vite 7 with Tailwind CSS 4 (via `@tailwindcss/vite`), npm scripts in `package.json`.
- **Testing stack:** PHPUnit 11, Laravel test runner (`php artisan test`).

Always check `composer.json` and `package.json` after pulling to spot new dependencies or scripts before you begin work.

---

## 2. Environment & Setup

1. **Install PHP dependencies**
   ```bash
   composer install
   ```
2. **Install Node dependencies**
   ```bash
   npm install
   ```
3. **Create environment file**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. **Configure database**
   - Update the database credentials in `.env`.
   - Run migrations with `php artisan migrate` (use `--force` in CI scripts only).
   - Seeders live in `database/seeders`; run `php artisan db:seed` as needed.
5. **Local development**
   - Preferred: run `composer run dev` (spins up `php artisan serve`, queue listener, Pail logs, Vite dev server).
   - Alternative: run services individually (queue workers via `php artisan queue:listen --tries=1`).
6. **Build assets**
   - Production build: `npm run build`.
   - Development: `npm run dev`.
7. **Queues & Horizon**
   - Horizon config in `config/horizon.php`.
   - For local queue processing, use `php artisan horizon` or the queue listener from the `dev` script.

Keep the `.env` file out of version control. Document new environment variables inside `README.md` if they impact project setup.

SQLite-specific setup notes now live in [`docs/sqlite.md`](docs/sqlite.md).

---

## 3. Directory Orientation

- `app/`
  - `App\Models` – Eloquent models (use factories in `database/factories`).
  - `App\Http` – Controllers, middleware, requests; prefer Form Request validation and keep controllers slim.
  - `App\Http\Responses` – House dedicated response classes; create or extend these instead of returning raw arrays in controllers.
  - `App\Livewire` – Livewire 3 components. Keep components lean; push data work into services/models.
  - `App\Services` – Place complex domain logic (API clients, data processors). Create the directory if absent but keep naming consistent.
- `bootstrap/` – Laravel bootstrap cache and app initialization files.
- `config/` – Configuration. When adding options, update the relevant config file and document defaults in the comments.
- `database/`
  - `migrations/` – Database schema changes. Follow timestamped naming conventions.
  - `seeders/`, `factories/` – Populate sample data using faker types consistent with domain.
- `public/` – Front-facing assets; do not manually modify `build/` output.
- `resources/`
  - `views/` – Blade templates; Livewire views live alongside components under `resources/views/livewire`.
  - `js/` – JavaScript entry points for Vite (ES modules).
  - `css/` – Tailwind entry file(s); configure theme via `tailwind.config.js` or the Tailwind 4 utilities API.
  - `lang/` – Localization files; integrate with `spatie/laravel-translatable` for content translations.
- `routes/` – HTTP routes (`web.php`, `api.php`). Group routes, apply middleware and route names consistently.
- `tests/`
  - `Feature/` – HTTP/Livewire/API level tests. Controllers and Form Requests must be covered here.
  - `Unit/` – Model/service tests.
- `scripts/` – Custom automation; ensure shell scripts are executable.

When introducing new code, align with existing organization. If unsure, inspect neighboring files before adding to maintain coherence.

---

## 4. Coding Standards

### 4.1 PHP

- **Type Safety:** Use scalar/return type hints everywhere. Nullable parameters must be declared explicitly (e.g., `?int`).
- **Constructor promotion:** Prefer property promotion for dependencies. Avoid empty constructors.
- **Control structures:** Always use braces, even for single statements.
- **Formatting:** Run `vendor/bin/pint --dirty` before committing. If Pint reports changes, re-run until clean.
- **Naming:** Use descriptive names (e.g., `loadPopularMovies`, `syncTmdbMetadata`). Method names should be verbs; boolean getters should start with `is`, `has`, or `can`.
- **Requests/Validation:** Encapsulate validation inside Form Request classes. Controllers must type-hint these Form Requests and avoid inline `validate()` calls.
- **Responses:** Prefer dedicated response classes under `App\Http\Responses` for non-trivial payloads. Keep serialization logic out of controllers.
- **Policies/Gates:** Enforce authorization using Laravel policies; store in `app/Policies`.
- **Services:** Centralize API calls or complex business logic under `App\Services`. Inject services into Livewire components/controllers using interfaces when practical.
- **Error handling:** Throw domain-specific exceptions; convert to HTTP responses using the exception handler when necessary.
- **Translations:** Use translation keys instead of hard-coded strings in views. For translatable Eloquent attributes, keep JSON columns synchronized. Ensure new user-facing strings support multiple locales.

### 4.2 Livewire & Frontend

- Use Livewire v3 conventions (`wire:model.live`, `$this->dispatch`).
- Each component view requires a single root element. Add `wire:key` when looping.
- Manage state on the server; avoid storing canonical data purely in Alpine/JS.
- Loading states: use `wire:loading`, `wire:target`, and `wire:dirty` for UX polish.
- Events: rely on `$this->dispatch('event-name')`; document event payloads in the component class.
- For Tailwind 4, define design tokens via `tailwind.config.js` or using the `@theme` directive in CSS entry files.
- Bundle third-party libraries via ES modules; avoid CDN scripts in Blade templates.
- Keep `resources/js/app.js` as the main entrypoint; register additional pages/components there if necessary.

### 4.3 JavaScript & CSS

- Adhere to ESNext module syntax (import/export). Avoid `require`.
- Prefer `const`/`let`; avoid `var`.
- Keep logic minimal in Blade inline scripts; place JS modules under `resources/js`.
- Tailwind-first styling. If you must write custom CSS, colocate with the component and use BEM-inspired naming to prevent collisions.

---

## 5. Database Guidelines

- Use Laravel migrations with descriptive names (`2024_01_01_000001_create_movies_table`).
- Always include `down()` implementations that fully reverse `up()` actions.
- Add indexes for lookup columns (IDs, slugs, foreign keys) using `->index()` or explicit index names.
- Manage pivot tables with composite unique keys where applicable to prevent duplicates.
- Keep seeders idempotent; wrap data creation in `updateOrCreate` when re-runnable.
- If altering production-critical tables, include a comment section in the migration docstring describing the change rationale.

---

## 6. External Integrations

- **OMDb/TMDb:** Store API keys in `.env`. Respect rate limits; implement caching via Laravel Cache (Redis) for read-heavy endpoints.
- **Stripe (Cashier):** Sync webhook events; after updating subscription logic, run targeted feature tests covering `Cashier` flows.
- **Horizon/Queues:** Place parsing jobs on dedicated queues (`parsing`, `emails`, etc.). Configure queue names in job classes via `public $queue`.
- **Localization:** When storing translations via `spatie/laravel-translatable`, ensure fallback locales are set in `config/translatable.php`.

Document any new integration steps in `README.md` so other contributors can reproduce them.

---

## 7. Testing Expectations

- Every change must have test coverage (new test or updated existing test). No untested changes.
- Controllers and Form Requests require dedicated Feature tests covering happy-path and validation failure scenarios.
- Scope tests appropriately:
  - Feature tests for HTTP/Livewire flows under `tests/Feature`.
  - Unit tests for service-layer logic under `tests/Unit`.
- Use factories (`database/factories`) for test data. Seeders should not be called from tests unless absolutely necessary.
- Prefer `RefreshDatabase` or `DatabaseTransactions` traits for isolation.
- Run the smallest relevant test suite:
  - `php artisan test tests/Feature/YourFeatureTest.php`
  - `php artisan test --filter=YourTestMethod`
- Coordinate with CI expectations; if you modify test helpers or base classes, run the full suite (`php artisan test`).

Record command outputs in your work log when communicating with stakeholders.

---

## 8. Code Review & Git Hygiene

- **Branching:** Create feature branches from `main` (or the active default branch). Keep branches small and focused.
- **Commits:** Write descriptive commit messages (`feat: add TMDb sync for TV shows`). One logical change per commit.
- **Formatting:** Ensure Pint and ESLint-equivalent checks pass before committing.
- **Diff audit:** Review `git diff` prior to commit; avoid accidental `.env` or compiled asset changes.
- **Pull requests:** Summaries should state the problem, solution, and tests executed. Reference relevant tickets/issues.
- **Rebasing:** Rebase onto the latest `main` before opening a PR to keep history linear.

---

## 9. Documentation Rules

- Only create or update documentation when explicitly asked or when introducing new developer-facing behavior that needs explanation.
- Store high-level guides in `docs/`. Update `README.md` for setup-related changes.
- Use Markdown with proper headings, fenced code blocks, and tables where clarity improves.

---

## 10. Quality Assurance Checklist

Before you finish any task:

1. Ensure code follows the conventions in this document.
2. Run Pint (`vendor/bin/pint --dirty`).
3. Execute the relevant PHPUnit tests.
4. For frontend changes, run `npm run build` (or at least `npm run dev` locally) to catch build regressions.
5. Update translations when adding user-facing copy.
6. Provide screenshots for UI modifications (use the provided browser tooling).
7. Document environment or migration steps in the PR description.

Following this checklist keeps the project stable and makes review painless.

---

## 11. Support & Troubleshooting

- Use Laravel Telescope/Debugbar locally for request debugging (ensure it remains disabled in production).
- Inspect Horizon dashboard for queue health.
- Check storage logs (`storage/logs/laravel.log`) for runtime errors.
- Run `php artisan config:clear`, `cache:clear`, `view:clear` if you encounter stale configuration issues during development.
- When debugging Livewire, leverage `Livewire::test()` in PHPUnit and browser devtools for emitted network requests.

Keep this document up to date. If you discover a new convention or pitfall, add it here so the next agent benefits.
